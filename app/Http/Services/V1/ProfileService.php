<?php

namespace App\Http\Services\V1;

use JWTAuth;
use App\Http\Services\Service;
use App\Http\Services\V1\SkillService;
use App\Models\User;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileService extends Service
{
    public function create($request)
    {
        # code...

        $request->merge([
            'type'              => 'regular',
            'created_date'      => Carbon::now()->format('Y-m-d h:i:s'),
            'password'          => Hash::make($request->password),
            'ip'                => $_SERVER['REMOTE_ADDR'],
            'coin'              => 0,
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


    public function getUserProfile($user_id, $request)
    {
        # code...

        $data = Profile::where(['user_id' => $user_id])->first();

        if($data){
            return $data;
        }

        return false;
    }

    public function updateFirstName($user_id, $request)
    {
        # code...
        $data = tap(Profile::where([ 'user_id' => $user_id]))->update($request);

        if($data){
            return $data;
        }

        return false;
    }

}
