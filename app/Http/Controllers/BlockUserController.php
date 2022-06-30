<?php

namespace App\Http\Controllers;

use App\Models\BlockUser;
use App\Models\Followers;
use Illuminate\Http\Request;

class BlockUserController extends Controller
{
    public function Create(request $req)
    {
        $isblocked = BlockUser::where('user_id', $req->user()->id)->where('block_id', $req->id)->get()->first();
        if ($isblocked) {
            $isblocked->delete();
            return response()->json([
                'isDone' => false
            ]);
        } else {
            $block = BlockUser::create([
                'user_id' => $req->user()->id,
                'block_id' => $req->id
            ]);
            $flw = Followers::where('following_id', $req->id)->where('follower_id', $req->user()->id)->delete();
            return response()->json([
                'isDone' => true
            ]);
        }
    }

    public function BlockList(request $req)
    {

        $list = BlockUser::where('user_id', $req->user()->id)->with('user')->get()->all();
        if (empty($list)) {
            return Controller::Response('', false, 'empty');
        }
        return Controller::Response($list, true, '');
    }
}
