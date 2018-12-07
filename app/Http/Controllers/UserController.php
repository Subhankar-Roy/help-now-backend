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
                'password'     => 'required|max:255',
                'confpassword' => 'required|max:255',
                'phone'        => 'required|max:255',
                'zip'          => 'required|max:255'
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
                //$customerInfo->custom_user_id =
                $customerInfo->first_name = trim($createCustomer->first_name);
                $customerInfo->last_name = trim($createCustomer->last_name);
                $customerInfo->phone = (int)trim($createCustomer->phone);
                //$customerInfo->street =(isset($createCustomer->street))? trim($createCustomer->street) : NULL;
                //$customerInfo->po = (isset($createCustomer->po))? trim($createCustomer->po) : NULL;
                //$customerInfo->city = (isset($createCustomer->city))? trim($createCustomer->city) : NULL;
                //$customerInfo->state = (isset($createCustomer->state))? trim($createCustomer->state) : NULL;
                $customerInfo->zip = (int)trim($createCustomer->zip);
                //$customerInfo->additional_address_info =(isset($createCustomer->add_info))? trim($createCustomer->add_info) : NULL;
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

    protected function generateCustomerCustomID($userid){
       // $customerId="C-";
        $getUserIdLength=strlen($userid);
        //for($i=$getUserIdLength; )
    }
}