<?php

namespace App\Http\Controllers;

use App\Models\PostTags;
use App\Models\Tags;
use App\Models\Posts;
use Illuminate\Http\Request;

class PostTagsController extends Controller
{
    public function PostTags()
    {
        $tags = PostTags::all();
        $isDone = Controller::CheckDB($tags);
        return response()->json([
            'isDone' => $isDone,
            'data' => $tags
        ]);
    }

    public function SearchTags(request $req)
    {
        $searchString = $req->title;
        $tags = PostTags::whereHas('tags', function ($query) use ($searchString) {
            $query->where('title', 'like', '%' . $searchString . '%');
        })
            ->with(['tags' => function ($query) use ($searchString) {
                $query->where('title', 'like', '%' . $searchString . '%');
            }])->get();
        $isDone = Controller::CheckDB($tags);
        return response()->json([
            'isDone' => $isDone,
            'data' => $tags
        ]);
    }

    public function SearchOnlyTags(request $req)
    {

        $tags = Tags::where('title', 'LIKE', "%{$req->title}%")->get()->all();
        $isDone = Controller::CheckDB($tags);
        return response()->json([
            'isDone' => $isDone,
            'data' => $tags
        ]);
    }
}
