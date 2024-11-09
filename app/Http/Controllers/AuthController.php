<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\UserResource;
use Exception;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    //
    public function adminLogin(Request $req){
       try{
        $req->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', Rules\Password::defaults()],
        ]);
        if(!auth()->attempt(['email' => $req->email, 'password' => $req->password,'role'=>'admin']))
            return response()->json(['message'=>'password or email not correct'],422);
       $token=auth()->user()->createToken('admin',expiresAt:now()->addDays(4),abilities:['admin'])->plainTextToken;
       $data=new UserResource(auth()->user());
       return response()->json(['token'=>$token,'response'=>$data],200);
      } catch(Exception $err){
        return response()->json(["message"=>$err->getMessage()],500);
      }
    }

    public function orgLogin(LoginRequest $req){
        try{
            $req->validate([
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
                'password' => ['required',Rules\Password::defaults()],
            ]);       
            if(!auth()->attempt(['email' => $req->email, 'password' => $req->password,'role'=>'admin'])){
                return response()->json(['message'=>'password or email not correct'],422);
            }
            $token=auth()->user()->createToken('org',expiresAt:now()->addDays(4),abilities:['org'])->plainTextToken;
            $org=new OrganizationResource(auth()->user());
            return response()->json(['token'=>$token,'response'=>$org],200);
        } catch(Exception $err){
            return response()->json(["message"=>$err->getMessage()],500);
        }
    }

    public function logout(Request $req){
        try{
            return $req->user()->currentAccessToken()->delete() ?
             response()->json(["message"=>"logout success"],200) :
             response()->json(["message"=>"logout fail"],422) ;
        }catch(Exception $err){
            return response()->json(["message"=>$err->getMessage()],500);
        }
    }
}
