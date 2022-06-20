<?php

namespace App\Http\Controllers;

use App\Models\FollowingPosts;
use App\Models\PostComments;
use Illuminate\Http\Request;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;
use App\Models\PostTags;
use App\Models\TagPeople;
use App\Models\Tags;
use DB;
use App\Models\PostLikes;
use App\Models\Followers;
use App\Models\SavedPosts;

class PostController extends Controller
{



    public function CreatePost(Request $request)
    {

        // $inputs = json_decode($request->input, true, JSON_UNESCAPED_SLASHES);

        // foreach ($inputs as $in) {
        //     $image_64 = $in;
        //     $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
        //     $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        //     $image = str_replace($replace, '', $image_64);
        //     $image = str_replace(' ', '+', $image);
        //     $imageName = Str::random(10) . '.' . $extension;
        //     $base = base64_decode($in);
        //     $checkupload = Storage::disk('sv')->put($imageName, base64_decode($image));

        //     $paths[] = $imageName;
        // }
        $paths = [];
        if ($request->file('file')) {

            foreach ($request->file('file') as $image) {

                $fileName = time() . '_' . $image->getClientOriginalName();
                $checkupload = Storage::disk('sv')->put('posts', $image);
                $paths[] = $checkupload;
            }
        }

        // $v = Verta();
        $json_paths = json_encode($paths);
        $postdata =   Posts::create([
            'user_id' => $request->user()->id,
            'caption' => $request->caption ?? "",
            'inputs' => $json_paths,
            'lat' => $request->lat,
            'lon' => $request->lon,
            'loc' => $request->loc,



        ]);
        $checkdb = Controller::CheckDB($postdata);
        $arr = explode(' ', $request->caption);
        $arr = $arr;
        foreach ($arr as $tag) {
            if (str_contains($tag, '#')) {
                $tag = ltrim($tag, '#');
                $tagid = Tags::where('title', $tag)->get()->first();

                if (empty($tagid)) {
                    $tagid = Tags::Create([
                        'title' => $tag,
                    ]);
                }

                $post_tags = PostTags::create([
                    'post_id' => $postdata->id,
                    'tag_id' => $tagid->id,
                ]);
            }
        }

        $taged_people = json_decode($request->tag_people, true, JSON_UNESCAPED_SLASHES);
        foreach ($taged_people as $taged_people) {
            $taging = TagPeople::create([
                'user_id'  => $taged_people,
                'post_id' => $postdata->id
            ]);
            $checkdb = Controller::CheckDB($taging);
        }
        if ($checkupload == true and $checkdb == true) {
            $isDone = true;
        } else {
            $isDone = false;
        }
        return response()->json(
            [
                'isDone' => $checkdb,
            ]
        );
    }

    private function getCommentSingle($comments, $data = [])
    {
        foreach ($comments as $comment) {
            $childs = PostComments::where('parent_id', $comment['id'])->with('user:id,name,img')->get()->toArray();
            foreach ($childs as $child) {
                $child['user']['img'] = env('DEFAULT_URL')  . $child['user']['img'];
                $newchilds[] = $child;
            }
            $new = [
                $comment,

            ];
            if ($childs) {
                $comment['children'] = $this->getCommentSingle($newchilds);
            } else {
                $comment['children'] = [];
            }

            $data[] = $comment;
        }
        return $data;
    }

    public function UserPosts(Request $req)
    {
        if (!empty($req->id)) {
            $id = $req->id;
        } else {
            $id = $req->user()->id;
        }
        $posts = Posts::where('user_id', $id)->with('likes')->get();
        $newcomments = [];
        $data = [];
        $output = [];
        foreach ($posts as $post) {

            $inputs =   json_decode($post['inputs']);
            foreach ($inputs as  $input) {
                $type = substr($input, strpos($input, ".") + 1);
                $imgformats = ['png', 'jpg', 'webp', 'gif'];
                $videoformats = ['mp4', 'mkv',];
                if (in_array($type, $imgformats))
                    $type = 'image';
                else if (in_array($type, $videoformats)) {
                    $type = 'video';
                }
                $inp = env('DEFAULT_URL') . '/sv/' . $input;
                $inpt[] = ['url' => $inp, 'type' => $type];
            }
            $post['inputs'] = $inpt;
            $inpt = null;
            $comments = PostComments::where('post_id', $post->id)->whereNull('parent_id')->with('user:id,name,img')->get()->toArray();
            if (!empty($comments)) {
                foreach ($comments as $child) {
                    $child['user']['img'] = env('DEFAULT_URL')  . $child['user']['img'];
                    $newcomments[] = $child;
                }
            } else {
                $newpost['comments'] = [];
            }
            $totallikes = count($post->likes);
            $post['totallikes'] = $totallikes;
            $newpost = $post;
            $isLike = PostLikes::where('post_id', $post->id)->where('user_id',  $req->user()->id)->get()->first();
            if ($isLike) {
                $isLike = true;
            } else {
                $isLike = false;
            }
            $usr = User::find($post['user_id']);
            $newpost['isLike'] = $isLike;
            $newpost['comments'] = $this->getCommentSingle($newcomments, $data);

            $newpost['name'] = $usr['name'];
            $newpost['img'] = env('DEFAULT_URL')  . $usr['img'];


            $output[] = $newpost;
        }


        return response()->json(
            [
                'isDone' => true,
                'data' =>
                $output

            ]
        );
    }

    public function SinglePost(request $request, $id)
    {
        $post = Posts::where('id', $id)->with('likes')->with('user')->get()->first()->toArray();
        $newcomments = [];

        $post['img'] = $post['user']['img'];
        $output = [];


        $inputs =   json_decode($post['inputs']);
        foreach ($inputs as  $input) {
            $inp = env('DEFAULT_URL') . '/sv/' . $input;
            $inpt[] = $inp;
        }
        $post['inputs'] = $inpt;
        $comments = PostComments::where('post_id', $post['id'])->whereNull('parent_id')->with('user:id,name,img')->get()->toArray();
        foreach ($comments as $child) {
            $child['user']['img'] = env('DEFAULT_URL') . '/' . $child['user']['img'];
            $newcomments[] = $child;
        }
        $totallikes = count($post['likes']);
        $post['totallikes'] = $totallikes;
        $newpost = $post;
        $isLike = PostLikes::where('post_id', $post['id'])->where('user_id',  $request->user()->id)->get()->first();
        if ($isLike) {
            $isLike = true;
        } else {
            $isLike = false;
        }
        $newpost['isLike'] = $isLike;
        $newpost['comments'] = $this->getCommentSingle($newcomments, $output);
        $output = $newpost;
        $isDone = Controller::CheckDB($post);
        unset($output["user"]);
        $output['img'] = env('DEFAULT_URL')  . $post['user']['img'];
        $output['username'] = $post['user']['name'];

        return response()->json(
            [
                'isDone' => $isDone,
                'data' => $output
            ]
        );
    }

    public function SearchPostByTitle(request $req)
    {

        $posts = Posts::where('title', 'LIKE', "%{$req->title}%")->get()->all();
        $isDone = Controller::CheckDB($posts);
        return response()->json([
            'isDone' => $isDone,
            'data' => $posts
        ]);
    }
    public function FollowingPosts(request $req)
    {

        // $posts = FollowingPosts::where('following_id', $req->user()->id)->with('posts.likes', 'myposts.likes')->get()->toArray();
        // dd($selfposts);
        // $posts = Followers::where('following_id', $req->user()->id)->join->on('posts', 'followers.follower_id', '=', 'posts.user_id')->join('posts', 'followers.following_id', '=', 'posts.user_id')->get()->toArray();
        // $posts = User::where('id', $req->user()->id)->join('followers', 'users.id', '=', 'followers.following_id')->join(
        //     'posts',
        //     function ($join) {
        //         $join->orOn('followers.following_id', '=', 'posts.user_id')
        //             ->oron('followers.follower_id', '=', 'posts.user_id');
        //     }
        // )->get()->all();
        // dd($posts);
        $followers = FollowingPosts::where('following_id', $req->user()->id)->orderby('created_at', 'desc')->get()->toArray();
        $uid = (string) $req->user()->id;
        $newfollowers = [$uid];

        foreach ($followers as $follower) {
            $newfollowers[] = $follower['follower_id'];
        }
        // dd($newfollowers);
        // dd($newfollowers);
        $posts =  Posts::whereIn('user_id', $newfollowers)->with('likes')->get()->toArray();
        // $posts = Followers::where('following_id', $req->user()->id)->join('posts', 'followers.follower_id', '=', 'posts.user_id')->select('posts.*')->get()->toArray();
        // $selfposts = Posts::where('user_id', $req->user()->id)->get()->toArray();
        // $allposts = array_merge($posts, $selfposts);
        // dd($allposts);
        $output = [];
        $data = [];
        $newcomments = [];
        foreach ($posts as $post) {
            // dd($posts);

            $inputs =   json_decode($post['inputs']);
            foreach ($inputs as  $input) {
                $type = substr($input, strpos($input, ".") + 1);
                $imgformats = ['png', 'jpg', 'webp', 'gif'];
                $videoformats = ['mp4', 'mkv',];
                if (in_array($type, $imgformats))
                    $type = 'image';
                else if (in_array($type, $videoformats)) {
                    $type = 'video';
                }
                $inp = env('DEFAULT_URL') . '/sv/' . $input;
                $inpt[] = ['url' => $inp, 'type' => $type];
            }
            $post['inputs'] = $inpt;
            $inpt = null;
            $comments = PostComments::where('post_id', $post['id'])->whereNull('parent_id')->with('user:id,name,img')->get()->toArray();
            $totallikes = count($post['likes']);
            $newpost = $post;

            $newpost['totallikes'] = $totallikes;
            if ($post['user_id'] == $req->user()->id) {
                $newpost['isMine'] = true;
            } else {
                $newpost['isMine'] = false;
            }
            $newpost['comments'] = [];
            if ($comments) {
                foreach ($comments as $child) {
                    $child['user']['img'] = env('DEFAULT_URL')  . $child['user']['img'];
                    $newcomments[] = $child;
                }
                $newpost['comments'] = $this->getCommentSingle($newcomments, $data);
            } else {
                $newpost['comments'] = [];
            }
            $isLike = PostLikes::where(
                'post_id',
                $post['id']
            )->where('user_id',  $req->user()->id)->get()->first();
            if ($isLike) {
                $isLike = true;
            } else {
                $isLike = false;
            }
            $usr = User::find($post['user_id']);
            $newpost['isLike'] = $isLike;
            $newpost['totalcomments'] = PostComments::where('post_id', $post['id'])->whereNull('parent_id')->with('user:id,name,img')->count();

            $newpost['likes']  = $post['likes'];
            // $newpost['comments'] = [];
            $newpost['isFollowing']  = true;
            $saved = SavedPosts::where('post_id', $post['id'])->where('user_id', $req->user()->id)->get()->first();
            if (empty($saved)) {
                $saved = false;
            } else {
                $saved = true;
            }
            $newpost['isSaved'] = $saved;
            $newpost['name'] = $usr['name'];
            $newpost['img'] = env('DEFAULT_URL')  . $usr['img'];


            $output[] = $newpost;
        }
        $isDone = Controller::CheckDB($posts);
        return response()->json([
            'isDone' => true,
            'data' => $output
        ]);
    }

    public function OtherUserProfiles(request $req)
    {



        $posts = User::find($req->id)->with('posts.likes')->get()->toArray();

        // $selfposts = Posts::where('id', $req->user()->id)->with('posts')->get()->toArray();
        // dd($selfposts);
        $output = [];
        $newcomments = [];
        foreach ($posts as $post) {
            foreach ($post['posts'] as $post) {

                $inputs =   json_decode($post['inputs']);
                foreach ($inputs as  $input) {
                    $inp = env('DEFAULT_URL') . '/sv/' . $input;
                    $inpt[] = $inp;
                }
                $post['inputs'] = $inpt;
                // $comments = PostComments::where('post_id', $post['id'])->whereNull('parent_id')->with('user:id,name,img')->get()->toArray();
                // foreach ($comments as $child) {

                //     $child['user']['img'] = env('DEFAULT_URL')  . $child['user']['img'];
                //     $newcomments[] = $child;
                // }
                $totallikes = count($post['likes']);
                $post['totallikes'] = $totallikes;
                $newpost = $post;
                $isLike = PostLikes::where('post_id', $post['id'])->where('user_id',  $req->user()->id)->get()->first();
                if ($isLike) {
                    $isLike = true;
                } else {
                    $isLike = false;
                }
                $usr = User::find($post['user_id']);
                $newpost['isLike'] = $isLike;
                $newpost['comments'] = null;
                $newpost['name'] = $usr['name'];
                $newpost['img'] = 'http://app.seeuland.com' . $usr['img'];

                // $newpost['comments'] = $this->getCommentSingle($newcomments, $output);
                $output[] = $newpost;
            }
        }
        $isDone = Controller::CheckDB($posts);
        return response()->json([
            'isDone' => $isDone,
            'data' => $output
        ]);
    }

    public function Explore(Request $req)
    {
        $skip = $req->number - 5;
        $posts = Posts::inRandomOrder()->with('likes')->skip($skip)->take($req->number)->get()->toArray();
        $output = [];
        $data = [];
        foreach ($posts as $post) {
            $inputs =   json_decode($post['inputs']);
            foreach ($inputs as  $input) {
                $type = substr($input, strpos($input, ".") + 1);
                $imgformats = ['png', 'jpg', 'webp', 'gif'];
                $videoformats = ['mp4', 'mkv',];
                if (in_array($type, $imgformats))
                    $type = 'image';
                else if (in_array($type, $videoformats)) {
                    $type = 'video';
                }
                $inp = env('DEFAULT_URL') . '/sv/' . $input;
                $inpt[] = ['url' => $inp, 'type' => $type];
            }
            $post['inputs'] = $inpt;
            $inpt = null;
            $comments = PostComments::where('post_id', $post['id'])->whereNull('parent_id')->with('user:id,name,img')->get()->toArray();
            $totallikes = count($post['likes']);
            $newpost = $post;

            $newpost['totallikes'] = $totallikes;
            $newpost['comments'] = [];
            if ($comments) {
                foreach ($comments as $child) {
                    $child['user']['img'] = env('DEFAULT_URL')  . $child['user']['img'];
                    $newcomments[] = $child;
                }
                $newpost['comments'] = $this->getCommentSingle($newcomments, $data);
            } else {
                $newpost['comments'] = [];
            }
            $isLike = PostLikes::where(
                'post_id',
                $post['id']
            )->where('user_id',  $req->user()->id)->get()->first();
            if ($isLike) {
                $isLike = true;
            } else {
                $isLike = false;
            }
            $usr = User::find($post['user_id']);
            $newpost['isLike'] = $isLike;
            $newpost['totalcomments'] = PostComments::where('post_id', $post['id'])->whereNull('parent_id')->with('user:id,name,img')->count();

            $newpost['likes']  = $post['likes'];
            // $newpost['comments'] = [];
            $newpost['isFollowing']  = true;

            $newpost['name'] = $usr['name'];
            $newpost['img'] = env('DEFAULT_URL')  . $usr['img'];


            $output[] = $newpost;
        }
        return response()->json([
            'isDone' => true,
            'data' => $output
        ]);
    }

    public function DeletePost(request $req)
    {

        $post = Posts::where('id', $req->id)->where('user_id', $req->user()->id)->delete();
        $post_tags = PostTags::where('post_id', $req->id)->delete();
        $tag_people = TagPeople::where('post_id', $req->id)->delete();
        $comments = PostComments::where('post_id', $req->id)->delete();
        $likes = PostLikes::where('post_id', $req->id)->delete();

        return response()->json([
            'isDone' => true,
        ]);
    }

    public function CreateSavedPost(request $req)
    {
        $check = SavedPosts::where('post_id', $req->id)->where('user_id', $req->user()->id)->first();
        if (empty($check)) {
            $save = SavedPosts::create([
                'user_id' => $req->user()->id,
                'post_id' => $req->id
            ]);
        } else {
            $check->delete();
        }

        return response()->json([
            'isDone' => true,
        ]);
        //is saved
    }

    public function SavedList(request $req)
    {
        $posts = SavedPosts::where('user_id', $req->user()->id)->with('posts')->get()->toArray();
        foreach ($posts as $post) {
            $ids[] = $post['post_id'];
        }

        $posts =  Posts::whereIn('id', $ids)->with('likes')->get()->toArray();
        // dd($posts);
        $output = [];
        foreach ($posts as $post) {
            // $post = $post['posts'][0];
            // // dd($post['posts'][0]);
            $inputs =   json_decode($post['inputs']);
            foreach ($inputs as  $input) {
                $type = substr($input, strpos($input, ".") + 1);
                $imgformats = ['png', 'jpg', 'webp', 'gif'];
                $videoformats = ['mp4', 'mkv',];
                if (in_array($type, $imgformats))
                    $type = 'image';
                else if (in_array($type, $videoformats)) {
                    $type = 'video';
                }
                $inp = env('DEFAULT_URL') . '/sv/' . $input;
                $inpt[] = ['url' => $inp, 'type' => $type];
            }
            $post['inputs'] = $inpt;
            $inpt = null;
            $comments = PostComments::where('post_id', $post['id'])->whereNull('parent_id')->with('user:id,name,img')->get()->toArray();
            $totallikes = count($post['likes']);
            $newpost = $post;

            $newpost['totallikes'] = $totallikes;
            if ($post['user_id'] == $req->user()->id) {
                $newpost['isMine'] = true;
            } else {
                $newpost['isMine'] = false;
            }
            $newpost['comments'] = [];
            if ($comments) {
                foreach ($comments as $child) {
                    $child['user']['img'] = env('DEFAULT_URL')  . $child['user']['img'];
                    $newcomments[] = $child;
                }
                $newpost['comments'] = $this->getCommentSingle($newcomments, $data);
            } else {
                $newpost['comments'] = [];
            }
            $isLike = PostLikes::where(
                'post_id',
                $post['id']
            )->where('user_id',  $req->user()->id)->get()->first();
            if ($isLike) {
                $isLike = true;
            } else {
                $isLike = false;
            }
            $usr = User::find($post['user_id']);
            $newpost['isLike'] = $isLike;
            $newpost['totalcomments'] = PostComments::where('post_id', $post['id'])->whereNull('parent_id')->with('user:id,name,img')->count();

            $newpost['likes']  = $post['likes'];
            // $newpost['comments'] = [];
            $newpost['isFollowing']  = true;
            $saved = SavedPosts::where('post_id', $post['id'])->where('user_id', $req->user()->id)->get()->first();
            if (empty($saved)) {
                $saved = false;
            } else {
                $saved = true;
            }
            $newpost['isSaved'] = $saved;
            $newpost['name'] = $usr['name'];
            $newpost['img'] = env('DEFAULT_URL')  . $usr['img'];


            $output[] = $newpost;
        }
        return response()->json([
            'isDone' => true,
            'data' =>  $output
        ]);
    }
}
