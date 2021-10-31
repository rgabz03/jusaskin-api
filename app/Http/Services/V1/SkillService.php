<?php

namespace App\Http\Services\V1;

use JWTAuth;
use App\Http\Services\Service;
use App\Models\User;
use App\Models\Skill;
use App\Models\UserSkill;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SkillService extends Service
{
    public function getUserSkills($user_id)
    {
        # code...
        $skills = UserSkill::where(['user_id' => $user_id])->get()->toArray();

        if($skills){
            return $skills;
        }

        return false;
    }


    public function list()
    {
        # code...
        $skills = Skill::where(['status' => 'enable'])
                    ->whereRaw(" parent != 0 ")
                    ->get()->toArray();

        if($skills){
            return $skills;
        }

        return false;

    }
}
