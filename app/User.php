<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';

    /**
     * For softdelete
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email'
    ];

    /**
     * The attributes that can not be mass assignable.
     *
     * @var array
     */
    protected $guarded = [ 
        'id','user_type','registration_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    public function getJWTCustomClaims() {
        return [];
    }
    /**
     * This function sign in one user 
     * coule be used in multiple controllers
     * @param Illuminate/Http/Request $request
     * @return json
     */
    public static function userAuthentication($request = null) {
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
                        'response' => 'Unauthorized! Wrong email id or password.'
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
                        'response' => $exception->getMessage()
                    ],$exception->getCode());
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
