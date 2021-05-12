<?php

namespace App\Http\Services\V1;

use JWTAuth;
use App\Http\Services\Service;
use App\Models\User;
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
            'coin'              => 0,
            'notification'      => 0,
            'login_attempt'     => 0,
            'status'            => 'active',
        ]);

        $create = User::create($request->all());

        if($create){
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
}
