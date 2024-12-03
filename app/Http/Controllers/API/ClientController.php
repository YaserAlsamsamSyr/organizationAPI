<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
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
use App\Models\Traffic;
use App\Http\Resources\CommentResource;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Models\Comment;
use App\Models\Opinion;
use App\Models\TypeProblem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Resources\OnlyOrganizationResource;
use App\Models\Activities;
use App\Models\Day;
use App\Models\Month;
use App\Models\Organization;
use App\Models\Year;

class ClientController extends Controller
{
    public function addProblem(ProblemRequest $req){
                try{
                    $pro=new Problem();
                    $pro->fullName=$req->fullName;
                    $pro->phone=$req->phone;
                    $pro->email=$req->email;
                    $pro->address=$req->address;
                    $pro->text=$req->text;
                    $pro->benifit=$req->benifit;
                    $pro->problemDate=$req->problemDate;
                    $pro->isPrevious=$req->isPrevious;
                    if($req->project_id)
                        $pro->project_id=$req->project_id;
                    if($req->organization_id)
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
    public function addComment(CommentRequest $req,string $proId){
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
         $day=date('d');
         $month=date('m');
         $year=date('y');
         $isFound=Traffic::where('mac',$req->mac)->first();
         if(!$isFound)//first time
             $newTraffic->firstTime=true;
         else
             $newTraffic->firstTime=false;
        $newTraffic->save();
        $isYear=Year::where('year',$year)->first();
        if(!$isYear){
            $isYear=Year::create(['year'=>$year]);
        }
        $isMonth=Month::where('month',$month)->first();
        if(!$isMonth){
            $isMonth=Month::create(['month'=>$month]);
        }
        if(!$isYear->months()->where('month',$month)->first())
                $isMonth->years()->attach($isYear); 
              
        $isday=Day::where('day',$day)->first();
        if(!$isday){
            $isday=Day::create(['day'=>$day]);
        }
        if(!$isMonth->days()->where('day',$day)->first())
                $isMonth->days()->attach($isday); 
        $newTraffic->days()->attach($isday);
         return response()->json(['message'=>'added success'],201);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage(),422]);
        }
    }  
    // new
    public function getProjectComments(string $proId){
        try {
            if(!preg_match("/^[0-9]+$/", $proId))
                   return throw ValidationException::withMessages(['validation error']);
            $res=Project::find($proId);
            if(!$res)
                return response()->json(['message','this project not found'],404);
            $res=CommentResource::collection($res->comments);
            return response()->json($res,200);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function getActivityComments(string $actId){
        try {
            if(!preg_match("/^[0-9]+$/", $actId))
                   return throw ValidationException::withMessages(['validation error']);
            $res=Activities::find($actId);
            if(!$res)
                return response()->json(['message','this activity not found'],404);
            $res=CommentResource::collection($res->comments);
            return response()->json($res,200);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function addCommentToActivity(CommentRequest $req,string $actId){
        try{
            if(!preg_match("/^[0-9]+$/", $actId))
                   return throw ValidationException::withMessages(['validation error']);
            $newComment=new Comment();
            $newComment->text=$req->text;
            $newComment->name=$req->name;
            $act=Activities::find($actId);
            if(!$act)
                return response()->json(['message'=>'activity not found'],404);        
            $act->comments()->save($newComment);
            return response()->json(['message'=>'comment sent success'],200);
        } catch(Exception $err){
             return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function infoForHomePage(){
        try{
        $last10Projects=ProjectResource::collection(Project::orderBy('id', 'desc')->take(10)->get());
            $allOrganizations=Organization::get()->count();
            $allProjects=Project::select('id','name','allWhoRate','rate')->get();
            $allOrgs=Organization::select('id','user_id')->get();
            $eachOrgWithTotalPros=[];
            $eachProWithTotalsuggestsAndProblemsAndRateInfo=[];
            foreach($allOrgs as $i){
                $ownerName=$i->owner->name;
                $totalPros=$i->projects()->count();
                array_push($eachOrgWithTotalPros,['orgName'=>$ownerName,'totalPros'=>$totalPros]);
            }
            foreach($allProjects as $j){
                $proName=$j->name;
                $totlaSugs=$j->suggests()->count();
                $totalProblems=$j->problems()->count();
                array_push($eachProWithTotalsuggestsAndProblemsAndRateInfo,['ProName'=>$proName,'rateInfo'=>['rate'=>$j->rate,'allPeople'=>$j->allWhoRate],'totalSug'=>$totlaSugs,'totalProblems'=>$totalProblems]);
            }
            return response()->json([
                'last10Problems'=>$last10Projects,
                "organizationsNum"=>$allOrganizations,
                "eachOrgWithTotalPros"=>$eachOrgWithTotalPros,
                "eachProWithTotalsuggestsAndProblemsAndRateInfo"=>$eachProWithTotalsuggestsAndProblemsAndRateInfo
            ],200);
        }catch(Exception $err){
            return response()->json(['message',$err->getMessage()],422);
        }
    }
    public function deleteComment(string $id){
        try{
            if(!preg_match("/^[0-9]+$/",$id))
                    return throw ValidationException::withMessages(['validation error']);
            $com=Comment::find($id);
            if(!$com)
                return response()->json(['message'=>'this comment not found'],404);
            if(!$com->delete())
                return response()->json(['message'=>'delete fail'],422);
            return response()->json(['message'=>'delete success'],200);
        }catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
}