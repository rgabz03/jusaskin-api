<?php

namespace App\Http\Controllers\API\V1;

use JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Login;
use App\Http\Requests\UserRegistration;
use App\Models\User;
use App\Http\Helpers\JwtToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Services\V1\UserService;

class UserController extends Controller
{
    //

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','create']]);
    }


    public function create(UserRegistration $request)
    {
        # code...
        $userService = new UserService();
        $data = $userService->create($request);

        $error =(isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully registered user!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue registering this user. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);

    }

    public function login(Login $request)
    {
        # code...
        $credentials = $request->only(['username', 'password']);

        $error = 200;
        if (! $token = auth()->attempt($credentials)) {
            $error = 401;
            $this->message = 'There was an issue logging in this user. Please try again.';
            $this->error = true;
        }else{
            $user_data = User::where(['username' => $request->username])->first();
            $data = $this->respondWithToken($token);
            $this->message = 'Successfully login user!';
            $this->data  = ['access' => $data->original, 'user_data' => $user_data];
        }

        return response()->json($this->getResponse(), $error);

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function guard()
    {
        return Auth::guard('api');
    }


    public function profile($id)
    {
        # code...
        $userService = new UserService();
        $data   =   $userService->profile($id);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully viewed user profile!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue getting users profile. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }
}
