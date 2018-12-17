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
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Mail\VerifyEmail;
use Crypt;


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
                'first_name'       => 'required|max:255',
                'last_name'        => 'required|max:255',
                'email'            => 'required|email|unique:users|max:255',
                'password'         => 'required|min:8',
                'confirm_password' => 'required|min:8|same:password',
                'mobile_phone'     => 'required|numeric|min:10',
                'zip'              => 'required|numeric|min:5'
            ]);

            if ($validator->fails()){
                return response()->json([
                    'status' => false,
                    'response' => $validator->errors()
                ],400);
            }

            DB::beginTransaction();
            $createCustomer = new User();
            $createCustomer->email             = trim($request->email);
            $createCustomer->user_type         = 3;
            $createCustomer->registration_type = 1;
            $createCustomer->password          = bcrypt($request->password);
            if($createCustomer->save()){
                $customerInfo = new PersonalInformation();
                $customerInfo->user_id        = $createCustomer->id;
                $customerInfo->custom_user_id = unique_id_generator('C', 'USA');
                $customerInfo->first_name     = trim($request->first_name);
                $customerInfo->middle_name    = $request->has('middle_name') ? $request->middle_name : NULL;
                $customerInfo->last_name      = trim($request->last_name);
                $customerInfo->phone          = trim($request->mobile_phone);
                $customerInfo->street         = $request->has('street')? trim($request->street) : NULL;
                $customerInfo->po             = $request->has('po')? trim($request->po) :  NULL;
                $customerInfo->city           = $request->has('city')? trim($request->city) :  NULL;
                $customerInfo->state          = $request->has('state')? trim($request->state) :  NULL;
                $customerInfo->zip            = trim($request->zip);
                //$customerInfo->additional_address_info =$request->has('add_info')? trim($request->add_info) :  NULL;
                if($customerInfo->save()){
                    DB::commit();
                    Mail::to($request->email)->send(new VerifyEmail($request->first_name,'verify-email/user/'.$createCustomer->id));
                    return response()->json([
                        'status' => true,
                        'response' => [
                            'response' => 'Successfully saved record!',
                            'metadata' => User::userAuthentication($request)
                        ]
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
                    'response' => "Something went worng! Try again!"
                ],400);
            }
        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
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
     * Method for update customer personal information
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
            if($request->has('street')){
               $savePersonalInfo->street = trim($request->street);
            }
            if($request->has('po')){
               $savePersonalInfo->po = trim($request->po);
            }
            if($request->has('city')){
               $savePersonalInfo->city = trim($request->city);
            }
            if($request->has('state')){
               $savePersonalInfo->state = trim($request->state);
            }
            if($request->has('zip')){
               $savePersonalInfo->zip = trim($request->zip);
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
     * Method to fetch customer personal information
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
     * Methods to update and save customer's demographic data
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
     * Fetch customer's dempgraphic info.
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
     * Methods to update and save customer's professional information
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function saveProfessionalinfo(Request $request){
        try{

            $user=$request->user();
            DB::beginTransaction();
            $saveProfessionalinfo=ProfessionalInformation::updateOrCreate(['user_id' => $user->id]);
            if($request->has('employer_name')){
                $saveProfessionalinfo->employer_name = trim($request->employer_name);
            }
            if($request->has('designation')){
                 $saveProfessionalinfo->designation = trim($request->designation);
            }
            if($request->has('phone')){
                $saveProfessionalinfo->phone = trim($request->phone);
            }
            if($request->has('email')){
                $saveProfessionalinfo->email = trim($request->email);
            }
            if($request->has('street')){
                $saveProfessionalinfo->street = trim($request->street);
            }
            if($request->has('po')){
                $saveProfessionalinfo->po = trim($request->po);
            }
            if($request->has('city')){
                $saveProfessionalinfo->city = trim($request->city);
            }
            if($request->has('state')){
                $saveProfessionalinfo->state = trim($request->state);
            }
            if($request->has('zip')){
                $saveProfessionalinfo->zip = trim($request->zip);
            }
            if($saveProfessionalinfo->save()){
                DB::commit();
                return response()->json([
                    'status' => true,
                    'response'  => "Professional information saved successfully."
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
            ],$e->getCode());
        }
    }

    /**
     * Fetch customer's professional info.
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function getProfessionalinfo(Request $request){
        try{
            $user=$request->user();
            $getprofessionalInfo=ProfessionalInformation::where('user_id',$user->id)->first();
            if($getprofessionalInfo){
                return response()->json([
                    'status'   => true,
                    'response' => [
                            'professionalstatus' => 1,
                            'professionalinfo' =>  $getprofessionalInfo,
                    ]
                ],200);
            }else{
                return response()->json([
                    'status' => true,
                    'response' => [
                            'professionalstatus' => 0,
                            'message'    => "Please Fill Professional Information."
                    ]
                ],200);
            }
        }catch(\Exception $e){
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
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
            $savePaymentinfo=CustomerPaymentSettings::updateOrCreate(['user_id' => $user->id]);
            if($request->has('card_type')){
                $savePaymentinfo->card_type = trim($request->card_type);
            }
            if($request->has('account_number')){
                $savePaymentinfo->account_number = trim($request->account_number);
            }
            if($request->has('expiration')){
                $savePaymentinfo->expiration = trim($request->expiration);
            }
            if($request->has('name_on_card')){
                $savePaymentinfo->name_on_card = trim($request->name_on_card);
            }
            if($request->has('security_code')){
                $savePaymentinfo->security_code = trim($request->security_code);
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
        }catch(\Exception $e){
             DB::rollback();
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage(),
            ],$e->getCode());
        }
    }

    /**
     * Fetch customer's payment info.
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function getPaymentinfo(Request $request){
        try{
            $user=$request->user();
            $getpaymentInfo=CustomerPaymentSettings::where('user_id',$user->id)->first();
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
     * Methods save customer's property information
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function createProperty(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name'       => 'required',
                'type'        => 'required',
            ]);

            if ($validator->fails()){
                return response()->json([
                    'status' => false,
                    'response' => $validator->errors()
                ],400);
            }
            $user=$request->user();
            DB::beginTransaction();
            $saveProperty = new CustomerPropertyInformation();
            $saveProperty->user_id       = $user->id; 
            $saveProperty->name          = ($request->has('name')) ? trim($request->name) : NULL;
            $saveProperty->property_type = ($request->has('type')) ? trim($request->type) : NULL;
            $saveProperty->street        = ($request->has('street')) ? trim($request->street) : NULL;
            $saveProperty->po            = ($request->has('po')) ? trim($request->po) : NULL;
            $saveProperty->city          = ($request->has('city')) ? trim($request->city) : NULL;
            $saveProperty->state         = ($request->has('state')) ? trim($request->state) : NULL;
            $saveProperty->zip           = ($request->has('zip')) ? trim($request->zip) : NULL;
            $saveProperty->area          = ($request->has('area')) ? trim($request->area) : NULL;
            $saveProperty->area_unit     = ($request->has('area_unit')) ? trim($request->area_unit) : NULL;
            if($saveProperty->save()){
                DB::commit();
                return response()->json([
                    'status'   => true,
                    'response' => "Property information saved successfully."
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
                'status'   => false,
                'response' => $e->getMessage()
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
            $user=$request->user();
            DB::beginTransaction();
            $updateProperty=CustomerPropertyInformation::where('user_id',$user->id)->where('id',$request->property_id)->first();
            if($updateProperty){
                if($request->has('name')){
                    $updateProperty->name = trim($request->name);
                }
                if($request->has('type')){
                    $updateProperty->property_type = trim($request->type);
                }
                if($request->has('street')){
                    $updateProperty->street = trim($request->street);
                }
                if($request->has('po')){
                    $updateProperty->po = trim($request->po);
                }
                if($request->has('city')){
                    $updateProperty->city = trim($request->city);
                }
                if($request->has('state')){
                    $updateProperty->state = trim($request->state);
                }
                if($request->has('zip')){
                    $updateProperty->zip = trim($request->zip);
                }
                if($request->has('area')){
                    $updateProperty->area = trim($request->area);
                }
                if($request->has('area_unit')){
                    $updateProperty->area_unit = trim($request->area_unit);
                }
                if($updateProperty->save()){
                    DB::commit();
                    return response()->json([
                        'status'   => true,
                        'response' => "Property information updated successfully."
                    ],200);
                }else{
                    DB::rollback();
                    return response()->json([
                        'status'   => false,
                        'response' => "Something went worng! Try again!"
                    ],400);
                }
            }else{
                 DB::rollback();
                return response()->json([
                    'status'   => false,
                    'response' => "This property information is not available!"
                ],400);
            }
        }catch(\Exception $e){
             DB::rollback();
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
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
            $propertyId=trim($request->property_id);
            $user=$request->user();
            DB::beginTransaction();
            $property =CustomerPropertyInformation::where('id',$propertyId)->where('user_id',$user->id)->DELETE();
            if($property){
                DB::commit();
                return response()->json([
                    'status'   => true,
                    'response' => "Property deleted successfully."
                ],200); 
            }else{
                DB::rollback();
                return response()->json([
                    'status'   => false,
                    'response' => "Property can not be deleted. Try Again."
                ],400);
            }
        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'status'   => false,
                'response' => $e->getMessage()
            ],$e->getCode()); 
        }
    }

    /**
     * Fetch all property of a customer
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function getallProperty(Request $request){
        try{
            $user=$request->user();
            $allproperty =CustomerPropertyInformation::where('user_id',$user->id)->get();
            return response()->json([
                'status'   => true,
                'response' =>[
                    'propertycount' => count($allproperty),
                    'allproperty'   => $allproperty
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
     * Fetch a property details of a customer
     * @param Illuminate/Http/Request $request
     * @return json
    */
    public function getPropertyinfo(Request $request){
        try{
            $user=$request->user();
            $allproperty =CustomerPropertyInformation::where('id',$request->property_id)->where('user_id',$user->id)->first();
            return response()->json([
                'status'   => true,
                'response' =>[
                    'propertyinfo' => $allproperty,
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
     * Update a settings details of a customer
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
     * Fetch a settings details of a customer
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
}