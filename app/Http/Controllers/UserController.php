<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use App\User;
use App\ResetPassword;
use App\PersonalInformation;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller{
    /**
     * This function signs up one provider
     * @param Illuminate/Http/Request $request
     * @return Json
    */
    public function requestUpdatepassword(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'email'                 => 'bail|required|email',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'response' => $validator->errors()
                ],400);
            }else{
                $getUser=User::where('email',$request->email)->first();
                if(!$getUser){
                    return response()->json([
                        'status'   => false,
                        'response' => "This email is not registered with us."
                    ],400);
                }else{
                    $token=time().md5($getUser->id);
                    $updateResetPassword=new ResetPassword();
                    $updateResetPassword->email=$getUser->email;
                    $updateResetPassword->token=$token;
                    if($updateResetPassword->save()){
                        if($getUser->user_type==1){ $usertype="superadmin"; }elseif($getUser->user_type==2){ $usertype="admin"; }elseif($getUser->user_type==3){ $usertype="customer"; }elseif($getUser->user_type==4){ $usertype="guest"; }elseif($getUser->user_type==5){ $usertype="provider"; }elseif($getUser->user_type==5){ $usertype="technician"; }else{ $usertype="user"; }
                        $resetpasslink="reset-password/user/".$usertype."/".$token;
                        return $sendResetpassword=Mail::to($getUser)->send(new ForgotPassword($getUser,$token,$resetpasslink));

                    }else{
                        return response()->json([
                            'status'   => false,
                            'response' => "Unable to send password reset mail."
                        ],400); 
                    }
                    
                }

            }
        } catch(\Exception $e) {
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }
    /**
     * This function signs up one provider
     * @param Illuminate/Http/Request $request
     * @return Json
    */
    public function passwordUpdate(Request $request){
        try{
        	$validator = Validator::make($request->all(),[
                /*'email'                 => 'bail|required|email',*/
                'token'                 => 'required',
                'password'              => 'required|min:8',
                'confirm_password'      => 'required|same:password',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'response' => $validator->errors()
                ],400);
            } else {
                $minute=10;
                $total=$minute*60;
                $resetPassword=ResetPassword::where('token',$request->token)->first();
                if(!$resetPassword){
                    return response()->json([
                        'status'   => false,
                        'response' => "User did not requested for any password reset."
                    ],400);
                }else{
                    $timeRequestCreated=strtotime($resetPassword->created_at);
                    $getTimeDifference=(time())-$timeRequestCreated;
                    if($getTimeDifference >$total){
                        return response()->json([
                            'status'   => false,
                            'response' => "Password reset link expired."
                        ],400);
                    }else{
                        DB::beginTransaction();
                        $getUser=User::where('email',$resetPassword->email)->first();
                        if(!$getUser){
                            return response()->json([
                                'status'   => false,
                                'response' => "This email is not registered with us."
                            ],400);
                        }else{
                            $getUser->password = bcrypt($request->password);
                            if($getUser->save()){
                                DB::commit();
                                return response()->json([
                                    'status'   => true,
                                    'response' => "Password updated successfully."
                                ],400); 
                            }else{
                                DB::rollback();
                                return response()->json([
                                    'status'   => false,
                                    'response' => "Unable to update password."
                                ],400); 
                            }
                        }
                    }
                }
            }
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }
}