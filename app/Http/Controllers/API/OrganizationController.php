<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\ProjectRequest;
use App\Models\Image;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class OrganizationController extends Controller
{
    public function createPro(ProjectRequest $req){
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
            $pro=auth()->user()->organization->projects()->save($pro);
            if(sizeof($imgs)!==0)
                $pro->images()->saveMany($imgs);
            return response()->json(['message'=>'added success'],201);
        } catch(Exception $err){
               return response()->json(['message'=>$err->getMessage(),422]);
        }
    }

    public function deletePro(string $proId){
        try{
            if(auth()->user()->role!=="org")
                return throw ValidationException::withMessages(['not authorized']);
            if(!preg_match("/^[0-9]+$/", $proId))
                return throw ValidationException::withMessages(['validation err']);
            $pro=auth()->user()->organization->projects()->findOrFail($proId);
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
    
    public function updatePro(ProjectRequest $req,string $proId){
        try{
            if(auth()->user()->role!="org")
               return response()->json(['message'=>"not authorized"]);
            if(!preg_match("/^[0-9]+$/", $proId))
               return throw ValidationException::withMessages(['validation err']);
            $pro=auth()->user()->organization->projects()->findOrFail($proId);
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
}
