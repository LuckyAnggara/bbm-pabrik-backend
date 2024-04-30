<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends BaseController

{

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();
            // if (count($user->tokens) >= 1) {
            //     return $this->sendResponse('user still login', 'User still Login.', 202);
            // } else {
                $user->tokens()->delete();
                $success['token']['access_token'] =  $user->createToken('MyApp')->plainTextToken;
                $success['token']['token_type'] = 'Bearer';
                $success['user'] =  Auth::user();
                return $this->sendResponse($success, 'User login successfully.');
            // }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }


    public function logout(Request $request)
    {
        // Get bearer token from the request
        $accessToken = $request->bearerToken();
        // Get access token from database
        $token = PersonalAccessToken::findToken($accessToken);
        if($token){
            // Revoke token
            $token->delete();
            return $this->sendResponse('done', 'User logout successfully.');
        }
        return $this->sendResponse('error','token not found');

    }

     public function user(Request $request)
    {
        return Auth::user();
    }
}
