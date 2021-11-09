<?php

namespace App\Http\Services\V1;

use JWTAuth;
use App\Http\Services\Service;
use App\Http\Services\V1\SkillService;
use App\Http\Services\V1\ProfileService;
use App\Models\User;
use App\Models\Message;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class MessageService extends Service
{

    public function getMyMessages($user_id, $request)
    {
        # code...

        $data = Message::select(['profiles.picture_path','messages.id','messages.user_id', 'messages.created_date', 'messages.content'])
                        ->selectRaw("if( messages.status = 'unread', (select count(*) from messages where messages.to_user_id = $user_id and messages.status = 'unread' ), 0 ) as message_count ")
                        ->leftJoin("profiles","messages.user_id","profiles.user_id")
                        ->where(['messages.to_user_id' => $user_id])
                        ->groupBy('messages.user_id')
                        ->orderBy('messages.created_date', 'DESC')
                        ->orderBy('message_count', 'ASC')
                        ->get();
                        // ->toArray();
                        // ->toSql();

                        return $data;

        if($data){
            return $data;
        }

        return false;
    }

    public function getMessageFromUser($id, $user_id)
    {
        # code...
        $data = Message::where(['user_id' => $user_id, 'to_user_id' => $id])
                        ->orWhere(['user_id' => $id, 'to_user_id' => $user_id])
                        ->orderBy('created_date','DESC')
                        ->get();

        if($data){

            if($id != $user_id){
                $this->changeStatustoRead($id, $user_id);
            }

            return $data;
        }

        return false;

    }

    public function getNameFromUserMessage($id, $user_id)
    {
        # code...
        $data = Profile::select(['profiles.first_name', 'users.login_date', 'users.id','profiles.picture_path'])
                        ->leftJoin("users","profiles.user_id", "users.id")
                        ->where(['profiles.user_id' => $user_id])
                        ->get()->toArray();

        if($data){
            return $data;
        }

        return false;

    }

    

    public function changeStatustoRead($to_user_id, $user_id)
    {
        # code...
        $update = Message::where(['status' => 'unread', 'user_id' => $user_id, "to_user_id" => $to_user_id ])->update(['status' => 'read']);

        if($update){
            return $update;
        }

        return false;
    }

    public function sendMessageToUser($id, $request)
    {
        # code...

        if(isset($request->to_user_id) ){
            $data = [
                "status"        => "unread",
                "post_id"       => ( isset($request->post_id) ) ? $request->post_id : 0,
                "content"       => $request->content,
                "to_user_id"    => $request->to_user_id,
                "user_id"       => $id,
                "created_date"  => Carbon::now()->format('Y-m-d h:i:s'),
            ];
            $save = Message::create($data);

            if($save){
                return $save;
            }
        }
        
        return false;
    }
}
