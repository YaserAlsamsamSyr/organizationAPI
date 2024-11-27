<?php

use App\Http\Controllers\API\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\OrganizationController;

Route::middleware('allow')->prefix('masterAdmin')->group(function(){
     Route::post('/login',[AuthController::class,'adminLogin'])->middleware('guest:sanctum');
     Route::post('/register',[UserController::class,'registerNewMasterAdmin'])->middleware('guest:sanctum');
     Route::get('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');
     Route::middleware(['auth:sanctum','abilities:admin'])->group(function(){
        Route::post('/addOrg',[UserController::class,'createOrg']);
        Route::delete('/deleteOrg/{id}',[UserController::class,'deleteOrg']);
        Route::post('/updateOrg/{id}',[UserController::class,'updateOrg']);
        //project
        Route::delete('/deletePro/{orgId}/{proId}',[UserController::class,'deletePro']);
        Route::post('/createPro/{orgId}',[UserController::class,'createPro']);
        Route::post('/updatePro/{orgId}/{proId}',[UserController::class,'updatePro']);
        // get all and specific project , organization
        Route::get('/getProjects',[UserController::class,'getProjects']);
        Route::get('/getOrganizations',[UserController::class,'getOrganizations']);
        Route::get('/getProject/{orgId}/{proId}',[UserController::class,'getProject']);
        Route::get('/getOrganization/{orgId}',[UserController::class,'getOrganization']);
        //
        Route::get('/getSuggests',[UserController::class,'getSuggests']);
        Route::delete('/deleteSuggest/{sugId}',[UserController::class,'deleteSuggest']);
        Route::get('/getProblems',[UserController::class,'getProblems']);
        Route::delete('/deleteProblem/{proId}',[UserController::class,'deleteProblem']);
        // profile
        Route::get('/myProfile',[UserController::class,'myProfile']);
        Route::post('/updateMyProfile',[UserController::class,'updateMyProfile']);
        //traffic
        Route::get('/getTraffic',[UserController::class,'getTraffic']);
        //opinion
        Route::get('/getOpinions/{projectId}',[UserController::class,'getOpinions']);
        Route::delete('/deleteOpinion/{projectId}/{opinionId}',[UserController::class,'deleteOpinion']);
        //activity
        Route::post('/createActivity/{proId}',[UserController::class,'createActivity']);
        Route::post('/updateActivity/{actId}',[UserController::class,'updateActivity']);
        Route::delete('/deleteActivity/{actId}',[UserController::class,'deleteActivity']);
        
    });
});
// organization
Route::middleware('allow')->prefix('organization')->group(function(){
    Route::post('/login',[AuthController::class,'organizationLogin'])->middleware('guest:sanctum');
    Route::get('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');
    Route::middleware(['auth:sanctum','abilities:org'])->group(function(){
        //project
        Route::delete('/deletePro/{proId}',[OrganizationController::class,'deletePro']);
        Route::post('/createPro',[OrganizationController::class,'createPro']);
        Route::post('/updatePro/{proId}',[OrganizationController::class,'updatePro']);
        Route::get('/getProjects',[OrganizationController::class,'getProjects']);
        Route::get('/getProject/{proId}',[OrganizationController::class,'getProject']);
        // profile
        Route::post('/updateMyProfile',[OrganizationController::class,'updateMyProfile']);
        //activity
        Route::post('/createActivity/{proId}',[UserController::class,'createActivity']);
        Route::post('/updateActivity/{actId}',[UserController::class,'updateActivity']);
        Route::delete('/deleteActivity/{actId}',[UserController::class,'deleteActivity']);
   });
});
//client
Route::middleware('allow')->prefix('client')->group(function(){
        Route::post('/addProblem',[ClientController::class,'addProblem']);
        Route::post('/addSuggest',[ClientController::class,'addSuggest']);
        Route::get('/getProjects',[ClientController::class,'getProjects']);
        Route::get('/getOrganizations',[ClientController::class,'getOrganizations']);
        Route::post('/addRate/{proId}',[ClientController::class,'addRate']);
        Route::post('/addComment/{proId}',[ClientController::class,'addComment']);
        Route::post('/addOpinion/{proId}',[ClientController::class,'addOpinion']);
        Route::get('/downloadPDF/{proId}',[ClientController::class,'downloadPDF']);
        Route::get('/getOnlyOrganizations',[ClientController::class,'getOnlyOrganizations']);
        Route::get('/getOnlyProjects/{orgId}',[ClientController::class,'getOnlyProjects']);
        Route::get('/getOnlyActivities/{proId}',[ClientController::class,'getOnlyActivities']);
        Route::post('/addRateToAct/{actId}',[ClientController::class,'addRateToAct']);
        Route::post('/addCustomerToTraffic',[ClientController::class,'addCustomerToTraffic']);
});