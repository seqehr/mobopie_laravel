<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStoryReq;
use App\Http\Requests\DeleteStoryReq;
use Illuminate\Http\Request;
use App\Models\Stories;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\StoryViews;
use App\Models\FollowingPosts;
use App\Models\User;
use App\LastSeen;
use Hekmatinasser\Verta\Verta;

class StoriesController extends Controller
{
    public function CreateStory(request $req, CreateStoryReq $valid)
    {
        if (!empty($valid->file('input'))) {

            $fileName = time() . '_' . $valid->file('input')->getClientOriginalName();
            $checkupload = Storage::disk('sv')->put('stories', $valid->file('input'));

            $db = Stories::create([
                'user_id' => $req->user()->id,
                'input' => $checkupload,
            ]);

            return Controller::Response('', true, 'created');
        } else {
            return Controller::Response('', false, 'some error');
        }
    }

    public function UserStories(request $req)
    {

        $stories = Stories::where('user_id', $req->id)->get()->toArray();
        return response()->json(
            [
                'isDone' => true,
                'data' => $stories
            ]
        );
    }


    public function OtherUserStories(request $req)
    {
        $newmystory = [];
        $mystories = Stories::where('user_id', $req->user()->id)->get()->toArray();
        $sd = [];
        if (!empty($mystories)) {
            foreach ($mystories as $mystroy) {
                $date = strtotime($mystroy['created_at']);
                if (time() < $date + (24 * 60 * 60)) {

                    $type = substr($mystroy['input'], strpos($mystroy['input'], ".") + 1);
                    $mystroy['input'] = env('DEFAULT_URL') . '/sv/' . $mystroy['input'];
                    $views = StoryViews::where('story_id', $mystroy['id'])->get()->count();
                    $mystroy['views'] = $views;
                    $imgformats = ['png', 'jpg', 'webp', 'gif'];
                    $videoformats = ['mp4', 'mkv',];
                    $sendtime = LastSeen::ShowLastSeen($date);
                    $mystroy['date'] = $sendtime;
                    if (in_array($type, $imgformats))
                        $mystroy['type'] = 'image';
                    else if (in_array($type, $videoformats)) {
                        $mystroy['type'] = 'video';
                    }
                    $sd[] = $mystroy ?? ' ';
                }
            }
        }
        $newmystory[] = ['user_id' => $req->user()->id, 'name' => $req->user()->name, 'img' => env('DEFAULT_URL') . $req->user()->img, 'stories' =>  $sd];

        $stories = FollowingPosts::where('following_id', $req->user()->id)->with('stories')->get()->makehidden(['id', 'following_id', 'status', 'date'])->toArray();

        $str = [];

        foreach ($stories  as $story) {

            $usr = User::find($story['follower_id']);
            $sm = [];
            if ($story['stories']) {
                foreach ($story['stories'] as $singlestory) {
                    $date = strtotime($singlestory['created_at']);
                    $sendtime = LastSeen::ShowLastSeen($date);
                    $singlestory['date'] = $sendtime;
                    if (time() < $date + (24 * 60 * 60)) {
                        $view = StoryViews::where('story_id', $singlestory['id'])->first();
                        if (empty($view)) {
                            StoryViews::create([
                                'story_id' => $singlestory['id'],
                                'user_id' => $req->user()->id
                            ]);
                        }
                        $type = substr($singlestory['input'], strpos($singlestory['input'], ".") + 1);
                        $imgformats = ['png', 'jpg', 'webp', 'gif'];
                        $videoformats = ['mp4', 'mkv',];

                        if (in_array($type, $imgformats))
                            $singlestory['type'] = 'image';
                        else if (in_array($type, $videoformats)) {
                            $singlestory['type'] = 'video';
                        }
                        $singlestory['input'] = env('DEFAULT_URL') . '/sv/' . $singlestory['input'];
                        $views = StoryViews::where('story_id', $singlestory['id'])->get()->count();
                        $singlestory['views'] = $views;
                        $sm[] = $singlestory;
                    }
                }
                $last_story = end($sm);
                $last_story_date = strtotime($last_story['created_at']);
                $str[] = [
                    'user_id' => $usr->id,
                    'name' => $usr->name,
                    'img' => env('DEFAULT_URL') . $usr->img,
                    'stories' =>  $sm,
                    'last_stroy_date' =>  $last_story_date,
                ];
            }
        }

        $lsdc = array_column($str, 'last_stroy_date');
        array_multisort($lsdc, SORT_DESC, $str);

        $str = array_merge($newmystory, $str);

        // dd($str);
        $isDone = Controller::isDone($stories);
        return response()->json(
            [
                'isDone' => true,
                'data' =>  $str
            ]
        );
    }

    public function StoryViews(request $req)
    {

        $db = StoryViews::where('story_id', $req->story_id)->get()->all();
        $checkdb = Controller::CheckDB($db);

        return response()->json([
            'isDone' => $checkdb,
            'data' => $db
        ]);
    }
    public function DeleteStory(request $req, DeleteStoryReq $valid)
    {
        $story = Stories::where('id', $valid->id)->where('user_id', $req->user()->id)->delete();
        if ($story) {
            $story_views = StoryViews::where('story_id', $valid->id)->delete();
            return Controller::Response('', true, 'deleted');
        } else {
            return Controller::Response('', false, 'some error');
        }
    }
}
