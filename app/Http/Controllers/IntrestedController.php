<?php

namespace App\Http\Controllers;

use App\Models\IntrestedCats;
use App\Models\IntrestedTags;
use App\Models\IntrestedUserTags;
use App\Models\LikePeople;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PrivateChats;

class IntrestedController extends Controller
{
    public function CreateIntrest(Request $req)
    {
        $tags = json_decode($req->tag, true);
        foreach ($tags as $singletag) {

            $thetag = IntrestedTags::where('tag', $singletag['name'])->where('cat_id', $singletag['cat'])->first();
            if (empty($thetag)) {
                $tag = IntrestedTags::create([
                    'cat_id' => $singletag['cat'],
                    'tag' => $singletag['name']
                ]);
                $usertag = IntrestedUserTags::create([
                    'user_id' => $req->user()->id,
                    'tag_id' => $tag->id,
                    'cat_id' => $singletag['cat']
                ]);
            } else {
                $checkuser = IntrestedUserTags::where('user_id', $req->user()->id)->where('tag_id', $thetag->id)->first();
                if (empty($checkuser)) {
                    $usertag = IntrestedUserTags::create([
                        'user_id' => $req->user()->id,
                        'tag_id' => $thetag->id,
                        'cat_id' => $thetag->cat_id
                    ]);
                }
            }
        }
        return response()->json([
            'isDone' => true,
        ]);
    }
    public function Userintrests(Request $req)
    {
        $intrests = IntrestedUserTags::where('user_id', $req->user()->id)->get()->all();
        if (!empty($intrests)) {
            return Controller::Response($intrests, true, '');
        } else {
            return Controller::Response('', false, 'empty');
        }
    }
    public function IntrestCats()
    {
        $cats = IntrestedCats::get()->all();

        if (!empty($cats)) {
            return Controller::Response($cats, true, '');
        } else {
            return Controller::Response('', false, 'empty');
        }
    }
    public function IntrestTags(request $req)
    {
        $tags = IntrestedTags::where('cat_id', $req->id)->get();
        return response()->json([
            'isDone' => true,
            'data' =>  $tags
        ]);
    }
    public function CreateLike(request $req)
    {
        $likepeople = LikePeople::where([
            ['first_user', '=', $req->user()->id],
            [
                'secound_user', '=', $req->user_id
            ],
        ])->orwhere([
            ['first_user', '=', $req->user()->id],
            [
                'secound_user', '=', $req->user_id
            ],
        ])->get()->first();
        if (!empty($likepeople)) {

            if ($likepeople->first_user == $req->user()->id) {
                $likepeople->update([
                    'flike' => true,
                ]);
            } else {
                $likepeople->update([
                    'slike' => true,
                ]);
            }
        } else {
            $likepeople = LikePeople::create([
                'first_user' => $req->user()->id,
                'secound_user' =>  $req->user_id,
                'flike' => true,
                'slike' => false,
            ]);
        }
        return response()->json([
            'isDone' => true,

        ]);
    }

    public function LikedPeople(request $req)
    {
        $newusers = [];
        $users = LikePeople::where('first_user', '=', $req->user()->id)->where('flike', true)->where('slike', true)->orwhere('secound_user', '=', $req->user()->id)->get()->all();
        // dd($users);
        foreach ($users as $user) {

            if ($user->first_user == $req->user()->id) {

                $usr = User::where('id', $user->secound_user)->get()->first();
            } else {
                $usr = User::where('id', $user->first_user)->get()->first();
            }
            $messages = PrivateChats::where([
                ['first_user', '=', $usr->id],
                ['secound_user', '=', $req->user()->id]
            ])->orwhere([
                ['first_user', '=', $req->user()->id],
                ['secound_user', '=', $usr->id]

            ])->get()->first();

            if (!empty($messages)) {
                $id = $messages['id'];
            } else {
                $id = null;
            }
            $newuser = $usr;
            $newuser['chat_id'] = $id;
            $newuser['img'] = env('DEFAULT_URL')  . $usr['img'];
            $newusers[] = $newuser;
        }
        return response()->json([
            'isDone' => true,
            'data' => $newusers
        ]);
    }
}
