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
use App\CustomerAccountSettings;
use App\PestControllMethodInfo;
use App\StateLicense;
use App\ExpireLicense;
use App\ProviderPestService;
use App\PestKeyword;
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

    /**
     * Update a settings details of a provider
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function saveAccountsettings(Request $request){
        //return $request->all();
        try{
            $user=$request->user();
            if($request->has('notification_text')){
                if(($request->notification_text)==0){
                     $updateNotificationTextsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',1)->where('settings','T')->delete();
                }else{
                    $updateNotificationTextsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',1)->where('settings','T')->first();
                    if(!$updateNotificationTextsettings){
                        $updateNotificationTextsettings=new CustomerAccountSettings();
                        $updateNotificationTextsettings->user_id=$user->id;
                        $updateNotificationTextsettings->settings_name=1;
                        $updateNotificationTextsettings->settings='T';
                        $updateNotificationTextsettings->save();
                    }
                   
                }
            }
            if($request->has('notification_email')){
                if(($request->notification_email)==0){
                     $updateNotificationEmailsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',1)->where('settings','E')->delete();
                }else{
                    $updateNotificationEmailsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',1)->where('settings','E')->first();
                    if(!$updateNotificationEmailsettings){
                        $updateNotificationEmailsettings=new CustomerAccountSettings();
                        $updateNotificationEmailsettings->user_id=$user->id;
                        $updateNotificationEmailsettings->settings_name=1;
                        $updateNotificationEmailsettings->settings='E';
                        $updateNotificationEmailsettings->save();
                    }
                }
            }
            if($request->has('notification_social')){
                if(($request->notification_social)==0){
                     $updateNotificationSocialsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',1)->where('settings','S')->delete();
                }else{
                    $updateNotificationSocialsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',1)->where('settings','S')->first();
                    if(!$updateNotificationSocialsettings){
                        $updateNotificationSocialsettings=new CustomerAccountSettings();
                        $updateNotificationSocialsettings->user_id=$user->id;
                        $updateNotificationSocialsettings->settings_name=1;
                        $updateNotificationSocialsettings->settings='S';
                        $updateNotificationSocialsettings->save();
                    }
                }

            }
            if($request->has('notification_phone')){
                if(($request->notification_phone)==0){
                     $updateNotificationPhonesettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',1)->where('settings','P')->delete();
                }else{
                    $updateNotificationPhonesettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',1)->where('settings','P')->first();
                    if(!$updateNotificationPhonesettings){
                        $updateNotificationPhonesettings=new CustomerAccountSettings();
                        $updateNotificationPhonesettings->user_id=$user->id;
                        $updateNotificationPhonesettings->settings_name=1;
                        $updateNotificationPhonesettings->settings='P';
                        $updateNotificationPhonesettings->save();
                    }
                }
            }
            if($request->has('specialoffer_text')){
                if(($request->specialoffer_text)==0){
                     $updateSpecialOfferTextsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',2)->where('settings','T')->delete();
                }else{
                    $updateSpecialOfferTextsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',2)->where('settings','T')->first();
                    if(!$updateSpecialOfferTextsettings){
                        $updateSpecialOfferTextsettings=new CustomerAccountSettings();
                        $updateSpecialOfferTextsettings->user_id=$user->id;
                        $updateSpecialOfferTextsettings->settings_name=2;
                        $updateSpecialOfferTextsettings->settings='T';
                        $updateSpecialOfferTextsettings->save();
                    }
                }
                
            }
            if($request->has('specialoffer_email')){
                if(($request->specialoffer_email)==0){
                     $updateSpecialOfferEmailsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',2)->where('settings','E')->delete();
                }else{
                    $updateSpecialOfferEmailsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',2)->where('settings','E')->first();
                    if(!$updateSpecialOfferEmailsettings){
                        $updateSpecialOfferEmailsettings=new CustomerAccountSettings();
                        $updateSpecialOfferEmailsettings->user_id=$user->id;
                        $updateSpecialOfferEmailsettings->settings_name=2;
                        $updateSpecialOfferEmailsettings->settings='E';
                        $updateSpecialOfferEmailsettings->save();
                    }
                }
                
            }
            if($request->has('specialoffer_social')){
                if(($request->specialoffer_social)==0){
                     $updateSpecialOfferSocialsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',2)->where('settings','S')->delete();
                }else{
                    $updateSpecialOfferSocialsettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',2)->where('settings','S')->first();
                    if(!$updateSpecialOfferSocialsettings){
                        $updateSpecialOfferSocialsettings=new CustomerAccountSettings();
                        $updateSpecialOfferSocialsettings->user_id=$user->id;
                        $updateSpecialOfferSocialsettings->settings_name=2;
                        $updateSpecialOfferSocialsettings->settings='S';
                        $updateSpecialOfferSocialsettings->save();
                    }
                }
            }
            if($request->has('specialoffer_phone')){
                if(($request->specialoffer_phone)==0){
                     $updateSpecialOfferPhonesettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',2)->where('settings','P')->delete();
                }else{
                    $updateSpecialOfferPhonesettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',2)->where('settings','P')->first();
                    if(!$updateSpecialOfferPhonesettings){
                        $updateSpecialOfferPhonesettings=new CustomerAccountSettings();
                        $updateSpecialOfferPhonesettings->user_id=$user->id;
                        $updateSpecialOfferPhonesettings->settings_name=2;
                        $updateSpecialOfferPhonesettings->settings='P';
                        $updateSpecialOfferPhonesettings->save();
                    }
                }
            }
            if($request->has('privacy')){
                $updatePrivacySettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',3)->first();
                if(!$updatePrivacySettings){
                    $updatePrivacySettings=new CustomerAccountSettings();
                    $updatePrivacySettings->user_id=$user->id;
                    $updatePrivacySettings->settings_name=3;
                }
                $updatePrivacySettings->settings= trim($request->privacy);
                $updatePrivacySettings->save();
            }
            if($request->has('post')){
                $updatePostSettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',4)->first();
                if(!$updatePostSettings){
                    $updatePostSettings=new CustomerAccountSettings();
                    $updatePostSettings->user_id=$user->id;
                    $updatePostSettings->settings_name=4;
                }
                $updatePostSettings->settings= trim($request->post);
                $updatePostSettings->save();
            }
            if($request->has('status')){
                $updateStatusSettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',5)->first();
                if(!$updateStatusSettings){
                    $updateStatusSettings=new CustomerAccountSettings();
                    $updateStatusSettings->user_id=$user->id;
                    $updateStatusSettings->settings_name=5;
                }
                $updateStatusSettings->settings = trim($request->status);
                $updateStatusSettings->save();
            }
            if($request->has('service')){
                $updateServiceSettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',6)->first();
                if(!$updateServiceSettings){
                    $updateServiceSettings=new CustomerAccountSettings();
                    $updateServiceSettings->user_id=$user->id;
                    $updateServiceSettings->settings_name=6;
                }
                $updateServiceSettings->settings = trim($request->service);
                $updateServiceSettings->save();
            }
            if($request->has('language')){
                $updateLanguageSettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',7)->first();
                if(!$updateLanguageSettings){
                    $updateLanguageSettings=new CustomerAccountSettings();
                    $updateLanguageSettings->user_id=$user->id;
                    $updateLanguageSettings->settings_name=7;
                }
                $updateLanguageSettings->settings = trim($request->language);
                $updateLanguageSettings->save();
            }
            if($request->has('ein_number')){
                $updateLanguageSettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',8)->first();
                if(!$updateLanguageSettings){
                    $updateLanguageSettings=new CustomerAccountSettings();
                    $updateLanguageSettings->user_id=$user->id;
                    $updateLanguageSettings->settings_name=8;
                }
                $updateLanguageSettings->settings = trim($request->ein_number);
                $updateLanguageSettings->save();
            }
            if($request->has('w9_upload')){
                $updateLanguageSettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',9)->first();
                if(!$updateLanguageSettings){
                    $updateLanguageSettings=new CustomerAccountSettings();
                    $updateLanguageSettings->user_id=$user->id;
                    $updateLanguageSettings->settings_name=9;
                }
                $updateLanguageSettings->settings = trim($request->w9_upload);
                $updateLanguageSettings->save();
            }
            if($request->has('partner')){
                $updateLanguageSettings=CustomerAccountSettings::where('user_id',$user->id)->where('settings_name',10)->first();
                if(!$updateLanguageSettings){
                    $updateLanguageSettings=new CustomerAccountSettings();
                    $updateLanguageSettings->user_id=$user->id;
                    $updateLanguageSettings->settings_name=10;
                }
                $updateLanguageSettings->settings = trim($request->partner);
                $updateLanguageSettings->save();
            }

            
            return response()->json([
                'status'   => true,
                'response' => "Account Settings Updated!"
            ],200); 
       }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    /**
     * Fetch a settings details of a provider
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function getAccountsettings(Request $request){
        try{
            $user=$request->user();
            $allsettings =CustomerAccountSettings::where('user_id',$user->id)->get();
            return response()->json([
                'status'   => true,
                'response' =>[
                    'settings' => $allsettings,
                ]
            ],200); 
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

     /**
     * Fetch a pest controll method of a provider
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function getPestcontrollMethod(Request $request){
        try{
            $user=$request->user();
            $fetchMethods =PestControllMethodInfo::where('user_id',$user->id)->get();
            return response()->json([
                'status'   => true,
                'response' =>[
                    'pestcontrollmethods' => $fetchMethods,
                ]
            ],200); 
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    public function savePestcontrollMethod(Request $request){
        try{
            $user=$request->user();
            if($request->method_status==0){
                $getMethods=PestControllMethodInfo::where('user_id',$user->id)->where('method_id',$request->method_id)->delete();
                if($getMethods){
                    return response()->json([
                        'status'   => true,
                        'response' =>"Method Updated !"
                    ],200);
                }else{
                    return response()->json([
                        'status'   => false,
                        'response' =>"Try Again!"
                    ],400);
                }
            }else{
                $getMethods=PestControllMethodInfo::where('user_id',$user->id)->where('method_id',$request->method_id)->first();
                if(!$getMethods){
                    $updateMethods=new PestControllMethodInfo();
                    $updateMethods->user_id=$user->id;
                    $updateMethods->method_id=$request->method_id;
                    $updateMethods->ecofriendly='1';
                    if($updateMethods->save()){
                        return response()->json([
                            'status'   => true,
                            'response' =>"Method Updated !"
                        ],200);
                    }else{
                        return response()->json([
                            'status'   => false,
                            'response' =>"Try Again!"
                        ],400);
                    }
                }else{
                    return response()->json([
                        'status'   => false,
                        'response' =>"Try Again!"
                    ],400);
                }
            }
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    public function updatePestControllEcofriendlyStatus(Request $request){
        try{
            $user=$request->user();
            $getMethods=PestControllMethodInfo::where('user_id',$user->id)->where('method_id',$request->method_id)->first();
            if($getMethods){
                $getMethods->ecofriendly=$request->echofriendly_status;
                if($getMethods->save()){
                    return response()->json([
                        'status'   => true,
                        'response' =>"Method Updated !"
                    ],200);
                }else{
                    return response()->json([
                        'status'   => false,
                        'response' =>"Try Again!"
                    ],400);
                }
            }else{
                return response()->json([
                    'status'   => false,
                    'response' =>"First add the method then try to change ecofriendly status."
                ],400);
            }
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    /**
     * Fetch a business 
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function getPestLicenceExpire(Request $request){
        try{
            $user=$request->user();
            $fetchLicenseExpireDate =ExpireLicense::where('user_id',$user->id)->get();
            return response()->json([
                'status'   => true,
                'response' =>[
                    'expire_licence' => $fetchLicenseExpireDate,
                ]
            ],200); 
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    public function updatePestLicenceExpire(Request $request){
        try{
            $user=$request->user();
            $getMethods=ExpireLicense::where('user_id',$user->id)->where('pest_type',$request->insects_id)->first();
            if(!$getMethods){
                $getMethods= new ExpireLicense();
                $getMethods->user_id=$user->id;
                $getMethods->pest_type=$request->insects_id;
            }
            $getMethods->expier_date=$request->expier;
            if($getMethods->save()){
                return response()->json([
                    'status'   => true,
                    'response' =>"Updated Business License Expire Date !"
                ],200);
            }else{
                return response()->json([
                    'status'   => false,
                    'response' =>"Try Again!"
                ],400);
            }
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    /**
     * Fetch a business 
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function deletePestLicenceExpire(Request $request){
        try{
            $user=$request->user();
            $deleteStatePermission=ExpireLicense::where('user_id',$user->id)->where('pest_type',$request->insects_id)->delete();
            if($deleteStatePermission){
                return response()->json([
                    'status'   => true,
                    'response' =>"License expiration removed successfully!"
                ],200);
            }else{
                return response()->json([
                    'status'   => false,
                    'response' =>"Try Again!"
                ],400);
            }
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }


    public function saveStateLicence(Request $request){
        try{
            $user=$request->user();
            if($request->state_status==0){
                $deleteStatePermission=StateLicense::where('user_id',$user->id)->where('pest_type',$request->insects_id)->where('state_id',$request->stateid)->delete();
                if($deleteStatePermission){
                    return response()->json([
                        'status'   => true,
                        'response' =>"Successfully removed the state from your service area!"
                    ],200);
                }else{
                    return response()->json([
                        'status'   => false,
                        'response' =>"Try Again!"
                    ],400);
                }
            }else{
                $statePermission=StateLicense::where('user_id',$user->id)->where('pest_type',$request->insects_id)->where('state_id',$request->stateid)->first();
                if($statePermission){
                    return response()->json([
                        'status'   => true,
                        'response' =>"This State is already in your service area!"
                    ],200);
                }else{
                    $addState=new StateLicense();
                    $addState->user_id=$user->id;
                    $addState->pest_type=$request->insects_id;
                    $addState->state_id=$request->stateid;
                    if($addState->save()){
                        return response()->json([
                            'status'   => true,
                            'response' =>"This State is added in your service area!"
                        ],200);
                    }else{
                        return response()->json([
                            'status'   => false,
                            'response' =>"Try Again!"
                        ],400);
                    }
                }
            }
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

     public function getStateLicence(Request $request){
        try{
           $user=$request->user();
            $data = StateLicense::select('pest_type',DB::raw("JSON_OBJECTAGG(id,state_id) as statevalues"))->with('statesinfo')->where('user_id',$user->id)->orderBy("pest_type")->groupBy('pest_type')->get();
                return response()->json([
                    'status'   => true,
                    'response' =>[
                        'serve_states' => $data
                    ]
                ],200); 
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    public function savePestTypeForService(Request $request){
        try{
            $user=$request->user();
            $pest_main_type=ProviderPestService::where('user_id',$user->id)->where('pest_type',$request->type)->where('pest_catagory',1)->first();
            if($pest_main_type && $request->status==0){
                $delete_type=ProviderPestService::where('user_id',$user->id)->where('pest_type',$request->type)->where('pest_catagory',1)->delete();
                if($delete_type){
                    return response()->json([
                        'status'   => true,
                        'response' =>'This catagory is removed.'
                    ],200);
                }else{
                    return response()->json([
                        'status'   => false,
                        'response' =>"Try Again!"
                    ],400);
                }
            }else if((!$pest_main_type) && $request->status==1){
                $newPestService=new ProviderPestService();
                $newPestService->user_id=$user->id;
                $newPestService->pest_type=$request->type;
                $newPestService->pest_catagory=1;
                if($newPestService->save()){
                    return response()->json([
                        'status'   => true,
                        'response' =>'This catagory is added.'
                    ],200);
                }else{
                    return response()->json([
                        'status'   => false,
                        'response' =>"Try Again!"
                    ],400);
                }
            }else{
                return response()->json([
                    'status'   => false,
                    'response' =>"Try Again!"
                ],400);
            }
            
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    public function getPestTypeForService(Request $request){
        try{
            $user=$request->user();
            $pest_main_type=ProviderPestService::where('user_id',$user->id)->where('pest_catagory',1)->get();
            return response()->json([
                'status'   => true,
                'response' =>[
                    'pest_main_catagory' => $pest_main_type,
                ]
            ],200); 
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    public function saveKeyWord(Request $request){
        try{
            $user=$request->user();
            $newPestService=new PestKeyword();
            $newPestService->user_id=$user->id;
            $newPestService->pest_type=$request->pest_type;
            $newPestService->keyword=$request->keyword;
            if($newPestService->save()){
                return response()->json([
                    'status'   => true,
                    'response' =>'This catagory is added.'
                ],200);
            }else{
                return response()->json([
                    'status'   => false,
                    'response' =>"Try Again!"
                ],400);
            }
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    public function deleteKeyWord(Request $request){
         try{
            $user=$request->user();
            $deleteKeyWord=ProviderPestService::where('id',$keyword_id)->where('user_id',$user->id)->delete();
            return response()->json([
                'status'   => true,
                'response' =>'Key Word Deleted'
            ],200); 
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    public function getKeyWord(Request $request){
        try{
            $user=$request->user();
            $data = PestKeyword::select('pest_type',DB::raw("JSON_OBJECTAGG(id, keyword) as keyvalue"))->where('user_id',$user->id)->orderBy("pest_type")->groupBy('pest_type')->get();
                return response()->json([
                    'status'   => true,
                    'response' =>[
                        'set_keywords' => $data
                    ]
                ],200); 
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    public function saveSubcatagoryPestForService(Request $request){
        try{
            $user=$request->user();
            $pest_sub_type=ProviderPestService::where('user_id',$user->id)->where('pest_type',$request->type)->where('pest_catagory',2)->first();
            if($pest_sub_type && $request->status==0){
                $delete_type=ProviderPestService::where('user_id',$user->id)->where('pest_type',$request->type)->where('pest_catagory',1)->delete();
                if($delete_type){
                    return response()->json([
                        'status'   => true,
                        'response' =>'This catagory is removed.'
                    ],200);
                }else{
                    return response()->json([
                        'status'   => false,
                        'response' =>"Try Again!"
                    ],400);
                }
            }else if((!$pest_sub_type) && $request->status==1){
                $newPestService=new ProviderPestService();
                $newPestService->user_id=$user->id;
                $newPestService->pest_type=$request->type;
                $newPestService->pest_catagory=2;
                if($newPestService->save()){
                    return response()->json([
                        'status'   => true,
                        'response' =>'This catagory is added.'
                    ],200);
                }else{
                    return response()->json([
                        'status'   => false,
                        'response' =>"Try Again!"
                    ],400);
                }
            }else{
                return response()->json([
                    'status'   => false,
                    'response' =>"Try Again!"
                ],400);
            }
            
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

}
