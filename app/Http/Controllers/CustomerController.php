<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\CustomerAccountSettings;
use App\CustomerPaymentSettings;
use App\CustomerPropertyInformation;
use App\DemographicsInformation;
use App\ProfessionalInformation;
use App\PersonalInformation;
use App\User;
use Validator;
use DB;
use globalHelper;
use Carbon\Carbon;


class CustomerController extends Controller
{   
    protected $user_type = '3'; // Registered User
    protected $registration_type = '1'; // Registered using email and password
    
    /**
     * Save the customer who is registering with email and password.
     * @param Illuminate/Http/Request $request
     * @return json
     */
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'first_name'   => 'required|max:255',
                'last_name'    => 'required|max:255',
                'email'        => 'required|email|unique:users|max:255',
                'password'     => 'required|min:6',
                'confpassword' => 'required|min:6|same:password',
                'phone'        => 'required|numeric|min:10',
                'zip'          => 'required|numeric'
            ]);

            if ($validator->fails()){
                return response()->json([
                    'status' => false,
                    'response' => $validator->errors(),
                    'message'  =>'Please provide required fields properly.'
                ],400);
            }

            DB::beginTransaction();
            $createCustomer = new User();
            $createCustomer->email                 = trim($request->email);
            $createCustomer->email_verified_at     = now();
            $createCustomer->user_type             = 3;
            $createCustomer->registration_type     = 1;
            $createCustomer->password              = bcrypt($request->password);
            if($createCustomer->save()){
                $customerInfo = new PersonalInformation();
                $customerInfo->user_id        = $createCustomer->id;
                $customerInfo->custom_user_id = unique_id_generator('C', 'USA');
                $customerInfo->first_name     = trim($request->first_name);
                $customerInfo->middle_name    = $request->has('middle_name') ? $request->middle_name : NULL;
                $customerInfo->last_name      = trim($request->last_name);
                $customerInfo->phone          = trim($request->phone);
                $customerInfo->street         = $request->has('street')? trim($request->street) : NULL;
                $customerInfo->po             = $request->has('po')? trim($request->po) :  NULL;
                $customerInfo->city           = $request->has('city')? trim($request->city) :  NULL;
                $customerInfo->state          = $request->has('state')? trim($request->state) :  NULL;
                $customerInfo->zip            = trim($request->zip);
                //$customerInfo->additional_address_info =$request->has('add_info')? trim($request->add_info) :  NULL;
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
        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage(),
                'message' => "Something went worng! Try again!"
            ],$e->getCode()); 
        }
    }

    /**
     * Method for login customer
     * @param Illuminate/Http/Request $request
     * @return \Illuminate\Http\Response
    */
    public function login(Request $request){
        return User::userAuthentication($request);
    }

    /**
     * Methods to update and save customer's demographic data
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function saveDemographicinfo(Request $request){
        try{
            DB::beginTransaction();
            $saveDemographics = DemographicsInformation::updateOrCreate(['user_id' => $request->userId]);
            $saveDemographics->language     = $request->has('language')? trim($request->language) : NULL;
            $saveDemographics->gender       = $request->has('gender')? trim($request->gender) : NULL;
            $saveDemographics->birthdate    = $request->has('birthdate')? trim($request->birthdate) : NULL;
            $saveDemographics->ethnicity    = $request->has('ethnicity')? trim($request->ethnicity) : NULL;
            $saveDemographics->relationship = $request->has('relationship')? trim($request->relationship) : NULL;
            $saveDemographics->education    = $request->has('education')? trim($request->education) : NULL;
            $saveDemographics->occupation   = $request->has('occupation')? trim($request->occupation) : NULL;
            if($saveDemographics->save()){
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message'  => "Demographics information saved successfully."
                ],200);
            }else{
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => "Something went worng! Try again!"
                ],400);
            }
        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'status' => false,
                'response' => $e->getMessage(),
                'message' => "Something went worng! Try again!"
            ],$e->getCode());
        }
    }

    public function getDemographicinfo(){
        try{
            $getprofessionalInfo=DemographicsInformation::where('user_id',$request->userId)->first();
            if(count($getprofessionalInfo) >0){
                return response()->json([
                    'status' => true,
                    'demostatus' => 1,
                    'response'   => $getprofessionalInfo,
                    'message' => "Please Fill Demographics Information."
                ],200);
            }else{
                return response()->json([
                    'status' => true,
                    'demostatus' => 0,
                    'message' => "Please Fill Demographics Information."
                ],200);
            }
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'response' => $e->getMessage(),
                'message' => "Something went worng! Try again!"
            ],$e->getCode());
        }
    }

    /**
     * Methods to update and save customer's professional information
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function saveProfessionalinfo(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'work_phone'   => 'required',
                'work_email'   => 'required',
                'street'       => 'required',
                'city'         => 'required',
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
            $saveProfessionalinfo=ProfessionalInformation::updateOrCreate(['user_id' => $request->userId]);
            $saveProfessionalinfo->employer_name = $request->has('employer_name')? trim($request->employer_name) : NULL;
            $saveProfessionalinfo->designation   = $request->has('designation')? trim($request->designation) : NULL;
            $saveProfessionalinfo->phone         = $request->has('phone')? trim($request->phone) : NULL;
            $saveProfessionalinfo->email         = $request->has('email')? trim($request->email) : NULL;
            $saveProfessionalinfo->street        = $request->has('street')? trim($request->street) : NULL;
            $saveProfessionalinfo->po            = $request->has('po')? trim($request->po) : NULL;
            $saveProfessionalinfo->city          = $request->has('city')? trim($request->city) : NULL;
            $saveProfessionalinfo->state         = $request->has('state')? trim($request->state) : NULL;
            $saveProfessionalinfo->zip           = $request->has('zip')? trim($request->zip) : NULL;
            if($saveProfessionalinfo->save()){
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message'  => "Professional information saved successfully."
                ],200);
            }else{
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => "Something went worng! Try again!"
                ],400);
            }
        }catch(\Exception $e){
             DB::rollback();
            return response()->json([
                'status' => false,
                'response' => $e->getMessage(),
                'message' => "Something went worng! Try again!"
            ],$e->getCode());
        }
    }

    public function getProfessionalinfo(){
        try{
            $getprofessionalInfo=ProfessionalInformation::where('user_id',$request->userId)->first();
            if(count($getprofessionalInfo) >0){
                return response()->json([
                    'status' => true,
                    'demostatus' => 1,
                    'response'   => $getprofessionalInfo,
                    'message' => "Professional Information"
                ],200);
            }else{
                return response()->json([
                    'status' => true,
                    'demostatus' => 0,
                    'message' => "Please Fill Demographics Information."
                ],200);
            }
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'response' => $e->getMessage(),
                'message' => "Something went worng! Try again!"
            ],$e->getCode());
        }
    }

    /**
     * Methods to update and save customer's payment information
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function savePaymentinfo(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'street'       => 'required',
                'city'         => 'required',
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
            $savePaymentinfo=CustomerPaymentSettings::updateOrCreate(['user_id' => $request->userId]);
            $savePaymentinfo->name          = $request->has('$request->name')? trim($request->name) : NULL;
            $savePaymentinfo->property_type = $request->has('property_type')? trim($request->property_type) : NULL;
            $savePaymentinfo->street        = $request->has('street')? trim($request->street) : NULL;
            $savePaymentinfo->po            = $request->has('po')? trim($request->po) : NULL;
            $savePaymentinfo->city          = $request->has('city')? trim($request->city) : NULL;
            $savePaymentinfo->state         = $request->has('state')? trim($request->state) : NULL;
            $savePaymentinfo->zip           = $request->has('zip')? trim($request->zip) : NULL;
            $savePaymentinfo->area          = $request->has('area')? trim($request->area) : NULL;
            $savePaymentinfo->area_unit     = $request->has('area_unit')? trim($request->area_unit) : NULL;
            if($savePaymentinfo->save()){
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message'  => "Prayment information saved successfully."
                ],200);
            }else{
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => "Something went worng! Try again!"
                ],400);
            }
        }catch(\Exception $e){
             DB::rollback();
            return response()->json([
                'status' => false,
                'response' => $e->getMessage(),
                'message' => "Something went worng! Try again!"
            ],$e->getCode());
        }
    }

    public function getPaymentinfo(){
        try{
            $getprofessionalInfo=CustomerPaymentSettings::where('user_id',$request->userId)->first();
            if(count($getprofessionalInfo) >0){
                return response()->json([
                    'status' => true,
                    'demostatus' => 1,
                    'response'   => $getprofessionalInfo,
                    'message' => "Payment Settings"
                ],200);
            }else{
                return response()->json([
                    'status' => true,
                    'demostatus' => 0,
                    'message' => "Please Fill Payment Settings."
                ],200);
            }
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'response' => $e->getMessage(),
                'message' => "Something went worng! Try again!"
            ],$e->getCode());
        }
    }

    /**
     * Methods save customer's property information
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function createProperty(Request $request){
        try{
            DB::beginTransaction();
            $saveProperty = new CustomerPropertyInformation();
            $saveProperty->user_id       = $request->user_id;
            $saveProperty->name          = $request->name;
            $saveProperty->property_type = $request->property_type;
            $saveProperty->street        = $request->street;
            $saveProperty->po            = $request->po;
            $saveProperty->city          = $request->city;
            $saveProperty->state         = $request->state;
            $saveProperty->zip           = $request->zip;
            $saveProperty->area          = $request->area;
            $saveProperty->area_unit     = $request->area_unit;
            if($saveProperty->save()){
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message'  => "Property information saved successfully."
                ],200);
            }else{
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'response' => "Something went worng! Try again!"
                ],400);
            }
        }catch(\Exception $e){
             DB::rollback();
            return response()->json([
                'status' => false,
                'response' => $e->getMessage(),
                'message' => "Something went worng! Try again!"
            ],$e->getCode());
        }
    }

    /**
     * Methods update customer's property information
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function updateProperty(Request $request){
        try{
            DB::beginTransaction();
            $getProperty=CustomerPropertyInformation::where('user_id',$request->userId)->where('id',$request->propertyId)->first();
            if(count($paymentInfo) >0){
                $updateProperty=CustomerPropertyInformation::find($paymentInfo->id);
                $updateProperty->user_id       = $request->user_id;
                $updateProperty->name          = $request->name;
                $updateProperty->property_type = $request->property_type;
                $updateProperty->street        = $request->street;
                $updateProperty->po            = $request->po;
                $updateProperty->city          = $request->city;
                $updateProperty->state         = $request->state;
                $updateProperty->zip           = $request->zip;
                $updateProperty->area          = $request->area;
                $updateProperty->area_unit     = $request->area_unit;
                if($updateProperty->save()){
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message'  => "Property information updated successfully."
                    ],200);
                }else{
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'response' => "Something went worng! Try again!"
                    ],400);
                }
            }else{
                 DB::rollback();
                return response()->json([
                    'status' => false,
                    'response' => "This property information is not available!"
                ],400);
            }
        }catch(\Exception $e){
             DB::rollback();
            return response()->json([
                'status' => false,
                'response' => $e->getMessage(),
                'message' => "Something went worng! Try again!"
            ],$e->getCode());
        }  
    }

    /**
     * Methods delete customer's payment information
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function deleteProperty(Request $request){
        try{
            DB::beginTransaction();
            $propertyId=trim($requst->property_id);

            $property =CustomerPropertyInformation::find($propertyId);
            if($property->delete()){
                DB::commit();
                return response()->json([
                    'status'   => true,
                    'response' => "Success",
                    'message' => "Property deleted successfully."
                ],200); 
            }else{
                DB::rollback();
                return response()->json([
                    'status'   => false,
                    'response' => "Fail",
                    'message' => "Property can not be deleted. Try Again."
                ],400);
            }
        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage(),
                'message' => "Something went worng! Try again!"
            ],$e->getCode()); 
        }
    }

    public function getallProperty(){

    }

    public function getPropertyinfo(){
        
    }
}