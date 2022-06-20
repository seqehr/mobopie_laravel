<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostComments;

class CommentsController extends Controller
{
    public function CreateComment(request $req)
    {
        PostComments::create([
            'post_id' => $req->post_id,
            'user_id' => $req->user()->id,
            'parent_id' => $req->parent,
            'text' => $req->text,

        ]);
        return response()->json([
            'isDone' => true,
        ]);
    }
}
