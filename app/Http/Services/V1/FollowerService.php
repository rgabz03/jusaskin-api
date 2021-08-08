<?php

namespace App\Http\Services\V1;

use JWTAuth;
use App\Http\Services\Service;
use App\Http\Services\V1\SkillService;
use App\Http\Services\V1\ProfileService;
use App\Models\User;
use App\Models\Profile;
use App\Models\Follower;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class FollowerService extends Service
{
    public function getUserCountFollower($user_id)
    {
        # code...
        $data = Follower::selectRaw("count(follower_id) as 'count'")
                        ->where(['user_id' => $user_id])
                        ->get();

        if($data){
            return $data;
        }

        return false;
    }


    public function getMyFollower($user_id)
    {
        # code...
        $data = Follower::where(['user_id' => $user_id])
                        ->get();

        if($data){
            return $data;
        }

        return false;
    }


    public function getFollowed($user_id)
    {
        # code...
        $data = Follower::where(['follower_id' => $user_id])
                        ->get();

        if($data){
            return $data;
        }

        return false;
    }

}
