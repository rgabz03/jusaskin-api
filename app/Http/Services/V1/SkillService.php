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
        $skills = UserSkill::select(["user_skills.id","skills.name"])
                            ->leftJoin("skills","user_skills.skill_id","skills.id")
                            ->where(['user_skills.user_id' => $user_id])->get()->toArray();

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


    public function updateUserSkills($user_id, $request)
    {
        # code...
        $convert_interest_ids = [];
        $interest_ids = [];

        $convert = (object) $request->interest;
        foreach($convert as $value => $val){
            $convert_interest_ids[] = $val;
        }
        foreach($convert_interest_ids as $value){
            $data = [
                'user_id'       => $user_id,
                'skill_id'      => $value['value'],
                'created_date'  => Carbon::now()->format('Y-m-d h:i:s'),
            ];

            $insert = UserSkill::create($data);
        }
        return true;
    }


    public function deleteUserSkills($user_id, $skill_id)
    {
        # code...
        
        $data = UserSkill::where(['user_id' => $user_id, 'id'=> $skill_id]);

        if($data->get()){
            UserSkill::where('id', $skill_id)->delete();
            return true;
        }

        return false;
    }

}
