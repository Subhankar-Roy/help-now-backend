<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\User;
use App\PersonalInformation;

class UserController extends Controller{
    /**
     * Save the customer who is registering with email and password.
     * 
     *
     * @return Response
     */
    public function storeCustomer(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'first_name'   => 'required|max:255',
                'last_name'    => 'required|max:255',
                'email'        => 'required|email|unique:users|max:255',
                'password'     => 'required|min:6|max:255',
                'confpassword' => 'required|min:6|max:255',
                'phone'        => 'required|numeric',
                'zip'          => 'required|numeric'
            ]);

            if ($validator->fails()){
                return response()->json([
                    'status' => false,
                    'response' => $validator->messages(),
                    'message'  =>'Please provide required fields.'
                ],400);
            }

            DB::beginTransaction();
            $createCustomer = new User();
            $createCustomer->email                 = trim($request->email);
            $createCustomer->email_verified_at     = date('Y-m-d H:i:s');
            $createCustomer->user_type             = 3;
            $createCustomer->registration_type     = 1;
            $createCustomer->password              = Hash::make($request->password);
            if($createCustomer->save()){
                $customerInfo = new PersonalInformation();
                $customerInfo->user_id = $createCustomer->id;
                $customerInfo->custom_user_id = self::generateCustomerCustomID($createCustomer->id);
                $customerInfo->first_name = trim($createCustomer->first_name);
                $customerInfo->middle_name = (isset($createCustomer->middle_name))? trim($createCustomer->street) : "";
                $customerInfo->last_name = trim($createCustomer->last_name);
                $customerInfo->phone = trim($createCustomer->phone);
                $customerInfo->street =(isset($createCustomer->street))? trim($createCustomer->street) : "";
                $customerInfo->po = (isset($createCustomer->po))? trim($createCustomer->po) : "";
                $customerInfo->city = (isset($createCustomer->city))? trim($createCustomer->city) : "";
                $customerInfo->state = (isset($createCustomer->state))? trim($createCustomer->state) : "";
                $customerInfo->zip = trim($createCustomer->zip);
                $customerInfo->additional_address_info =(isset($createCustomer->add_info))? trim($createCustomer->add_info) : "";
                if($customerInfo->save()){
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message'  => "User Created Successfully."

                    ],201);
                }else{
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' => "Something went worng! Try again!"
                    ],400);
                }
            }else{
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => "Something went worng! Try again!"
                ],400);
            }
        }catch(Exception $error){
            DB::rollback();
            return response()->json([
                'status' => false,
                'response' => $error,
                'message' => "Something went worng! Try again!"
            ],500);
        }
    }

    /**
    * Method generate string for unique custom-user-id 
    * @param $userid | int
    * @return string
    */
    protected function generateCustomerCustomID($userid){
        $getUserIdLength=strlen($userid);
        $generateId=$userid;
        for($i=$getUserIdLength; $i<9; $i++){
            $generateId="0".$generateId;
        }
        $insert="-";
        $position=3;
        $generateId= implode($insert, str_split($generateId, $position));
        $generateId="C-".$generateId."-USA";
        return $generateId;
    }

     /**
    * @return \Illuminate\Http\Response
    */
 
    public function login(Request $request){
        try{
            $user = User::where('email', trim($request->email))->first();
            if($user ){
                if($user->user_type==3){
                    if (Hash::check($request->password, $user->password)) {
                        $token=$this->jwt($user);
                        return response()->json([
                            'status' => true,
                            'token' => $token,
                            'message' => "User Loggedin Successfully."
                        ],200);
                    }else{
                        return response()->json([
                            'status' => false,
                            'message' => "Password Mismatch."
                        ],400);
                    }
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => "This Email Id Is Not Registered As A Customer."
                    ],400);  
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message' => "This Email Id Is Not Registered."
                ],400);  
            }
        }catch(Exception $error){
            return response()->json([
                'status' => false,
                'response' => $error,
                'message' => "Something went worng! Try again!"
            ],500);
        }
    }
 
}