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
use DB;

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
            'password'              => 'required|min:6',
            'confirm_password'      => 'required|same:password',
            'name_of_organization'  => 'required'
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
                $user->email_verified_at    = now();
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
                    if ($p_info->save()) {
                        // save in organization info
                        $o_details                    = new ProviderOrganization();
                        $o_details->user_id           = $user->id;
                        $o_details->organization_name = $request->name_of_organization;
                        if ($o_details->save()) {
                            DB::commit();
                            return response()->json([
                                'status'   => true,
                                'response' => 'Successfully saved record!'
                            ],200);
                        } else {
                            DB::rollback();
                            return response()->json([
                                'status'   => false,
                                'response' => 'Failed while saving organization info!'
                            ],500);
                        }
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
     * This function sign in one user
     * @param Illuminate/Http/Request $request
     * @return json
     */
    public function login(Request $request) {
        if ($request->has('email') && $request->has('password')) {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            try {
                $token = auth('api')->attempt($credentials);
                if ($token) {
                    return response()->json([
                        'status' => true,
                        'response' => [
                            'token' => $token,
                            'authenticated_user' => auth('api')->user(),
                            // to change the expiration of web token go to config/jwt.php
                            'expires' => auth('api')->factory()->getTTL() * 60,
                        ]
                        ],200);
                } else {
                    return response()->json([
                        'status' => false,
                        'response' => 'Unauthorized!'
                    ],401);
                }
            } catch (\Exception $exception) {
                // Exception handling based on types
                if ($exception instanceof UnauthorizedHttpException) {
                    $preException = $exception->getPrevious();
                    if ($preException instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                        return response()->json([
                            'status' => false,
                            'response' => 'TOKEN_EXPIRED'
                        ],401);
                    } else if ($preException instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                        return response()->json([
                            'status' => false,
                            'response' => 'TOKEN_INVALID'
                        ],401);
                    } else if ($preException instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
                        return response()->json([
                            'status' => false,
                            'response' => 'TOKEN_BLACKLISTED'
                        ],401);
                    }
                    if ($exception->getMessage() === 'Token not provided') {
                        return response()->json([
                            'status' => false,
                            'response' => 'Token not provided'
                        ],400);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'response' => $e->getMessage()
                    ],$e->getCode());
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'response' => 'Missing expected params. Hint: email or password!'
            ],400);
        }
    }
}
