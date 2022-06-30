<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLocReq;
use App\Http\Requests\UserSearchReq;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Followers;
use App\Models\Posts;
use App\Models\PostComments;
use App\Models\PostLikes;
use App\Models\Messages;
use App\Models\PrivateChats;
use App\Models\VitrinPhotos;
use App\Models\BlockUser;

class UserController extends Controller
{
    public function GetUserList()
    {
        $users = User::paginate(10);

        return view('admin.users', compact('users'));
    }


    public function PostByUsers()
    {
        $posts = Posts::with('user')->paginate(0);

        return

            view('admin.getposts', compact('posts'));
    }

    public function GetEditUser($id)
    {
        $user = User::find($id);
        return view('admin.edituser', compact('user'));
    }

    public function PostEditUser(request $req)
    {
        $user = User::find($req->id)->update([
            'name' => $req->name,
            'email' => $req->email,
        ]);
        return redirect()->back()->with('mssg', 'Action Successed');
    }

    public function PostDisableUser($id)
    {
        $user = User::find($id);
        if ($user->status == 'disable') {
            $user = User::find($id)->update([
                'status' => 'enable'
            ]);
        } else {
            $user = User::find($id)->update([
                'status' => 'disable'
            ]);
        }
        return redirect()->back()->with('mssg', 'Action Successed');
    }

    public function UserSearch(request $req, UserSearchReq $valid)
    {
        $newusers = [];
        $searchTerm = $req->name;
        $users = User::where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('fname', 'LIKE', "%{$searchTerm}%")
            ->orWhere('lname', 'LIKE', "%{$searchTerm}%")
            ->get()->makeHidden('status')->toArray();


        foreach ($users as $user) {
            $isblocked = BlockUser::where('user_id', $req->user()->id)->where('block_id', $user['id'])->get()->first();
            if (empty($isblocked)) {
                $user['img'] = env('DEFAULT_URL')  . $user['img'];
                $followed = Followers::where('following_id', $req->user()->id)->where('follower_id', $user['id'])->get()->toArray();
                if (!empty($followed)) {
                    $followed = true;
                } else {
                    $followed = false;
                }
                $user['isFollowing'] = $followed;
                // $messages = PrivateChats::where('first_user' ,   $user['id'] )->where('secound_user' ,  $req->user()->id  )->get()->first();
                //  $messages= PrivateChats::where(['first_user' => $user['id'] ,'secound_user' => $req->user()->id ])->orwhere(['first_user' => $req->user()->id ,'secound_user' => $user['id']  ])->get()->first();
                $messages = PrivateChats::where([
                    ['first_user', '=', $user['id']],
                    ['secound_user', '=', $req->user()->id]
                ])->orwhere([
                    ['first_user', '=', $req->user()->id],
                    ['secound_user', '=', $user['id']]

                ])->get()->first();

                if (!empty($messages)) {
                    $id = $messages['id'];
                } else {
                    $id = null;
                }
                if ($user['page'] == true) {
                    $page = true;
                } else {
                    $page = false;
                }
                $user['page'] = $page;
                $user['chat_id'] = $id;
                $newusers[] = $user;
            }
        }
        $isDone = Controller::CheckDB($users);
        return response()->json([
            'isDone' => $isDone,
            'data' => $newusers
        ]);
    }

    public function UserFollowers(request $req)
    {

        $followers = Followers::where('following_id', $req->user()->id)->with('user')->get()->all();
        $isDone = Controller::CheckDB($followers);
        return response()->json([
            'isDone' => $isDone,
            'data' => $followers
        ]);
    }

    public function NearBy(request $req)
    {
        $newusers = [];
        if ($req->user()->lat and $req->user()->lon) {
            $users = User::select("users.*", \DB::raw("6371 * acos(cos(radians(" . $req->user()->lat . "))
     * cos(radians(users.lat))
     * cos(radians(users.lon) - radians(" . $req->user()->lon . "))
     + sin(radians(" . $req->user()->lat . "))
     * sin(radians(users.lat)))*1000 AS distance"))
                ->having('distance', '<', 50)->where('id', '!=', $req->user()->id)
                ->get()->toArray();

            foreach ($users as $user) {
                $str =  $user['distance'];
                $ip = explode('.', $str);
                $dis =  $ip[0] . "." . substr($ip[1], 0, 1);
                $user['img'] = env('DEFAULT_URL')  . $user['img'];
                $user['bg'] = env('DEFAULT_URL')  . $user['bg'];
                $user['distance'] = $dis;
                $age = time() - strtotime($user['birthday']);
                $age = date_diff(date_create($user['birthday']), date_create('now'))->y;
                $user['age'] = $age;
                $vitrin = VitrinPhotos::where('user_id', $user['id'])->get()->all();
                $user['vitrin'] = $vitrin;
                $newusers[] = $user;
            }
        } else {
            $users = [];
        }

        return Controller::Response($newusers, true, '');
    }
    public function Updateloc(Request $req, UpdateLocReq $valid)
    {
        $user = User::where('id', $req->user()->id)->update([
            'lat' => $valid->lat,
            'lon' => $valid->lon
        ]);
        if (!$user) {
            return Controller::Response('', false, 'something went wrong');
        }
        return Controller::Response('', true, 'updated');
    }

    public function CreateFcm(Request $req)
    {
        $user = User::find($req->user()->id)->update([
            'fcm_token' => $req->token
        ]);
        return response()->json([
            'isDone' => true,
        ]);
    }
}
