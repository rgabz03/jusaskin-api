<?php

namespace App\Http\Services\V1;

use JWTAuth;
use App\Http\Services\Service;
use App\Http\Services\V1\SkillService;
use App\Models\User;
use App\Models\Profile;
use App\Models\Post;
use App\Models\PostComment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PostService extends Service
{
    public function list($request)
    {
        # code...
        $data = Post::where(['status' => 'active'])
                    ->orderBy( 'created_date', 'desc')
                    ->get();

        if($data){
            return $data;
        }

        return false;

    }



    public function getPostComment($post_id, $request)
    {
        # code...
        $data = PostComment::where(['status' => 'active', 'post_id' => $post_id])
                            ->orderBy( 'created_date', 'desc')
                            ->get();

        if($data){
            return $data;
        }

        return false;
    }


    public function getPostLike($post_id)
    {
        # code...
        $data = Post::select('likes')
                    ->where(['status' => 'active', 'post_id' => $post_id])
                    ->get();

        if($data){
            return $data;
        }

        return false;
    }


    public function likePost($post_id)
    {
        # code...
        $data = tap(Post::where(['status' => 'active', 'post_id' => $post_id]))
                    ->update(['likes' => 'likes + 1']);

        if($data){
            return $data;
        }

        return false;
    }


    public function countPostLike($id, $request)
    {
        # code...
        $data = Post::selectRaw("count(id) as 'count_like'")
                    ->where(['id' => $id])
                    ->get();
        
        if($data){
            return $data;
        }

        return false;
    }


    public function countPostComment($id, $request)
    {
        # code...
        $data = PostComment::selectRaw("count(id) as 'count_comment'")
                    ->where(['post_id' => $id])
                    ->get();
        
        if($data){
            return $data;
        }

        return false;
    }
}
