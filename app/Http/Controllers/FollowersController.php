<?php

namespace App\Http\Controllers;

use App\Models\Followers;
use Illuminate\Http\Request;
use App\Models\Activity;

class FollowersController extends Controller
{
    public function FollowUser(request $req)
    {
        $v = Verta();
        $flw = Followers::where('following_id', $req->user()->id)->where('follower_id', $req->follower_id)->get()->first();
        if (!empty($flw)) {
            $follow = Followers::where('id', $flw->id)->delete();
            $status = 201;
        } else {
            $follow = Followers::create([
                'follower_id' => $req->follower_id,
                'following_id' => $req->user()->id,
                'status' => false,
                'date' => $v->timestamp
            ]);
            Activity::create([
                'actioner_id' => $req->user()->id,
                'user_id' => $req->follower_id,
                'type' => 'request'
            ]);
            $status = 200;
        }

        $isDone = Controller::CheckDB($follow);
        return response()->json([
            'isDone' => $isDone,
        ], $status);
    }



    public function FollowerCounter(request $req)
    {

        $followers = Followers::where('following_id', $req->id)->with('user')->count();
        $isDone = Controller::CheckDB($followers);
        return response()->json([
            'isDone' => true,
            'data' => $followers
        ]);
    }
    public function FollowingCounter(request $req)
    {

        $followers = Followers::where('follower_id', $req->id)->where('status', 'accepted')->with('user')->count();
        $isDone = Controller::CheckDB($followers);
        return response()->json([
            'isDone' => $isDone,
            'data' => $followers
        ]);
    }


    public function UserFollowing(request $req)
    {
        if (!empty($req->id)) {
            $id = $req->id;
        } else {
            $id = $req->user()->id;
        }
        $followers = Followers::where('following_id', $id)->where('status', true)->with('follower')->get()->makeHidden('status')->all();
        $data = [];
        foreach ($followers as $follower) {

            $follower['follower']['img'] = env('DEFAULT_URL')  . $follower['follower']['img'];
            $followers = [];
            $data[] = $follower['follower'];
        }
        // $isDone = Controller::CheckDB($followers);
        return response()->json([
            'isDone' => true,
            'data' => $data
        ]);
    }

    public function UserFollowers(request $req)
    {
        if (!empty($req->id)) {
            $id = $req->id;
        } else {
            $id = $req->user()->id;
        }
        $followers = Followers::where('follower_id', $id)->where('status', true)->with('following')->get()->makeHidden('status')->all();
        $data = [];
        foreach ($followers as $follower) {
            $follower['following']['img'] = env('DEFAULT_URL')  . $follower['following']['img'];
            $followers = [];
            $data[] = $follower['following'];
        }

        // $isDone = Controller::CheckDB($followers);
        return response()->json([
            'isDone' => true,
            'data' => $data
        ]);
    }
    public function UnfollowUsers(request $req)
    {
        $followers = Followers::where('follower_id', $req->user()->id)->where('following_id', $req->user_id)->with('user')->where('status', true)->delete();
        $isDone = Controller::CheckDB($followers);
        return response()->json([
            'isDone' => $isDone,

        ]);
    }
    public function Pendings(request $req)
    {

        $followers = Followers::where('following_id', $req->user()->id)->where('status', false)->with('follower')->get()->all();

        $isDone = Controller::CheckDB($followers);
        return response()->json([
            'isDone' => $isDone,
            'data' => $followers
        ]);
    }

    public function FollowChangeStatus(request $req)
    {

        $followers = Followers::where('id', $req->id)->first();
        if ($req->status == 'true') {
            $followers->update([
                'status' => true
            ]);
            Activity::create([
                'actioner_id' =>
                $followers->following_id,
                'user_id' => $req->user()->id,
                'type' => 'accept'
            ]);
            if (!empty($req->activity_id)) {
                Activity::where('id', $req->activity_id)->delete();
            }
            return response()->json([
                'isDone' => true

            ]);
        } else {
            $followers->delete();
            return response()->json([
                'isDone' => true

            ]);
        }
    }
}
