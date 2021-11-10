<?php

namespace App\Http\Controllers\API\V1;

use JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Login;
use App\Http\Requests\UserRegistration;
use App\Http\Requests\Profile;
use App\Models\User;
use App\Http\Helpers\JwtToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Services\V1\UserService;
use App\Http\Services\V1\FollowerService;
use App\Http\Services\V1\MessageService;
use Carbon\Carbon;

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
            $update_login = User::where('id', $user_data->id)->update(['login_date' => Carbon::now()->format('Y-m-d h:i:s')]);
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

    public function getCoinsBalance($id, Request $request)
    {
        # code...
         $userService = new UserService();
         $data   =   $userService->getCoinsBalance($id, $request);

         $error  =   (isset($data['error'])) ? $data['error'] : 200;

         if ($data) {
             $this->message = 'Successfully viewed user coins!';
             $this->data  = $data;
         } else {
             $this->message = 'There was an issue getting users coins. Please try again.';
             $this->error = true;
         }

         return response()->json($this->getResponse(), $error);
    }

    public function getUserProfile($id, Request $request)
    {
        # code...
        $userService = new UserService();
        $data   =   $userService->getUserProfile($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully viewed user profile!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue viewing users profile. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }

    public function getUserCountFollower($id, Request $request)
    {
        # code...

        $userService = new UserService();
        $data   =   $userService->getUserCountFollower($id);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully get followers count!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue getting followers count. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }

    public function getUserInterest($id, Request $request)
    {
        # code...
        $userService = new UserService();
        $data   =   $userService->getUserInterest($id);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully get users interest!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue getting interest. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }


    public function getUserPostSave($id, Request $request)
    {
        # code...

        $userService = new UserService();
        $data   =   $userService->getUserSavedPost($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully get users saved post!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue getting saved post. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }

    public function recieveNotification($id, Request $request)
    {
        # code...
        $userService = new UserService();
        $data   =   $userService->recieveNotification($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully update notification!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue update notification. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }

    public function updateProfile($id, Profile $request)
    {
        # code...
        $userService = new UserService();

        $data   =   $userService->updateProfile($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data && !isset($data['error'])) {
            $this->message = 'Successfully updated Profile!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue updating profile. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }



    public function updateDescription($id, Request $request)
    {
        # code...
        $userService = new UserService();

        $data   =   $userService->updateDescription($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully updated Description!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue updating description. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }



    public function updateProfession($id, Request $request)
    {
        # code...
        $userService = new UserService();

        $data   =   $userService->updateProfession($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully updated Profession!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue updating profession. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }


    public function getMyMessages($id, Request $request)
    {
        # code...
        $messageService = new MessageService();

        $data   =   $messageService->getMyMessages($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully fetch data!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue fetching data. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }

    public function getMessageFromUser($id,$user_id)
    {
        # code...
        $messageService = new MessageService();

        $data   =   $messageService->getMessageFromUser($id, $user_id);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully fetch data!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue fetching data. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }

    public function getNameFromUserMessage($id,$user_id)
    {
        # code...
        $messageService = new MessageService();

        $data   =   $messageService->getNameFromUserMessage($id, $user_id);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully fetch data!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue fetching data. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }
    
    public function sendMessageToUser($id,Request $request)
    {
        # code...
        $messageService = new MessageService();

        $data   =   $messageService->sendMessageToUser($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully sent data!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue sending data. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }


    public function list(Request $request)
    {
        # code...
        $userService = new UserService();

        $data   =   $userService->list($request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully fetch data!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue  data. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }


    public function checkIfYouFollowedUser($id, $user_id)
    {
        # code...
        $userService = new UserService();

        $data   =   $userService->checkIfYouFollowedUser($id, $user_id);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully fetch data!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue fetching data. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }


    public function followUser($id, $user_id)
    {
        # code...
        $followerService = new FollowerService();

        $data   =   $followerService->followUser($id, $user_id);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully fetch data!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue fetching data. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }
    
    
}
