<?php

namespace App\Http\Services\V1;

use JWTAuth;
use App\Http\Services\Service;
use App\Http\Services\V1\SkillService;
use App\Http\Services\V1\UploadService;
use App\Models\User;
use App\Models\Profile;
use App\Models\Post;
use App\Models\PostSave;
use App\Models\PostComment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostService extends Service
{

    public function create($user_id, $request)
    {   
        $uploadService = new UploadService();

        if($request->header('Authorization'))
        {
            try {
                $user = JWTAuth::parseToken()->authenticate();
                $role = $user->role;
            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                // do whatever you want to do if a token is expired
                return response()->json(['message' => 'Token is Invalid','error'=>true,'data'=>''], 403);
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                // do whatever you want to do if a token is invalid
                return response()->json(['message' => 'Token is Expired','error'=>true,'data'=>''], 403);
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                // do whatever you want to do if a token is not present
                return response()->json(['message' => 'Unauthorized','error'=>true,'data'=>''], 401);
            }

            // Add an image uploader here
            $file_name= 'na';
            $file_data      = $uploadService->uploadPostFile($request);

            if($file_data){
                $file_name = $file_data['filename'];
            }
            // End

            $params = [
                'ip'            => $_SERVER['REMOTE_ADDR'],
                'title'         => $request->title,
                'content'       => $request->content,
                'media'         => $file_name,
                'created_date'  => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            $data = Post::create($params);
            
            if($data){
                return $data;
            }
        }

        return false;
        # code...
    }

    public function list($request)
    {
        # code...
        $domain = config('app.client_base_url') ? config('app.client_base_url') : "127.0.0.1";
        $user = auth()->user();

        $data = Post::select(['posts.*',"profiles.first_name"])
                    ->selectRaw(" '$domain' as domain, if( (select count(*) from followers where user_id = $user->id and followed_id = posts.user_id ) > 0, 1, 0 ) as followed ")
                    ->leftJoin("profiles", "posts.user_id", "profiles.user_id")
                    ->where(['status' => 'active'])
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
        // $data = Post::selectRaw("count(id) as 'count_like'")
        //             ->where(['id' => $id])
        //             ->get();
        
        // if($data){
        //     return $data;
        // }            

        $data = Post::select('likes')
                    ->where(['id' => $id])
                    ->get();

        if($data){
            return $data->likes;
        }

        return false;
    }


    public function countPostComment($id, $request)
    {
        # code...

        $data = Post::select('comment_count')
                    ->where(['id' => $id])
                    ->get();

        if($data){
            return $data->comment_count;
        }


        return false;
    }

}
