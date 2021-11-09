<?php

namespace App\Http\Services\V1;

use JWTAuth;
use App\Http\Services\Service;
use App\Http\Services\V1\SkillService;
use App\Http\Services\V1\ProfileService;
use App\Http\Services\V1\FollowerService;
use App\Models\User;
use App\Models\Profile;
use App\Models\PostSave;
use App\Models\Follower;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserService extends Service
{

    public function find($id)
    {
        # code...
        $data = User::findOrFail($id);

        return $data;
    }

    public function create($request)
    {
        # code...

        $limit = $this->registrationPerDayLimit(100);

        if($limit > 100){
            return ['error' => 409];
        }

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


    public function checkEmailExist($email)
    {
        # code...
        $data = User::where('username', $email)->first();

        if($data){
            return $data;
        }

        return false;
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


    public function getUserFollower($user_id)
    {
        # code...
        $followerService = new FollowerService();

        $data = $followerService->getUserFollower($user_id);

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

    public function getUserSavedPost($user_id, $request)
    {
        # code...
        $data = PostSave::select(['posts.*'])
                        ->leftJoin("posts", "posts.id", "post_save.post_id")
                        ->where(['post_save.user_id' => $user_id])
                        ->get();

        if($data){
            return $data;
        }

        return false;
    }


    public function registrationPerDayLimit($limit)
    {
        # code...

        // $now = date('Y-m-d')." 00:00:00";
        $now = "2021-01-01 00:00:00";
        $data = User::select("id")
                    ->whereRaw(" created_date between ('$now') and (NOW())")
                    ->limit($limit)
                    ->count();
        if($data){
            return $data;
        }

        return false;
    }

    public function updateProfile($id, $request)
    {
        # code...

        $user_data = $this->find($id);

        if($user_data){

            // check email if exist
            $checkEmailExist = $this->checkEmailExist($request->username);
            
            if($checkEmailExist && $user_data->username != $request->username){
                return ['error' => 422];
            }

            $username_update = User::where('id', $id)->update(['username' => $request->username]);

            $data = Profile::where('user_id', $id)->update($request->only('first_name','last_name','location'));

            if($data){
                return $data;
            }
        }

        return false;
    }



    public function recieveNotification($id, $request)
    {
        # code...

        $user_data = $this->find($id);

        if($user_data){

            $data = User::where('id', $id)->update(['notification' => $request->notification]);

            if($data){
                return $data;
            }
        }

        return false;
    }


    public function updateDescription($id, $request)
    {
        # code...

        $user_data = $this->find($id);

        if($user_data){

            $data = Profile::where('user_id', $id)->update(['description' => $request->description]);

            if($data){
                return $data;
            }
        }

        return false;
    }


    public function updateProfession($id, $request)
    {
        # code...

        $user_data = $this->find($id);

        if($user_data){

            $data = Profile::where('user_id', $id)->update(['job' => $request->profession]);

            if($data){
                return $data;
            }
        }

        return false;
    }


    public function list($request)
    {
        # code...
        $keyword = (isset($request->keyword)) ? $request->keyword : '';

        $data = User::select(["profiles.first_name","profiles.last_name", "profiles.job", "users.id"])
                    ->selectRaw(" count( followers.user_id ) as followers_count, count( posts.user_id ) as posts_count  ")
                    ->leftJoin("profiles","users.id","profiles.user_id")
                    ->leftJoin("followers","users.id","followers.user_id")
                    ->leftJoin("posts","users.id","posts.user_id")
                    ->where(['users.status' => 'active'])
                    ->whereRaw(" (profiles.first_name like '%$keyword%' or profiles.last_name like '%$keyword%' ) ")
                    ->groupBy("users.id")
                    ->get();

        if($data){
            return $data;
        }

        return false;

    }

    public function checkIfYouFollowedUser($your_id, $user_id)
    {
        # code...
        $data = Follower::where(['user_id' => $your_id, "follower_id" => $user_id])->first();

        if($data){
            return ['followed' => true];
        }

        return false;
    }
}
