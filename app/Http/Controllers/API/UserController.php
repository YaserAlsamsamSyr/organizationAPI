<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Http\Requests\OrganizationRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\ProjectRequest;
use App\Http\Requests\TrafficRequest;
use App\Models\Image;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Suggest;
use App\Http\Resources\SuggestResource;
use App\Http\Resources\ProblemResource;
use App\Models\Detail;
use App\Models\Number;
use App\Models\Problem;
use App\Models\Skil;
use App\Models\Social;
use App\Models\Traffic;

class UserController extends Controller
{
    public function registerNewMasterAdmin(Request $req){
        try{  
            $req->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $user = User::create([
               'name' => $req->name,
               'email' => $req->email,
               'password' => Hash::make($req->string('password')),
               'role'=>'admin'
            ]);
            if(!auth()->attempt(['email' => $req->email, 'password' => $req->password])){
                return response()->json(['error'=>'try agen'],422);
            }
            $token=auth()->user()->createToken('admin',expiresAt:now()->addDays(4),abilities:['admin'])->plainTextToken;
            $data=new UserResource($user);
            return response()->json(['token'=>$token,'response'=>$data],201);
        }catch(Exception $err){
                return response()->json(['message'=>$err->getMessage()],422);
          }
    }
    public function createOrg(OrganizationRequest $req){
            try{
                if(auth()->user()->role!=="admin")
                    return throw ValidationException::withMessages(['not authorized']);
                $newOrg=User::create([
                    'name'=>$req->name,
                    'email'=>$req->email,
                    'password'=>Hash::make($req->string('password')),
                    'admin_id'=>auth()->user()->id
                ]);
                $org=new Organization();
                $org->experience=$req->experience;
                // upload one image
                if($req->hasfile('logo')) {  
                    $file=$req->file('logo');
                    $name = uniqid().'.'.$file->getClientOriginalExtension();
                    $file->move(public_path('/images/organizations/logo'),$name);
                    $org->logo=asset('/images/organizations/logo/'.$name);
                }
                // upload multi image
                $imgs=[];
                if($req->hasfile('images')) {
                    $validator=Validator::make($req->all(), [
                        "images"    => ["required",'array',"min:1"],
                        "images.*"  => ['required','image','mimes:jpeg,jpg,png,gif'],
                    ]);
                    if ($validator->fails())
                        return throw ValidationException::withMessages([$validator->messages()->first()]);
                   foreach($req->file('images') as $file) {
                       $name = uniqid().'.'.$file->getClientOriginalExtension();
                       $file->move(public_path('/images/organizations/imgs'),$name);
                       array_push($imgs,new Image(['url'=>asset('/images/organizations/imgs/'.$name)]));
                   }
    
                }
                //
                $org->view=$req->view;
                $org->message=$req->message;
                $org->address=$req->address;
                $org->phone=$req->phone;
                $data=[];
                $newOrg->organization()->save($org);
                if($req->details){
                    foreach($req->details as $det) 
                       array_push($data,new Detail(['text'=>$det['text']]));
                    $newOrg->organization->details()->saveMany($data);
                    $data=[];
                }
                if($req->skils){
                    foreach($req->skils as $skil) 
                       array_push($data,new Skil(['text'=>$skil['text']]));        
                    $newOrg->organization->skils()->saveMany($data);
                    $data=[];
                }
                if($req->number){
                    foreach($req->number as $number) 
                       array_push($data,new Number(['type'=>$number['type'],'number'=>$number['number']]));
                    $newOrg->organization->numbers()->saveMany($data);    
                    $data=[];
                }
                if($req->socials){
                    foreach($req->socials as $socials) 
                       array_push($data,new Social(['type'=>$socials['type'],'url'=>$socials['url']]));
                    $newOrg->organization->socials()->saveMany($data);    
                }
                if(sizeof($imgs)!==0)
                   $newOrg->organization->images()->saveMany($imgs);
                return response()->json(['message'=>'added success'],201);
            }catch(Exception $err){
                   return response()->json(['message'=>$err->getMessage(),422]);
            }
    }
    public function deleteOrg(string $id){
        try{
            if(auth()->user()->role!=="admin")
                return throw ValidationException::withMessages(['not authorized']);
            if(!preg_match("/^[0-9]+$/", $id))
                return throw ValidationException::withMessages(['validation err']);
            $user=auth()->user()->myOrganizations()->findOrFail($id);
            //delete all image and pdf
            $n=explode("/images/",$user->organization->logo)[1];
            if(File::exists(public_path().'/images/'.$n)) {
                File::delete(public_path().'/images/'.$n);
            }
            $imggs=$user->organization->images;
            for($i=0;$i<sizeof($imggs);$i++){
                $n=explode("/images/",$imggs[$i]->url)[1];
                if(File::exists(public_path().'/images/'.$n)) {
                    File::delete(public_path().'/images/'.$n);
                }
            }
            //
            if(!$user->delete())
                return throw ValidationException::withMessages(['delete err']);
            return response()->json(["message"=>"delete success"],200);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function updateOrg(OrganizationRequest $req,string $id){
        try{
            if(auth()->user()->role!="admin")
               return response()->json(['message'=>"not authorized"]);
            if(!preg_match("/^[0-9]+$/", $id))
               return throw ValidationException::withMessages(['validation err']);
            $org=auth()->user()->myOrganizations()->findOrFail($id);
            $org->name=$req->name;
            if($req->password!=null)
                $org->password=Hash::make($req->string($req->password));
            $org->save();
            $org->organization->experience=$req->experience;
            $org->organization->details=$req->details;
            $org->organization->skils=$req->skils;
            // upload one image
            if($req->hasfile('logo')) {  
                $file=$req->file('logo');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/organizations/logo'),$name);
                //delete old logo
                $n=explode("/images/",$org->organization->logo)[1];
                if(File::exists(public_path().'/images/'.$n)) {
                    File::delete(public_path().'/images/'.$n);
                }
                //
                $org->organization->logo=asset('/images/organizations/logo/'.$name);
            }
            //
            // upload multi image
            $imgs=[];
            if($req->hasfile('images')) {
                $validator=Validator::make($req->all(), [
                    "images"    => ["required",'array',"min:1"],
                    "images.*"  => ['required','image','mimes:jpeg,jpg,png,gif'],
                ]);
                if ($validator->fails())
                    return throw ValidationException::withMessages([$validator->messages()->first()]);
               foreach($req->file('images') as $file) {
                   $name = uniqid().'.'.$file->getClientOriginalExtension();
                   $file->move(public_path('/images/organizations/imgs'),$name);
                   array_push($imgs,new Image(['url'=>asset('/images/organizations/imgs/'.$name)]));
               }
            }
            //
            $org->organization->view=$req->view;
            $org->organization->message=$req->message;
            $org->organization->number=$req->number;
            $org->organization->socials=$req->socials;
            $org->organization->address=$req->address;
            $org->organization->phone=$req->phone;
            $org->organization->complaints=$req->complaints;
            $org->organization->suggests=$req->suggests;
            $org->organization->save();
            if(sizeof($imgs)!==0){
                //delete old images
                for($i=0;$i<sizeof($org->organization->images);$i++){
                    $n=explode("/images/",$org->organization->images[$i]->url)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
                $org->organization->images()->delete();
                //
                $org->organization->images()->saveMany($imgs);
            }
            return response()->json(['message'=>'update success'],200);
        }catch(Exception $err){
            return response()->json(['message'=>$err->getMessage(),422]);
        }
    }
    public function createPro(ProjectRequest $req,string $orgId){
        try{
            if(auth()->user()->role!=="admin")
                return throw ValidationException::withMessages(['not authorized']);
            if(!preg_match("/^[0-9]+$/", $orgId))
               return throw ValidationException::withMessages(['validation err']);
            $pro=new Project();
            $pro->name=$req->name;
            $pro->address=$req->address;
            // upload one image
            if($req->hasfile('logo')) {  
                $file=$req->file('logo');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/projects/logo'),$name);
                $pro->logo=asset('/images/projects/logo/'.$name);
            }
            //
            $pro->summary=$req->summary;
            $pro->start_At=$req->start_At;
            $pro->end_At=$req->end_At;
            $pro->benefitDir=$req->benefitDir;
            $pro->benefitUnd=$req->benefitUnd;
            $pro->activities=$req->activities;
            if($req->rate!==null)
                 $pro->rate=$req->rate;
            // upload one pdf
            if($req->hasfile('pdfURL')) {  
                $file=$req->file('pdfURL');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/projects/pdfs'),$name);
                $pro->pdfURL=asset('/images/projects/pdfs/'.$name);
            }
            //
            if($req->videoURL!==null)
                 $pro->videoURL=$req->videoURL;
            // upload multi image
            $imgs=[];
            if($req->hasfile('images')) {
                $validator=Validator::make($req->all(), [
                    "images"    => ["required",'array',"min:1"],
                    "images.*"  => ['required','image','mimes:jpeg,jpg,png,gif'],
                ]);
                if ($validator->fails())
                    return throw ValidationException::withMessages([$validator->messages()->first()]);
               foreach($req->file('images') as $file) {
                   $name = uniqid().'.'.$file->getClientOriginalExtension();
                   $file->move(public_path('/images/projects/imgs'),$name);
                   array_push($imgs,new Image(['url'=>asset('/images/projects/imgs/'.$name)]));
               }
            }
            //
            $user=auth()->user()->myOrganizations()->findOrfail($orgId);
            $pro=$user->organization->projects()->save($pro);
            if(sizeof($imgs)!==0)
                $pro->images()->saveMany($imgs);
            return response()->json(['message'=>'added success'],201);
        } catch(Exception $err){
               return response()->json(['message'=>$err->getMessage(),422]);
        }
    }
    public function deletePro(string $orgId,string $proId){
        try{
            if(auth()->user()->role!=="admin")
                return throw ValidationException::withMessages(['not authorized']);
            if(!preg_match("/^[0-9]+$/", $proId))
                return throw ValidationException::withMessages(['validation err']);
            if(!preg_match("/^[0-9]+$/", $orgId))
                return throw ValidationException::withMessages(['validation err']);
            $user=auth()->user()->myOrganizations()->findOrfail($orgId);
            $pro=$user->organization->projects()->findOrFail($proId);
            //delete all image and pdf
            $n=explode("/images/",$pro->logo)[1];
            if(File::exists(public_path().'/images/'.$n)) {
                File::delete(public_path().'/images/'.$n);
            }
            $n=explode("/images/",$pro->pdfURL)[1];
            if(File::exists(public_path().'/images/'.$n)) {
                File::delete(public_path().'/images/'.$n);
            }
            $imggs=$pro->images;
            for($i=0;$i<sizeof($imggs);$i++){
                $n=explode("/images/",$imggs[$i]->url)[1];
                if(File::exists(public_path().'/images/'.$n)) {
                    File::delete(public_path().'/images/'.$n);
                }
            }
            //
            if(!$pro->delete())
                return throw ValidationException::withMessages(['delete err']);
            return response()->json(["message"=>"delete success"],200);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function updatePro(ProjectRequest $req,string $orgId,string $proId){
        try{
            if(auth()->user()->role!="admin")
               return response()->json(['message'=>"not authorized"]);
            if(!preg_match("/^[0-9]+$/", $proId))
               return throw ValidationException::withMessages(['validation err']);
            if(!preg_match("/^[0-9]+$/", $orgId))
               return throw ValidationException::withMessages(['validation err']);
            $org=auth()->user()->myOrganizations()->findOrFail($orgId);
            $pro=$org->organization->projects()->findOrFail($proId);
            $pro->name=$req->name;
            $pro->address=$req->address;
            // upload one image
            if($req->hasfile('logo')) {  
                $file=$req->file('logo');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/projects/logo'),$name);
                //delete old logo
                $n=explode("/images/",$pro->logo)[1];
                if(File::exists(public_path().'/images/'.$n)) {
                    File::delete(public_path().'/images/'.$n);
                }
                //
                $pro->logo=asset('/images/projects/logo/'.$name);
            }
            //
            $pro->summary=$req->summary;
            $pro->start_At=$req->start_At;
            $pro->end_At=$req->end_At;
            $pro->benefitDir=$req->benefitDir;
            $pro->benefitUnd=$req->benefitUnd;
            $pro->activities=$req->activities;
            if($req->rate!==null)
                 $pro->rate=$req->rate;
            // upload one pdf
            if($req->hasfile('pdfURL')) {  
                $file=$req->file('pdfURL');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/projects/pdfs'),$name);
                //delete old pdf
                $n=explode("/images/",$pro->pdfURL)[1];
                if(File::exists(public_path().'/images/'.$n)) {
                    File::delete(public_path().'/images/'.$n);
                }
                //
                $pro->pdfURL=asset('/images/projects/pdfs/'.$name);
            }
            //
            if($req->videoURL!==null)
                 $pro->videoURL=$req->videoURL;
            // upload multi image
            $imgs=[];
            if($req->hasfile('images')) {
                $validator=Validator::make($req->all(), [
                    "images"    => ["required",'array',"min:1"],
                    "images.*"  => ['required','image','mimes:jpeg,jpg,png,gif'],
                ]);
                if ($validator->fails())
                    return throw ValidationException::withMessages([$validator->messages()->first()]);
               foreach($req->file('images') as $file) {
                   $name = uniqid().'.'.$file->getClientOriginalExtension();
                   $file->move(public_path('/images/projects/imgs'),$name);
                   array_push($imgs,new Image(['url'=>asset('/images/projects/imgs/'.$name)]));
               }
            }
            //
            $pro->save();
            if(sizeof($imgs)!==0){
                //delete old images
                for($i=0;$i<sizeof($pro->images);$i++){
                    $n=explode("/images/",$pro->images[$i]->url)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
                $pro->images()->delete();
                //
                $pro->images()->saveMany($imgs);
            }
            return response()->json(['message'=>'update success'],200);
        }catch(Exception $err){
            return response()->json(['message'=>$err->getMessage(),422]);
        }
    }
    public function getSuggests(){
        try{
             $sug=SuggestResource::collection(Suggest::all());
             return response()->json($sug,200);
        }catch(Exception $err){
            return response()->json(['messsage'=>$err->getMessage(),422]);
        }   
    }
    public function deleteSuggest(string $sugId){
        try{            
            if(!preg_match("/^[0-9]+$/", $sugId))
               return throw ValidationException::withMessages(['validation err']);
            $sug=Suggest::find($sugId);
            return $sug->delete() ?
                   response()->json(['message'=>'delete success'],200) :
                   response()->json(['message'=>'delete fail'],422);
        } catch(Exception $err){
            return response()->json(['messsage'=>$err->getMessage(),422]);
        }   
    }
    public function getProblems(){
        try{
            if(auth()->user()->role!="admin")
               return response()->json(['message'=>"not authorized"]);
             $pro=ProblemResource::collection(Problem::all());
             return response()->json($pro,200);
        }catch(Exception $err){
            return response()->json(['messsage'=>$err->getMessage(),422]);
        }   
    }
    public function deleteProblem(string $proId){
        try{  
            if(auth()->user()->role!="admin")
               return response()->json(['message'=>"not authorized"]);          
            if(!preg_match("/^[0-9]+$/", $proId))
               return throw ValidationException::withMessages(['validation err']);
            $pro=Problem::find($proId);
            return $pro->delete() ?
                   response()->json(['message'=>'delete success'],200) :
                   response()->json(['message'=>'delete fail'],422);
        } catch(Exception $err){
            return response()->json(['messsage'=>$err->getMessage(),422]);
        }   
    }
    public function myProfile(){
        try{
            if(auth()->user()->role!="admin")
                 return response()->json('not authorized',422);
            $data=['id'=>auth()->user()->id,'name'=>auth()->user()->name,'email'=>auth()->user()->email];
            return response()->json($data,200);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function updateMyProfile(Request $req){
        try{  
            if(auth()->user()->role!="admin")
               return response()->json('not authorized',422);
            $req->validate([
                'name' => ['nullable', 'string', 'max:255'],
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            ]);
            
            $user=User::find(auth()->user()->id);
            if($req->name!=null)
                $user->name=$req->name;
            if($req->password!=null)
               $user->password=Hash::make($req->string('password'));
            $user->save();
            return response()->json(['message'=>'update success'],201);
        } catch(Exception $err){
                return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function addCustomerToTraffic(TrafficRequest  $req){
               try{
                if(auth()->user()->role!="admin")
                    return response()->json('not authorized',422);
                $newTraffic=new Traffic();
                $newTraffic->mac=$req->mac;
                $newTraffic->day=date('d');
                $newTraffic->month=date('m');
                $newTraffic->year=date('y');
                $isFound=Traffic::where('mac',$req->mac)->first();
                if(!$isFound)//first time
                    $newTraffic->firstTime=true;
                else
                    $newTraffic->firstTime=false;
                $newTraffic->user_id=auth()->user()->id;
                $newTraffic->save();
                return response()->json(['message'=>'added success'],201);
               } catch(Exception $err){
                   return response()->json(['message'=>$err->getMessage(),422]);
               }
    }
    public function getTraffic(){
        try{
            if(auth()->user()->role!="admin")
                return response()->json('not authorized',422);
            
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage(),422]);
        }
    }
}