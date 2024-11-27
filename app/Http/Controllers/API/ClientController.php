<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpinionRequest;
use App\Http\Requests\ProblemRequest;
use App\Http\Requests\SuggestRequest;
use App\Http\Resources\ActivitiesResource;
use Exception;
use App\Models\Problem;
use App\Models\Suggest;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\OrganizationResource;
use App\Http\Requests\TrafficRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Models\Comment;
use App\Models\Opinion;
use App\Models\Traffic;
use App\Models\TypeProblem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Resources\OnlyOrganizationResource;
use App\Models\Activities;

class ClientController extends Controller
{
    public function addProblem(ProblemRequest $req){
                try{
                    $pro=new Problem();
                    $pro->date=$req->date;
                    $pro->number=$req->number;
                    $pro->fullName=$req->fullName;
                    $pro->phone=$req->phone;
                    $pro->email=$req->email;
                    $pro->address=$req->address;
                    $pro->text=$req->text;
                    $pro->benifit=$req->benifit;
                    $pro->problemDate=$req->problemDate;
                    $pro->isPrevious=$req->isPrevious;
                    if($req->project_id){
                        $pro->project_id=$req->project_id;
                    } else
                        $pro->organization_id=$req->organization_id;
                    $pro->save();
                    $newTypeProblem=[];
                    if($req->typeProblems){
                        foreach($req->typeProblems as $t)
                           array_push($newTypeProblem,new TypeProblem(['type'=>$t['type']]));
                        $pro->typeProblems()->saveMany($newTypeProblem);
                    }
                    return response()->json(['message'=>'add success'],201);
                }catch(Exception $err){
                    return response()->json(['message'=>$err->getMessage()],422);
                }
    }
    public function addSuggest(SuggestRequest $req){
        try{
            $sug=new Suggest();
            $sug->text=$req->text;
            $sug->email=$req->email;
            $sug->fullName=$req->fullName;
            $sug->phone=$req->phone;
            $sug->address=$req->address;
            if($req->project_id){
                $sug->project_id=$req->project_id;
            } else
                $sug->organization_id=$req->organization_id;
            $sug->save();
            return response()->json(['message'=>'add success'],201);
        }catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function getProjects(Request $req){
        try {
            $perPage=$req->per_page??10;
            $res=Project::paginate($perPage);
            if(sizeof($res)>0)
                $res=ProjectResource::collection($res);
            return response()->json($res,201);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function getOrganizations(Request $req){
        try {
            $perPage=$req->per_page??10;
            $res=User::where('role','org')->paginate($perPage);
            if(sizeof($res)>0)
                $res=OrganizationResource::collection($res);
            return response()->json($res,200);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function addComment(ProblemRequest $req,string $proId){
           try{
                if(!preg_match("/^[0-9]+$/", $proId))
                       return throw ValidationException::withMessages(['validation error']);
                $newComment=new Comment();
                $newComment->text=$req->text;
                $newComment->name=$req->name;
                $pro=Project::find($proId);
                if(!$pro)
                    return response()->json(['message'=>'project not found'],404);        
                $pro->comments()->save($newComment);
                return response()->json(['message'=>'comment sent success'],200);
            } catch(Exception $err){
                 return response()->json(['message'=>$err->getMessage()],422);
            }
    }
    public function addRate(Request $req,string $proId){
                  try{
                    if(!preg_match("/^[0-9]+$/", $proId))
                           return throw ValidationException::withMessages(['validation err']);
                    if(!preg_match("/^[0-5]{1}(.[0-9]{1,2})?$/", $req->rate))
                           return throw ValidationException::withMessages(['rating value err , must be [0 -> 5]']);
                    $pro=Project::find($proId);
                    if(!$pro)
                        return response()->json(['message'=>'project not found'],404);  
                    $pro->rate=(floatVal($pro->rate)+floatVal($req->rate))/2.0;
                    $pro->allWhoRate++;
                    $pro->save();
                    return response()->json(['message'=>'rating success'],200);
                  }catch(Exception $err){
                      return response()->json(['message'=>$err->getMessage()],422);
                  }
    }
    public function addRateToAct(Request $req,string $actId){
                  try{
                    if(!preg_match("/^[0-9]+$/", $actId))
                           return throw ValidationException::withMessages(['validation err']);
                    if(!preg_match("/^[0-5]{1}(.[0-9]{1,2})?$/", $req->rate))
                           return throw ValidationException::withMessages(['rating value err , must be [0 -> 5]']);
                    $pro=Activities::find($actId);
                    if(!$pro)
                        return response()->json(['message'=>'activity not found'],404);  
                    $pro->rate=(floatVal($pro->rate)+floatVal($req->rate))/2.0;
                    $pro->allWhoRate++;
                    $pro->save();
                    return response()->json(['message'=>'rating success'],200);
                  }catch(Exception $err){
                      return response()->json(['message'=>$err->getMessage()],422);
                  }
    }
    public function downloadPDF(string $proId){
              try{
                if(!preg_match("/^[0-9]+$/", $proId))
                       return throw ValidationException::withMessages(['validation error']);
                $pro=Project::find($proId);
                if(!$pro)
                    return response()->json(['message'=>'project not found'],404);
                $pdfUrl="/images/".explode("/images/",$pro->pdfURL)[1];
                //PDF file is stored under project/public/download/info.pdf
                $file= public_path(). $pdfUrl;
                $headers = array(
                          'Content-Type: application/pdf',
                        );
                return Response::download($file, $pro->name.$pro->id.'.pdf', $headers);
              }catch(Exception $err){
                  return response()->json(['message'=>$err->getMessage()],422);
              }
    }
    public function addOpinion(OpinionRequest $req,string $proId){
        try{
            if(!preg_match("/^[0-9]+$/", $proId))
                return throw ValidationException::withMessages(['validation error']);
            $pro=Project::find($proId);
            if(!$pro)
                return response()->json(['message'=>'project not found'],404);
            $isHere=Opinion::where('mac',$req->mac)->get();
            if(sizeof($isHere)>0)
                return response()->json(['message'=>'opinion was added already'],422);   
            $opinion=new Opinion();
            $opinion->name=$req->name;
            $opinion->mac=$req->mac;
            $opinion->q1=$req->q1;
            $opinion->q2=$req->q2;
            $opinion->q3=$req->q3;
            $opinion->q4=$req->q4;
            $opinion->q5=$req->q5;
            $opinion->q6=$req->q6;
            $opinion->q7=$req->q7;
            $opinion->q8=$req->q8;
            $opinion->q9=$req->q9;
            $opinion->q10=$req->q10;
            $opinion->q11=$req->q11;
            $opinion->q12=$req->q12;
            $opinion->q13=$req->q13;
            $opinion->q14=$req->q14;
            $opinion->q15=$req->q15;
            $opinion->q16=$req->q16;
            $opinion->q17=$req->q17;
            $opinion->q18=$req->q18;
            $opinion->q19=$req->q19;
            $opinion->q20=$req->q20;
            $pro->opinions()->save($opinion);
            return response()->json(['message'=>'opinion added success'],201);
          }catch(Exception $err){
              return response()->json(['message'=>$err->getMessage()],422);
          }
    }
    public function getOnlyOrganizations(Request $req){
        try {
            $perPage=$req->per_page??10;
            $res=User::where('role','org')->paginate($perPage);
            if(sizeof($res)>0)
                $res=OnlyOrganizationResource::collection($res);
            return response()->json($res,200);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function getOnlyProjects(string $orgId){
        try {
            if(!preg_match("/^[0-9]+$/", $orgId))
                   return throw ValidationException::withMessages(['validation error']);
            $org=User::find($orgId);
            if(!$org)
                return response()->json(['message'=>'this organization not found'],404);
            $res=$org->organization->projects;
            if(sizeof($res)>0){
                $res=ProjectResource::collection($res);
                for($i=0;$i<sizeof($res);$i++)
                       unset($res[$i]['activities']);
            }
            return response()->json($res,201);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function getOnlyActivities(string $proId){
        try {
            if(!preg_match("/^[0-9]+$/", $proId))
                   return throw ValidationException::withMessages(['validation error']);
            $res=Project::find($proId);
            if(!$res)
               return response()->json(['message'=>'this project not found'],404);
            $acts=$res->activities;
            return response()->json(['activites'=>ActivitiesResource::collection($acts)],201);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function addCustomerToTraffic(TrafficRequest  $req){
        try{
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
         $newTraffic->save();
         return response()->json(['message'=>'added success'],201);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage(),422]);
        }
    }  
}