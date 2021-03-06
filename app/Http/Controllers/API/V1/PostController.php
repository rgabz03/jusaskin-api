<?php

namespace App\Http\Controllers\API\V1;

use JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\V1\UserService;
use App\Http\Services\V1\PostService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\DefaultTrait;


class PostController extends Controller
{

    use DefaultTrait;
    //
    public function create($user_id, Request $request)
    {
        # code...
        $postService = new PostService();

        $user_data = $this->user_data($request);

        if($user_data){

            $data   =   $postService->create($user_data->id, $request);

            $error  =   (isset($data['error'])) ? $data['error'] : 200;

            if ($data) {
                $this->message = 'Successfully created data!';
                $this->data  = $data;
            } else {
                $this->message = 'There was an issue creating data. Please try again.';
                $this->error = true;
            }
        }else{
            $error = 400;
        }

        return response()->json($this->getResponse(), $error);
    }

    public function list(Request $request)
    {
        # code...
        $postService = new PostService();
        $data   =   $postService->list($request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully get post list!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue getting post list. Please try again.';
            $this->error = true;
        }

        return response()->json($this->getResponse(), $error);
    }

    public function likePost($id, Request $request)
    {
        # code...
        $postService = new PostService();
        $data   =   $postService->likePost($id);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully liked post!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue liking the post. Please try again.';
            $this->error = true;
        }
    }


    public function getCountPostLike($id, Request $request)
    {
        # code...
        $postService = new PostService();
        $data   =   $postService->countPostLike($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully fetch data!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue fetching the data. Please try again.';
            $this->error = true;
        }
    }


    public function getPostComment($id, Request $request)
    {
        # code...
        $postService = new PostService();
        $data   =   $postService->getPostComment($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully fetch data!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue fetching the data. Please try again.';
            $this->error = true;
        }
    }


    public function countPostComment($id, Request $request)
    {
        # code...
        $postService = new PostService();
        $data   =   $postService->countPostComment($id, $request);

        $error  =   (isset($data['error'])) ? $data['error'] : 200;

        if ($data) {
            $this->message = 'Successfully fetch data!';
            $this->data  = $data;
        } else {
            $this->message = 'There was an issue fetching the data. Please try again.';
            $this->error = true;
        }
    }


}

