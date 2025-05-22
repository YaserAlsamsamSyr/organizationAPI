<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\ProjectRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\Image;
use App\Models\Project;
use Illuminate\Support\Facades\File;
use App\Http\Requests\OrganizationRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Detail;
use App\Models\Number;
use App\Models\Skil;
use App\Models\Social;
use App\Models\Summary;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function createPro(ProjectRequest $req){
        $proId=-1;
        $imgss=[];
        try{
            if(auth()->user()->role!=="org")
                return throw ValidationException::withMessages(['not authorized']);
            $pro=new Project();
            $pro->name=$req->name;
            $pro->address=$req->address;
            // upload one image
            if($req->hasfile('logo')) {  
                $file=$req->file('logo');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/projects/logo'),$name);
                //
                array_push($imgss,'/images/projects/logo/'.$name);
                //
                $pro->logo=asset('/images/projects/logo/'.$name);
            }
            //
            $pro->start_At=$req->start_At;
            $pro->end_At=$req->end_At;
            $pro->benefitDir=$req->benefitDir;
            $pro->benefitUnd=$req->benefitUnd;
            // upload one pdf
            if($req->hasfile('pdfURL')) {  
                $file=$req->file('pdfURL');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/projects/pdfs'),$name);
                //
                array_push($imgss,'/images/projects/pdfs/'.$name);
                //
                $pro->pdfURL=asset('/images/projects/pdfs/'.$name);
            }
            //
            if($req->videoURL!==null)
                 $pro->videoURL=$req->videoURL;
            // upload one image
            if($req->hasfile('videoLogo')) {  
                $file=$req->file('videoLogo');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/projects/logo'),$name);
                //
                array_push($imgss,'/images/projects/logo/'.$name);
                //
                $pro->videoLogo=asset('/images/projects/logo/'.$name);
            }
            //
            // upload multi image
            $imgs=[];
            if($req->hasfile('images')) {
               foreach($req->file('images') as $file) {
                   $name = uniqid().'.'.$file->getClientOriginalExtension();
                   $file->move(public_path('/images/projects/imgs'),$name);
                   //
                   array_push($imgss,'/images/projects/imgs/'.$name);
                   //
                   array_push($imgs,new Image(['url'=>asset('/images/projects/imgs/'.$name)]));
               }
            }
            //
            $pro=auth()->user()->organization->projects()->save($pro);
            $proId=$pro->id;
            $summaries=[];
            if($req->summaries){
                foreach($req->summaries as $sam) 
                   array_push($summaries,new Summary(['text'=>$sam['text'],'type'=>$sam['type']]));
                $pro->summaries()->saveMany($summaries);
            }
            if(sizeof($imgs)!==0)
                $pro->images()->saveMany($imgs);
            return response()->json(['message'=>'added success','proId'=>$pro->id],201);
        } catch(Exception $err){
                if($proId>-1){
                     try{
                        $pro=Project::find($proId);
                        $pro->delete();
                     } catch(Exception $err){
                        $pro=Project::find($proId);
                        $pro->delete();
                     }
                }
                try{
                    if(sizeof($imgss)>0){
                       foreach($imgss as $i){
                           if(File::exists(public_path().$i)) {
                               File::delete(public_path().$i);
                           }
                       }
                    }
                }catch(Exception $err){
                   if(sizeof($imgss)>0){
                       foreach($imgss as $i){
                           if(File::exists(public_path().$i)) {
                               File::delete(public_path().$i);
                           }
                       }
                    }
                }
               return response()->json(['message'=>$err->getMessage(),422]);
        }
    }
    public function deletePro(string $proId){
        try{
            if(auth()->user()->role!=="org")
                return throw ValidationException::withMessages(['not authorized']);
            if(!preg_match("/^[0-9]+$/", $proId))
                return throw ValidationException::withMessages(['validation err']);
            $pro=auth()->user()->organization->projects()->find($proId);
            if(!$pro)
                 return response()->json(['message'=>'this project not found'],404);
            //delete all image and pdf
            if($pro->logo!="no logo"){
                $n=explode("/images/",$pro->logo)[1];
                if(File::exists(public_path().'/images/'.$n)) {
                    File::delete(public_path().'/images/'.$n);
                }
            }
            if($pro->pdfURL!="no pdf"){
                $n=explode("/images/",$pro->pdfURL)[1];
                if(File::exists(public_path().'/images/'.$n)) {
                    File::delete(public_path().'/images/'.$n);
                }
            }
            if($pro->videoLogo!="no image"){
                $n=explode("/images/",$pro->videoLogo)[1];
                if(File::exists(public_path().'/images/'.$n)) {
                    File::delete(public_path().'/images/'.$n);
                }
            }
            $imggs=$pro->images;
            for($i=0;$i<sizeof($imggs);$i++){
                $n=explode("/images/",$imggs[$i]->url)[1];
                if(File::exists(public_path().'/images/'.$n)) {
                    File::delete(public_path().'/images/'.$n);
                }
            }
            // new
            $activities=$pro->activities;
            for($j=0;$j<sizeof($activities);$j++){
                for($i=0;$i<sizeof($activities[$j]->images);$i++){
                    $n=explode("/images/",$activities[$j]->images[$i]->url)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
                if($activities[$j]->videoImg!=="no image"){
                    $n=explode("/images/",$activities[$j]->videoImg)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
                if($activities[$j]->pdf!=="no pdf"){
                    $n=explode("/images/",$activities[$j]->pdf)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
            }
            //
            if(!$pro->delete())
                return throw ValidationException::withMessages(['delete error']);
            return response()->json(["message"=>"delete success"],200);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function updatePro(ProjectRequest $req,string $proId){
        $images=[];
        try{
            if(auth()->user()->role!="org")
               return response()->json(['message'=>"not authorized"]);
            if(!preg_match("/^[0-9]+$/", $proId))
               return throw ValidationException::withMessages(['validation err']);
            $pro=auth()->user()->organization->projects()->find($proId);
            if(!$pro)
                return response()->json(['message'=>'this project not found'],404);
            $pro->name=$req->name;
            $pro->address=$req->address;
            // upload one image
            if($req->hasfile('logo')) {  
                $file=$req->file('logo');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/projects/logo'),$name);
                array_push($images,'/images/projects/logo/'.$name);
                //delete old logo
                if($pro->logo!="no logo"){
                    $n=explode("/images/",$pro->logo)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
                //
                $pro->logo=asset('/images/projects/logo/'.$name);
            }
            //
            $pro->start_At=$req->start_At;
            $pro->end_At=$req->end_At;
            $pro->benefitDir=$req->benefitDir;
            $pro->benefitUnd=$req->benefitUnd;
            if($req->rate!==null)
                 $pro->rate=$req->rate;
            // upload one pdf
            if($req->hasfile('pdfURL')) {  
                $file=$req->file('pdfURL');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/projects/pdfs'),$name);
                array_push($images,'/images/projects/pdfs/'.$name);
                //delete old pdf
                if($pro->pdfURL!="no pdf"){
                    $n=explode("/images/",$pro->pdfURL)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
                //
                $pro->pdfURL=asset('/images/projects/pdfs/'.$name);
            }
            //
            if($req->videoURL!==null)
                 $pro->videoURL=$req->videoURL;
            // upload one image
            if($req->hasfile('videoLogo')) {  
                $file=$req->file('videoLogo');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/projects/logo'),$name);
                array_push($images,'/images/projects/logo/'.$name);
                //delete old logo
                if($pro->videoLogo){
                    $n=explode("/images/",$pro->videoLogo)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
                //
                $pro->videoLogo=asset('/images/projects/logo/'.$name);
            }
            // upload multi image
            $imgs=[];
            if($req->hasfile('images')) {
               foreach($req->file('images') as $file) {
                   $name = uniqid().'.'.$file->getClientOriginalExtension();
                   $file->move(public_path('/images/projects/imgs'),$name);
                   array_push($images,'/images/projects/imgs/'.$name);
                   array_push($imgs,new Image(['url'=>asset('/images/projects/imgs/'.$name)]));
               }
            }
            //
            $pro->save();
            $summaries=[];
            if($req->summaries){
                foreach($req->summaries as $sam) 
                   array_push($summaries,new Summary(['text'=>$sam['text'],'type'=>$sam['type']]));
                if(sizeof($pro->summaries()->get())>0)
                    $pro->summaries()->delete();
                $pro->summaries()->saveMany($summaries);
            }
            if(sizeof($imgs)!==0){
                //delete old images
                for($i=0;$i<sizeof($pro->images);$i++){
                    $n=explode("/images/",$pro->images[$i]->url)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
                if(sizeof($pro->images()->get())>0)
                    $pro->images()->delete();
                //
                $pro->images()->saveMany($imgs);
            }
            return response()->json(['message'=>'update success'],200);
        }catch(Exception $err){
            foreach($images as $i){
                if(File::exists(public_path().$i)) {
                    File::delete(public_path().$i);
                }
            }
            return response()->json(['message'=>$err->getMessage(),422]);
        }
    }
    public function updateMyProfile(OrganizationRequest $req){
        $images=[];
        try{
            if(auth()->user()->role!="org")
               return response()->json(['message'=>"not authorized"]);
            $org=auth()->user();
            $org->name=$req->name;
            if($req->password!=null)
                $org->password=Hash::make($req->string('password'));
            $org->save();
            $org->organization->experience=$req->experience;
            // upload one image
            if($req->hasfile('logo')) {  
                $file=$req->file('logo');
                $name = uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('/images/organizations/logo'),$name);
                array_push($images,'/images/organizations/logo/'.$name);
                //delete old logo
                if($org->logo!="no logo"){
                    $n=explode("/images/",$org->organization->logo)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
                //
                $org->organization->logo=asset('/images/organizations/logo/'.$name);
            }
            //
            // upload multi image
            $imgs=[];
            if($req->hasfile('images')) {
               foreach($req->file('images') as $file) {
                   $name = uniqid().'.'.$file->getClientOriginalExtension();
                   $file->move(public_path('/images/organizations/imgs'),$name);
                   array_push($images,'/images/organizations/imgs/'.$name);
                   array_push($imgs,new Image(['url'=>asset('/images/organizations/imgs/'.$name)]));
               }
            }
            //
            $org->organization->view=$req->view;
            $org->organization->message=$req->message;
            $org->organization->address=$req->address;
            $org->organization->phone=$req->phone;
            $org->organization->save();
            ////////////////
            $data=[];
            if($req->details){
                foreach($req->details as $det) 
                   array_push($data,new Detail(['text'=>$det['text']]));
                if(sizeof($org->organization->details()->get())>0)
                    $org->organization->details()->delete();
                $org->organization->details()->saveMany($data);
                $data=[];
            }
            if($req->skils){
                foreach($req->skils as $skil) 
                   array_push($data,new Skil(['text'=>$skil['text']]));  
                if(sizeof($org->organization->skils()->get())>0)
                    $org->organization->skils()->delete();      
                $org->organization->skils()->saveMany($data);
                $data=[];
            }
            if($req->number){
                foreach($req->number as $number) 
                   array_push($data,new Number(['type'=>$number['type'],'number'=>$number['number']]));
                if(sizeof($org->organization->numbers()->get())>0)
                    $org->organization->numbers()->delete();
                $org->organization->numbers()->saveMany($data);    
                $data=[];
            }
            if($req->socials){
                foreach($req->socials as $socials) 
                   array_push($data,new Social(['type'=>$socials['type'],'url'=>$socials['url']]));
                if(sizeof($org->organization->numbers()->get())>0)
                    $org->organization->numbers()->delete();
                $org->organization->socials()->saveMany($data);    
            }
            ////////////////
            if(sizeof($imgs)!==0){
                //delete old images
                for($i=0;$i<sizeof($org->organization->images);$i++){
                    $n=explode("/images/",$org->organization->images[$i]->url)[1];
                    if(File::exists(public_path().'/images/'.$n)) {
                        File::delete(public_path().'/images/'.$n);
                    }
                }
                if(sizeof($org->organization->images()->get())>0)
                    $org->organization->images()->delete();
                //
                $org->organization->images()->saveMany($imgs);
            }
            return response()->json(['message'=>'update success'],200);
        }catch(Exception $err){
            foreach($images as $i){
                if(File::exists(public_path().$i)) {
                    File::delete(public_path().$i);
                }
            }
            return response()->json(['message'=>$err->getMessage(),422]);
        }
    }
    public function getProjects(Request $req){
        try{  
            if(auth()->user()->role!="org")
               return response()->json('not authorized',422);
            $numItems=$req->per_page??10;
            $pros=auth()->user()->organization->projects()->paginate($numItems);
            return response()->json(['projects'=>ProjectResource::collection($pros)],200);
        } catch(Exception $err){
                return response()->json(['message'=>$err->getMessage()],422);
        } 
    }
    public function getProject(String $proId){
        try{
            if(auth()->user()->role!="org")
               return response()->json('not authorized',422);
            $pro=auth()->user()->organization->projects()->find($proId);
            if(!$pro)
                return response()->json(['message'=>'this project not found'],404);
            $pro=new ProjectResource($pro);
            return response()->json(['project'=>$pro],200);
        } catch(Exception $err){
                return response()->json(['message'=>$err->getMessage()],422);
        }
    }
}
