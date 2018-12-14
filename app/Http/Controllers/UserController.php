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
     * This function sign in one user
     * @param Illuminate/Http/Request $request
     * @return json
     */
    public function requestUpdatepassword(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'email'  => 'bail|required|email',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'response' => $validator->errors()
                ],400);
            }else{
                $getUser=User::where('email',$request->email)->with('personal_info')->first();
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
                        $resetpasslink="reset-password/user/".$token;
                        $sendResetpassword=Mail::to($getUser)->send(new ForgotPassword($getUser,$token,$resetpasslink));
                        return response()->json([
                            'status'   => true,
                            'response' => "Please check your inbox. We send you a email with a reset password link"
                        ],200);
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
                                ],200); 
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
    /** This function signs up one provider
    * @param Illuminate/Http/Request $request
    * @return Json
    */   
    public function postLogin(Request $request) {
        return User::userAuthentication($request);
    }
    /**
     * This function returns the status of token provided
     * @param Illuminate/Http/Request $request
     * @return Json
     */
    public function postCheckStatus(Request $request) {
        if ($request->has('token')) {
            try {
                $search_token = ResetPassword::where('token', $request->token)->first();
                if ($search_token) {
                    $server_time = getdate();
                    $date        = $server_time['mday'];
                    $month       = $server_time['mon'];
                    $year        = $server_time['year'];
                    $hour        = $server_time['hours'];
                    $min         = $server_time['minutes'];
                    $sec         = $server_time['seconds'];
                    $timesnow    = $year.'-'.$month.'-'.$date.' '.$hour.':'.$min.':'.$sec;
                    $startTime   = \Carbon\Carbon::parse($search_token->created_at);
                    $finishTime  = \Carbon\Carbon::parse($timesnow);
                    $timesago    = $finishTime->diffInMinutes($startTime);
                    return response()->json([
                        'status'   => true,
                        'response' => [
                            'response' => $search_token,
                            'metadata' => [
                                'timesago' => $timesago
                            ]
                        ]
                    ],200);
                } else {
                    return response()->json([
                        'status'   => false,
                        'response' => 'Invalid token given!'
                    ],400);  
                }
            } catch(\Exception $e) {
                return response()->json([
                    'status'   => false,
                    'response' => $e->getMessage()
                ],$e->getCode()); 
            }
        } else {
            return response()->json([
                'status' => false,
                'response' => 'Missing Expected param token!'
            ],400);
        }
    }
    /**
     * verify email
     * @param verification_id null
     */
    public function getVerifyEmail($verification_id = null) {
        if ($verification_id) {
            try {
                $search_id = User::findOrFail($verification_id);
                if ($search_id) {
                    $search_id->email_verified_at = now();
                    if ($search_id->save()) {
                        return response()->json([
                            'status' => true,
                            'response' => [
                                'response' => 'Successfully verified your email!',
                                'metadata' => $search_id->user_type
                            ]
                        ],200);
                    } else {
                        return response()->json([
                            'status' => false,
                            'response' => 'Failed to verify your email. Please try again later!'
                        ],500);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'response' => 'No user verification code found!'
                    ],404);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status'   => false,
                    'response' => $e->getMessage() 
                ],$e->getCode());
            }
        } else {
            return response()->json([
                'status'   => false,
                'response' => 'Missing verification code!'
            ],400);
        }
    }
    /**
     * check user is verified 
     * @param request Request
     */
    public function postCheckUserStatus(Request $request) {
        if ($request->has('user_id')) {
            try {
                $search_user = User::findOrFail($request->user_id);
                if ($search_user) {
                    return response()->json([
                        'status' => true,
                        'response' => $search_user
                    ],200);
                } else {
                    return response()->json([
                        'status' => false,
                        'response' => 'No user found!'
                    ],404);
                }
            } catch(\Exception $e) {
                return response()->json([
                    'status'   => false,
                    'response' => $e->getMessage()
                ], $e->getCode());
            }
        } else {
            return response()->json([
                'status' => false,
                'response' => 'No user id supplied!'
            ],400);
        }
    }
}