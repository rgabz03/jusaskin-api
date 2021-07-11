<?php

namespace App\Http\Services\V1;

use JWTAuth;
use App\Http\Services\Service;
use App\Http\Services\V1\SkillService;
use App\Http\Services\V1\ProfileService;
use App\Http\Services\V1\FollowerService;
use App\Models\User;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserService extends Service
{
    public function create($request)
    {
        # code...

        $request->merge([
            'type'              => 'regular',
            'created_date'      => Carbon::now()->format('Y-m-d h:i:s'),
            'password'          => Hash::make($request->password),
            'ip'                => $_SERVER['REMOTE_ADDR'],
            'coins'             => 100,
            'notification'      => 0,
            'login_attempt'     => 0,
            'status'            => 'active',
        ]);

        $create = User::create($request->all());

        if($create){

            $profile_data_payloads = [
                'user_id'       => $create->id,
                'created_date'  => Carbon::now()->format('Y-m-d h:i:s'),
            ];

            $create_default_profile = Profile::create($profile_data_payloads);

            return $create;
        }

        return false;
    }


    public function loginUser($credentials)
    {
        # code...

        if (! $token = auth()->attempt($credentials)) {

            return ['error' => 401];
        }

        return $token;
    }


    public function profile($id)
    {
        # code...
        if(auth()->user()->id != $id){
            return ['error' => 401];
        }

        $skillService = new SkillService();

        $users = User::where(['id' => $id])
                    ->join('profiles','profiles.user_id','users.id')
                    ->first();

        $skills = $skillService->getUserSkills($id);

        if($users){
            return ['profile' => $users, 'skills' => $skills];
        }

        return false;
    }


    public function getCoinsBalance($id, $request)
    {
        # code...
        if(auth()->user()->id != $id){
            return ['error' => 401];
        }

        $data = User::where(['id' => $id])->first('coins');

        if($data){
            return $data;
        }
        return false;
    }

    public function getUserProfile($id, $request)
    {
        # code...
        if(auth()->user()->id != $id){
            return ['error' => 401];
        }

        $profileService = new ProfileService();

        $data = $profileService->getUserProfile($id, $request);

        if($data){
            return $data;
        }
        return false;
    }


    public function getUserCountFollower($user_id)
    {
        # code...
        $followerService = new FollowerService();

        $data = $followerService->getUserCountFollower($user_id);

        if($data){
            return $data;
        }

        return false;
    }


    public function getUserInterest($user_id)
    {
        # code...

        $skillService = new SkillService();

        $data = $skillService->getUserSkills($user_id);

        if($data){
            return $data;
        }

        return false;
    }

}
