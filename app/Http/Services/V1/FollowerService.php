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
        $data = Follower::selectRaw("count(followed_id) as 'count'")
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
        $data = Follower::where(['followed_id' => $user_id])
                        ->get();

        if($data){
            return $data;
        }

        return false;
    }


    public function followUser($id, $user_id)
    {
        # code...
        $check = Follower::where(['user_id' =>  $id, "followed_id" => $user_id])->first();

        if(!$check){
            $data = [
                "user_id"       => $id,
                "followed_id"   => $user_id,
                "followed_date" => Carbon::now()->format('Y-m-d h:i:s'),
            ];

            $save = Follower::create($data);

            if($save){
                return $save;
            }
        }
        
        return false;
    }


    public function getUserFollowedList($id, $request)
    {
        # code...
        $data = User::select(["followers.id", "profiles.picture_path","followers.followed_id"])
                    ->selectRaw( "( select profiles.first_name from profiles where profiles.user_id = followers.followed_id ) as follower_name " )
                    ->leftJoin("profiles", "users.id", "profiles.user_id")
                    ->leftJoin("followers", "users.id","followers.user_id")
                    ->where(["followers.user_id" => $id])
                    ->groupBy("followed_id")
                    ->get();

        if($data){
            return $data;
        }

        return false;
    }

}
