<?php
/**
 * This controller intended to use only for provider or owner and their related transactions.
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use Carbon\Carbon;
use App\PersonalInformation;
use App\ProviderOrganization;
use App\DemographicsInformation;
use App\ProviderPayment;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Mail\VerifyEmail;

class ProviderController extends Controller
{
    protected $user_type = '5'; // service provider
    protected $registration_type = '1'; // login using email and password
    /**
     * This function signs up one provider
     * @param Illuminate/Http/Request $request
     * @return json
     */
    public function signUp(Request $request) {
        $validator = Validator::make($request->all(),[
            'first_name'            => 'required',
            'last_name'             => 'required',
            'mobile_phone'          => 'bail|required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'email'                 => 'bail|required|unique:users|email',
            'password'              => 'required|min:8',
            'confirm_password'      => 'required|same:password',
            'zip'                   => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'response' => $validator->errors()
            ],400);
        } else {
            try {
                DB::beginTransaction();
                // saving provider info
                $user = new User();
                $user->email                = $request->email;
                $user->password             = bcrypt($request->password);
                $user->user_type            = $this->user_type;
                $user->registration_type    = $this->registration_type;
                if ($user->save()) {
                    // save provider personal info
                    $p_info                 = new PersonalInformation();
                    $p_info->user_id        = $user->id;
                    $p_info->custom_user_id = unique_id_generator('P', 'USA');
                    $p_info->first_name     = $request->first_name;
                    $p_info->middle_name    = $request->has('middle_name') ? $p_info->middle_name : null;
                    $p_info->last_name      = $request->last_name;
                    $p_info->phone          = $request->mobile_phone;
                    $p_info->zip            = $request->zip;
                    if ($p_info->save()) {
                        DB::commit();
                        Mail::to($request->email)->send(new VerifyEmail($request->first_name,'verify-email/user/'.$user->id));
                        return response()->json([
                            'status'   => true,
                            'response' => [
                                'response' => 'Successfully saved record!',
                                'metadata' => User::userAuthentication($request)
                            ]
                        ],200);
                    } else {
                        DB::rollback();
                        return response()->json([
                            'status'   => false,
                            'response' => 'Failed while saving personal info!'
                        ],500);
                    }
                } else {
                    DB::rollback();
                    return response()->json([
                        'status'   => false,
                        'response' => 'Something went wrong while saving the record! Please try again later.'
                    ],500);
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
    
    /**
     * Method for update providers personal information
     * @param Illuminate/Http/Request $request
     * @return \Illuminate\Http\Response
    */
    public function savePersonalInfo(Request $request){
        try{
            $user=$request->user();
            DB::beginTransaction();
            $savePersonalInfo= PersonalInformation::where('user_id',$user->id)->first();
            if(!$savePersonalInfo){
                $savePersonalInfo= new PersonalInformation();
                $savePersonalInfo->user_id        = $user->id;
                $savePersonalInfo->custom_user_id = unique_id_generator('C', 'USA');
            }
            if($request->has('first_name')){
                $savePersonalInfo->first_name = trim($request->first_name);
            }
            if($request->has('middle_name')){
               $savePersonalInfo->middle_name = trim($request->middle_name);
            }
            if($request->has('last_name')){
               $savePersonalInfo->last_name = trim($request->last_name);
            }
            if($request->has('phone')){
               $savePersonalInfo->phone = trim($request->phone);
            }
            if($savePersonalInfo->save()){
                DB::commit();
                return response()->json([
                    'status' => true,
                    'response'  => "Personal information saved successfully."
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
                'response' => $e->getMessage()
            ],$e->getCode());
        }
    }

    /**
     * Method for update providers personal information
     * @param Illuminate/Http/Request $request
     * @return \Illuminate\Http\Response
    */
    public function saveOrganizatinInfo(Request $request){
        try{
            $user=$request->user();
            DB::beginTransaction();
            $saveOrganization= ProviderOrganization::where('user_id',$user->id)->first();
            if(!$saveOrganization){
                $saveOrganization= new ProviderOrganization();
                $saveOrganization->user_id = $user->id;
            }
            if($request->has('organization')){
                $saveOrganization->organization_name = trim($request->organization);
            }
            if($request->has('phone')){
               $saveOrganization->phone = trim($request->phone);
            }
            if($request->has('founded_at')){
               $saveOrganization->founded_at = trim($request->founded_at);
            }
            if($request->has('title')){
               $saveOrganization->title = trim($request->title);
            }
            if($request->has('street')){
               $saveOrganization->street = trim($request->street);
            }
            if($request->has('po')){
               $saveOrganization->po = trim($request->po);
            }
            if($request->has('city')){
               $saveOrganization->city = trim($request->city);
            }
            if($request->has('state')){
               $saveOrganization->state = trim($request->state);
            }
            if($request->has('zip')){
               $saveOrganization->zip = trim($request->zip);
            }
            if($saveOrganization->save()){
                DB::commit();
                return response()->json([
                    'status' => true,
                    'response'  => "Organization information saved successfully."
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
                'response' => $e->getMessage()
            ],$e->getCode());
        }
    }

    public function getOrganizatinInfo(Request $request){
        try{
            $user=$request->user();
            $getOrganization= ProviderOrganization::where('user_id',$user->id)->first();
            if($getOrganization){
                return response()->json([
                    'status' => true,
                    'response' =>[
                        'organizationstatus' =>1,
                        'organization' => $getOrganization
                    ]
                ],200);
            }else{
                return response()->json([
                    'status' => true,
                    'response' =>[
                        'organizationstatus' =>0,
                        'organization' => $getOrganization
                    ]
                ],200);
            }
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'response' => $e->getMessage()
            ],$e->getCode());
        }
    }    

    /**
     * Method to fetch provider's personal information
     * @param Illuminate/Http/Request $request
     * @return \Illuminate\Http\Response
    */
    public function getPersonalinfo(Request $request){
        try{
            $user=$request->user();
            $getPersonalinfo= PersonalInformation::where('user_id',$user->id)->first();
            return response()->json([
                'status'   => true,
                'response' => [
                        'user'         => $user,
                        'personalinfo' => $getPersonalinfo,
                        'username'     => strstr($user->email, '@', true),
                        'password'     => $user->password
                ]
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'response' => $e->getMessage()
            ],$e->getCode());
        }
    }

    /**
     * Methods to update and save provider's demographic data
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function saveDemographicinfo(Request $request){
        try{
            $user=$request->user();
            DB::beginTransaction();
            $saveDemographics = DemographicsInformation::updateOrCreate(['user_id' => $user->id]);
            if($request->has('language')){
                $saveDemographics->language = trim($request->language);
            }
            if($request->has('gender')){
                $saveDemographics->gender = trim($request->gender);
            }
            if($request->has('birthdate')){
                $saveDemographics->birthdate = trim($request->birthdate);
            }
            if($request->has('ethnicity')){
                $saveDemographics->ethnicity = trim($request->ethnicity);
            }
            if($request->has('relationship')){
                $saveDemographics->relationship = trim($request->relationship);
            }
            if($request->has('education')){
                $saveDemographics->education = trim($request->education);
            }
            if($request->has('occupation')){
                $saveDemographics->occupation = trim($request->occupation);
            }
            if($saveDemographics->save()){
                DB::commit();
                return response()->json([
                    'status' => true,
                    'response'  => "Demographics information saved successfully."
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
                'response' => $e->getMessage()
            ],$e->getCode());
        }
    }

    /**
     * Fetch provider's dempgraphic info.
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function getDemographicinfo(Request $request){
        try{
            $user=$request->user();
            $getdemographicInfo=DemographicsInformation::where('user_id',$user->id)->first();
            if($getdemographicInfo){
                return response()->json([
                    'status'   => true,
                    'response' => [
                            'demostatus'      => 1,
                            'demographicinfo' =>  $getdemographicInfo,
                    ]
                ],200);
            }else{
                return response()->json([
                    'status'   => true,
                    'response' => [
                        'demostatus' => 0,
                        'message'    => "Please Fill Demographics Information."
                    ]
                ],200);
            }
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage(),
            ],$e->getCode());
        }
    }

    /**
     * Methods to update and save provider's payment information
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function savePaymentinfo(Request $request){
        /*try{*/

            /*$validator = Validator::make($request->all(), [
                'street'       => 'required',
                'city'         => 'required',
                'zip'          => 'required|numeric'
            ]);

            if ($validator->fails()){
                return response()->json([
                    'status'   => false, 
                    'response' => $validator->messages()
                ],400);
            }*/
            $user=$request->user();
            DB::beginTransaction();
            $savePaymentinfo=ProviderPayment::updateOrCreate(['user_id' => $user->id]);
            if($request->has('bank')){
                $savePaymentinfo->bank = trim($request->bank);
            }
            if($request->has('account_number')){
                $savePaymentinfo->account_number = trim($request->account_number);
            }
            if($request->has('routing')){
                $savePaymentinfo->routing = trim($request->routing);
            }
            if($request->has('name_on_card')){
                $savePaymentinfo->name_on_card = trim($request->name_on_card);
            }
            if($request->has('street')){
                $savePaymentinfo->street = trim($request->street);
            }
            if($request->has('po')){
                $savePaymentinfo->po = trim($request->po);
            }
            if($request->has('city')){
                $savePaymentinfo->city = trim($request->city);
            }
            if($request->has('state')){
                $savePaymentinfo->state = trim($request->state);
            }
            if($request->has('zip')){
                $savePaymentinfo->zip = trim($request->zip);
            }
            if($request->has('paypal_account')){
                $savePaymentinfo->paypal_account = trim($request->paypal_account);
            }
            if($request->has('venmo_account')){
                $savePaymentinfo->venmo_account = trim($request->venmo_account);
            }
            if($savePaymentinfo->save()){
                DB::commit();
                return response()->json([
                    'status'    => true,
                    'response'  => "Payment information saved successfully."
                ],200);
            }else{
                DB::rollback();
                return response()->json([
                    'status'   => false,
                    'response' => "Something went worng! Try again!"
                ],400);
            }
        /*}catch(\Exception $e){
             DB::rollback();
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage(),
            ],$e->getCode());
        }*/
    }

    /**
     * Fetch provider's payment info.
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function getPaymentinfo(Request $request){
        try{
            $user=$request->user();
            $getpaymentInfo=ProviderPayment::where('user_id',$user->id)->first();
            if($getpaymentInfo){
                return response()->json([
                    'status'   => true,
                    'response' => [
                            'paymentstatus' => 1,
                            'paymentinfo'   => $getpaymentInfo
                    ]
                ],200);
            }else{
                return response()->json([
                    'status'   => true,
                    'response' => [
                            'paymentstatus' => 0,
                            'paymentinfo'   => "Please Fill Payment Settings."
                    ]
                ],200);
            }
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage(),
            ],$e->getCode());
        }
    }
}
