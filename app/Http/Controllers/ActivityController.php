<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\Followers;

class ActivityController extends Controller
{
    public function Activites(Request $req)
    {

        $data = [];
        $ac = Activity::where('user_id', $req->user()->id)->with('user')->get();
        foreach ($ac as $activity) {
            $followed = Followers::where('following_id', $activity->actioner_id)->where('follower_id', $req->user()->id)->where('status', false)->first();
            if (!empty($followed)) {
                $activity['idtofollow'] = $followed->id;
                $followed = true;
            } else {
                $activity['idtofollow'] = $followed;
                $followed = false;
            }
            $activity['isFollowing'] = $followed;

            $data[] = $activity;
        }
        return response()->json([
            'isDone' => true,
            'data' => $data
        ]);
    }
}
