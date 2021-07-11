<?php

namespace App\Http\Controllers\API\V1;

use JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Login;
use App\Http\Requests\UserRegistration;
use App\Models\User;
use App\Models\Skill;
use App\Http\Helpers\JwtToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Services\V1\UserService;

class SkillController extends Controller
{
    //

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['list']]);
    }

    public function list()
    {
        # code...
        $data = Skill::get();

        $error = 200;

        if ($data) {
            $this->message = 'Successfully fetch data!';
            $this->data  = $data;
        } else {
            $error = 400;
            $this->message = 'There was an issue fetching data. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }
}
