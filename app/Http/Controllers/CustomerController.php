]<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DemographicsInformation;
use App\ProfessionalInformation;
use App\CustomerPaymentSettings;
use App\PersonalInformation;
use App\CustomerPropertyInformation;
use App\CustomerAccountSettings;
use App\User;
use Validator;
use DB;

class CustomerController extends Controller
{
    /**
     * Methods to update and save customer's demographic data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveDemographicinfo(Request $request){
        try{
            $demographicInfo=DemographicsInformation::where('user_id',$request->userId)->first();
            DB::beginTransaction();
            if(count($demographicInfo) >0){
                $saveDemographics=DemographicsInformation::find($demographicInfo->id);
            }else{
                $saveDemographics = new DemographicsInformation();
                $saveDemographics->user_id = $request->userId;
            }
            $saveDemographics->language = (isset($request->language))? trim($request->language) : "";
            $saveDemographics->gender =(isset($request->gender))? trim($request->gender) : "";
            $saveDemographics->birthdate = (isset($request->birthdate))? trim($request->birthdate) : "";
            $saveDemographics->ethnicity =  (isset($request->ethnicity))? trim($request->ethnicity) : "";
            $saveDemographics->relationship =  (isset($request->relationship))? trim($request->relationship) : "";
            $saveDemographics->education =  (isset($request->education))? trim($request->education) : "";
            $saveDemographics->occupation =  (isset($request->occupation))? trim($request->occupation) : "";
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
        }catch(Exception $error){
            DB::rollback();
            return response()->json([
                'status' => false,
                'response' =>$error,
                'message' => "Something went worng! Try again!"
            ],500);
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
        }catch(Exception $error){
            return response()->json([
                'status' => false,
                'response' =>$error,
                'message' => "Something went worng! Try again!"
            ],500);
        }
    }

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

            $professionalInfo=ProfessionalInformation::where('user_id',$request->userId)->first();
            DB::beginTransaction();
            if(count($professionalInfo) >0){
                $saveProfessionalinfo=ProfessionalInformation::find($professionalInfo->id);
            }else{
                $saveProfessionalinfo = new ProfessionalInformation();
                $saveProfessionalinfo->user_id = $request->userId;
            }
            $saveProfessionalinfo->employer_name=(isset($request->employer_name))? trim($request->employer_name) : "";
            $saveProfessionalinfo->designation=(isset($request->designation))? trim($request->designation) : "";
            $saveProfessionalinfo->phone=(isset($request->phone))? trim($request->phone) : "";
            $saveProfessionalinfo->email=(isset($request->email))? trim($request->email) : "";
            $saveProfessionalinfo->street=(isset($request->street))? trim($request->street) : "";
            $saveProfessionalinfo->po=(isset($request->po))? trim($request->po) : "";
            $saveProfessionalinfo->city=(isset($request->city))? trim($request->city) : "";
            $saveProfessionalinfo->state=(isset($request->state))? trim($request->state) : "";
            $saveProfessionalinfo->zip=(isset($request->zip))? trim($request->zip) : "";
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
        }catch(Exception $error){
            DB::rollback();
            return response()->json([
                'status' => false,
                'response' => $error,
                'message' => "Something went worng! Try again!"
            ],500);
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
        }catch(Exception $error){
            return response()->json([
                'status' => false,
                'response' =>$error,
                'message' => "Something went worng! Try again!"
            ],500);
        }
    }

    public function getPaymentinfo(Request $request){
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
        }catch(Exception $error){
            return response()->json([
                'status' => false,
                'response' =>$error,
                'message' => "Something went worng! Try again!"
            ],500);
        }
    }

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

            $paymentInfo=CustomerPaymentSettings::where('user_id',$request->userId)->first();
            DB::beginTransaction();
            if(count($paymentInfo) >0){
                $savePaymentinfo=CustomerPaymentSettings::find($paymentInfo->id);
            }else{
                $savePaymentinfo = new CustomerPaymentSettings();
                $savePaymentinfo->user_id = $request->userId;
            }
            $savePaymentinfo->name=(isset($request->name))? trim($request->name) : "";
            $savePaymentinfo->property_type=(isset($request->property_type))? trim($request->property_type) : "";
            $savePaymentinfo->street=(isset($request->street))? trim($request->street) : "";
            $savePaymentinfo->po=(isset($request->po))? trim($request->po) : "";
            $savePaymentinfo->city=(isset($request->city))? trim($request->city) : "";
            $savePaymentinfo->state=(isset($request->state))? trim($request->state) : "";
            $savePaymentinfo->zip=(isset($request->zip))? trim($request->zip) : "";
            $savePaymentinfo->area=(isset($request->area))? trim($request->area) : "";
            $savePaymentinfo->area_unit=(isset($request->area_unit))? trim($request->area_unit) : "";
            if($savePaymentinfo->save()){
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
        }catch(Exception $error){
            DB::rollback();
            return response()->json([
                'status' => false,
                'response' => $error,
                'message' => "Something went worng! Try again!"
            ],500);
        }
    }

    public function createProperty(Request $request){
        $saveProperty = new CustomerPropertyInformation();
        $saveProperty->user_id=$request->user_id;
        $saveProperty->name=$request->name;
        $saveProperty->property_type=$request->property_type;
        $saveProperty->street=$request->street;
        $saveProperty->po=$request->po;
        $saveProperty->city=$request->city;
        $saveProperty->state=$request->state;
        $saveProperty->zip=$request->zip;
        $saveProperty->area=$request->area;
        $saveProperty->area_unit=$request->area_unit;
        if($saveProperty->save()){

        }else{

        }
    }

    public function updateProperty(Request $request){
        try{
            $getProperty=CustomerPaymentSettings::where('user_id',$request->userId)->where('id',$request->propertyId)->first();
            if(count($paymentInfo) >0){
                $updateProperty=CustomerPaymentSettings::find($paymentInfo->id);
                $updateProperty->user_id=$request->user_id;
                $updateProperty->name=$request->name;
                $updateProperty->property_type=$request->property_type;
                $updateProperty->street=$request->street;
                $updateProperty->po=$request->po;
                $updateProperty->city=$request->city;
                $updateProperty->state=$request->state;
                $updateProperty->zip=$request->zip;
                $updateProperty->area=$request->area;
                $updateProperty->area_unit=$request->area_unit;
                if($updateProperty->save()){

                }else{

                }
            }else{

            }
        }catch(Exception $error){
        }  
    }

    public function deleteProperty(Request $request){
    }
    

}