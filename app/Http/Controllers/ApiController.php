<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use App\usermaster;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterAuthRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    public $loginAfterSignUp = true;
 
    public function register(Request $request)
    {
	$userM = usermaster::where('userType', 'ROLE_USER')->first();
	//return $userM;
        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
	    $user->user_master_id = $userM->id;
        $user->save();
 
        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }
 
        return response()->json([
            'status' => true,
            'data' => $user
        ], Response::HTTP_OK);
    }
 
    public function login(Request $request)
    {

        $input = $request->only('email', 'password');
        $jwt_token = null;
  	
        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ]);
        }


        return response()->json([
            'status' => true,
            'token' => $jwt_token,
        ]);
    }
 
    public function logout(Request $request)
    {
        try {
           // JWTAuth::invalidate($request->token);
            JWTAuth::parseToken()->invalidate(); 
            return response()->json([
                'status' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    public function getAuthUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        $user = JWTAuth::authenticate($request->token);
 
        return response()->json(['user' => $user]);
    }

    public function getAllUsers(Request $request)
    {
                          if (! $user = JWTAuth::parseToken()->authenticate()) {
                                    return response()->json(['user_not_found'], 404);
                            }
  $auser = User::select('*')
                     ->where([['id','!=',$user->id]])
                         ->get();
 
        //$user =  User::all();
 
        return response()->json(['user' => $auser]);
    }

        public function getAuthenticatedUser()
            {
                    try {

                            if (! $user = JWTAuth::parseToken()->authenticate()) {
                                    return response()->json(['user_not_found'], 404);
                            }

                    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                            return response()->json(['token_expired'], $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                            return response()->json(['token_invalid'], $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                            return response()->json(['token_absent'], $e->getStatusCode());

                    }

                    return response()->json(compact('user'));
            }
}
