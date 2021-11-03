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
use App\Http\Services\V1\SkillService;

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
        $skillService = new SkillService();
        $data = $skillService->list();

        $error = ( isset($data['error']) ) ?  $data['error'] :200;

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

    public function getUserSkills($user_id)
    {
        # code...
        $skillService = new SkillService();
        $data = $skillService->getUserSkills($user_id);

        $error = ( isset($data['error']) ) ?  $data['error'] :200;

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

    public function updateUserSkills($user_id, Request $request)
    {
        # code...
        $skillService = new SkillService();
        $data = $skillService->updateUserSkills($user_id, $request);

        $error = ( isset($data['error']) ) ?  $data['error'] :200;

        if ($data) {
            $this->message = 'Successfully updated data!';
            $this->data  = $data;
        } else {
            $error = 400;
            $this->message = 'There was an issue updating data. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }


    public function deleteUserSkills($user_id, $skill_id)
    {
        # code...
        $skillService = new SkillService();
        $data = $skillService->deleteUserSkills($user_id, $skill_id);

        $error = ( isset($data['error']) ) ?  $data['error'] :200;

        if ($data) {
            $this->message = 'Successfully deleted data!';
            $this->data  = $data;
        } else {
            $error = 400;
            $this->message = 'There was an issue deleting data. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }

    
}
