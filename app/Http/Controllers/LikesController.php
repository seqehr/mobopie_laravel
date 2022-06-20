<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\PostLikes;

class LikesController extends Controller
{
    public function CreateLike(Request $req)
    {
        $like = PostLikes::where('post_id', $req->post_id)->where('user_id',  $req->user()->id)->get()->first();
        if ($like) {
            PostLikes::find($like->id)->delete();
            $status = false;
        } else {
            PostLikes::create([
                'user_id' => $req->user()->id,
                'post_id' => $req->post_id
            ]);
            $checkActivty = Activity::where('actioner_id', $req->user()->id)->where('user_id', $req->user_id)->where('post_id', $req->post_id)->first();
            if (empty($checkActivty)) {
                Activity::create([
                    'actioner_id' => $req->user()->id,
                    'user_id' => $req->user_id,
                    'post_id' => $req->post_id,
                    'type' => 'like'
                ]);
            } else {
                $checkActivty->delete();
                Activity::create([
                    'actioner_id' => $req->user()->id,
                    'user_id' => $req->user_id,
                    'post_id' => $req->post_id,
                    'type' => 'like'
                ]);
            }

            $status = true;
        }
        return response()->json([
            'isDone' => true,
            'data' => [
                'isLike' => $status
            ]
        ]);
    }
}
