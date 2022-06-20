<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Models\Stories;
use App\Models\User;
use Illuminate\Http\Request;
use DB;

class AdminController extends Controller
{
    public function Dashboard()
    {
        return view('admin.dash');
    }

    public function UsersList(Request $req)
    {
        $users = User::all();
        return response()->json(
            $users
        );
    }

    public function SingleUser(Request $req)
    {
        $user = User::where('name', $req->name)->first();
        return response()->json(
            $user
        );
    }

    public function Posts(Request $req)
    {
        $posts = Posts::with('User')->get()->all();
        return response()->json(
            $posts
        );
    }
    public function Stories(Request $req)
    {
        $stories = Stories::with('User')->get()->all();
        return response()->json(
            $stories
        );
    }

    public function DisableUser(Request $req)
    {
        $user = User::where('id', $req->id)->first();
        if ($user->status == 'active') {
            $user->update([
                'status' => 'deactive'
            ]);
            $tokens = DB::table('personal_access_tokens')->where('name', $user->email)->delete();
        } else {
            $user->update([
                'status' => 'active'
            ]);
        }
        return response()->json(
            ['isDone' => true]
        );
    }
}
