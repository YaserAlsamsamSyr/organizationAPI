<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProblemRequest;
use App\Http\Requests\SuggestRequest;
use Exception;
use App\Models\Problem;
use App\Models\Suggest;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\OrganizationResource;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ClientController extends Controller
{
    public function addProblem(ProblemRequest $req){
                try{
                    $pro=new Problem();
                    $pro->text=$req->text;
                    $pro->save();
                    return response()->json(['message'=>'add success'],201);
                }catch(Exception $err){
                    return response()->json(['message'=>$err->getMessage()],422);
                }
    }
    public function addSuggest(SuggestRequest $req){
        try{
            $sug=new Suggest();
            $sug->text=$req->text;
            $sug->save();
            return response()->json(['message'=>'add success'],201);
        }catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function getProjects(){
        try {
            $res=Project::all();
            if($res)
            $res=ProjectResource::collection(Project::all());
            return response()->json($res,201);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function getOrganizations(){
        try {
            $res=User::where('role','org')->get();
            if($res)
                $res=OrganizationResource::collection($res);
            return response()->json($res,201);
        } catch(Exception $err){
            return response()->json(['message'=>$err->getMessage()],422);
        }
    }
    public function addComment(ProblemRequest $req,string $proId){
           try{
                if(!preg_match("/^[0-9]+$/", $proId))
                       return throw ValidationException::withMessages(['validation err']);
                $newComment=new Comment();
                $newComment->text=$req->text;
                $pro=Project::find($proId);
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
                    if(!preg_match("/^[0-5]{1}$/", $req->rate))
                           return throw ValidationException::withMessages(['rating value err , must be [0 -> 5]']);
                    $pro=Project::find($proId);
                    $pro->rate=($pro->rate+$req->rate)/2;
                    $pro->save();
                    return response()->json(['message'=>'rating success'],200);
                  }catch(Exception $err){
                      return response()->json(['message'=>$err->getMessage()],422);
                  }
    }
    public function downloadPDF(string $proId){
              try{
                $pro=Project::findOrFail($proId);
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
}