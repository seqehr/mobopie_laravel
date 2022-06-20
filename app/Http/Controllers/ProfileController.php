<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Followers;
use App\Models\UserLanguages;
use App\Models\UserWorks;
use App\Models\VitrinPhotos;
use App\Models\BlockUser;
use App\Models\PrivateChats;

class ProfileController extends Controller
{
    public function CompeleteProfile(request $req)
    {

        if ($req->works) {
            $deleteworks = UserWorks::where('user_id', $req->user()->id)->forcedelete();
            $works = json_decode($req->works, true, JSON_UNESCAPED_SLASHES);
            foreach ($works as $work) {
                $checkwork = UserWorks::where('user_id', $req->user()->id)->where('name', $work)->first();
                if (empty($checkwork)) {
                    $addworks = UserWorks::create([
                        'name' => $work,
                        'user_id' => $req->user()->id,
                        'default' => false
                    ]);
                }
            }
        }
        if ($req->languages) {
            $deletelangs = UserLanguages::where('user_id', $req->user()->id)->forcedelete();
            $works = json_decode($req->languages, true, JSON_UNESCAPED_SLASHES);
            foreach ($works as $work) {
                $addworks = UserLanguages::create([
                    'name' => $work,
                    'user_id' => $req->user()->id,
                    'default' => false
                ]);
            }
        }
        if ($req->file('vit')) {

            foreach ($req->file('vit') as $image) {

                $fileName = time() . '_' . $image->getClientOriginalName();
                $checkupload = Storage::disk('sv')->put('vitrin', $image);
                VitrinPhotos::create([
                    'img' => $checkupload,
                    'user_id' => $req->user()->id,
                    'defalut' => false
                ]);
            }
        }
        if ($req->img and $req->bg) {
            $images = [$req->img, $req->bg];
            foreach ($images as $in) {
                $image_64 = $in;
                $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
                $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
                $image = str_replace($replace, '', $image_64);
                $image = str_replace(' ', '+', $image);
                $imageName = Str::random(10) . '.' . $extension;
                $base = base64_decode($in);
                $checkupload = Storage::disk('sv')->put($imageName, base64_decode($image));

                $paths[] = '/sv/' . $imageName;
            }
        }
        if (User::where('id', $req->user()->id)->update([
            'name' => $req->name ?? "",
            'img' => $paths[0] ?? "",
            'bg' => $paths[1] ?? "",
            'bio' => $req->bio,
            'gender' => $req->gender,
            'region' => $req->region,
            'birthday' => $req->birthday,
            'fname' => $req->fname,
            'lname' => $req->lname,
            'height' => $req->height,
            'language' => $req->language,
            'relationship' => $req->relationship,
            'sex' => $req->sex,
            'religion' => $req->religion,
            'personality' => $req->personality,
            'education' => $req->education,
            'work' => $req->work
        ])) {
            return response()->json([
                'isDone' => true
            ]);
        } else {
            return response()->json([
                'isDone' => false
            ]);
        }
    }
    public function UserProfile(Request $req)
    {

        if (!empty($req->id)) {
            $id = $req->id;
        } else {
            $id = $req->user()->id;
        }
        $data = User::where('id', $id)->get()->makeHidden(['status', 'date', 'level', 'remember_token', 'email_verified_at'])->first()->toArray();

        $followed = Followers::where('following_id', $req->user()->id)->where('follower_id', $data['id'])->get()->toArray();
        if (!empty($followed)) {
            $followed = true;
        } else {
            $followed = false;
        }
        $isblocked = BlockUser::where('user_id', $req->user()->id)->where('block_id', $req->id)->get()->first();
        if ($isblocked) {
            $isblocked = true;
        } else {
            $isblocked = false;
        }
        $data['isBlocked'] = $isblocked;
        $data['isFollowing'] = $followed;
        if ($data['page'] == true) {
            $page = true;
        } else {
            $page = false;
        }
        $data['page'] = $page;

        $messages = PrivateChats::where([
            ['first_user', '=', $data['id']],
            ['secound_user', '=', $req->user()->id]
        ])->orwhere([
            ['first_user', '=', $req->user()->id],
            ['secound_user', '=', $data['id']]

        ])->get()->first();

        if (!empty($messages)) {
            $cid = $messages['id'];
        } else {
            $cid = null;
        }
        $data['chat_id'] = $cid;
        $data['img'] = env('DEFAULT_URL')  . $data['img'];
        $data['bg'] = env('DEFAULT_URL') . $data['bg'];
        return response()->json(
            [
                'isDone' => true,
                'data' => $data
            ]
        );
    }

    public function RegionSearch(request $request)
    {

        // $param = 'q=' . $request->all()['query'];

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, 'https://restcountries.com/v3.1/all');
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
        $query = curl_exec($curl_handle);
        curl_close($curl_handle);
        $data  = json_decode($query);


        foreach ($data as $data) {
            $what[] = $data->name->common;
        }

        return response()->json([
            'isDone' => true,
            'data' => $what
        ]);
    }

    public function createVitrinPhotos(request $req)
    {

        if ($req->file('img')) {
            if ($req->default == 'true') {
                $default = true;
            } else {
                $default = false;
            }
            foreach ($req->file('img') as $image) {

                $VitrinPhotos = VitrinPhotos::where('id', $req->id)->get()->first();
                if (!empty($VitrinPhotos)) {
                    $fileName = time() . '_' . $image->getClientOriginalName();
                    $checkupload = Storage::disk('sv')->put('vitrin', $image);
                    $VitrinPhotos->update([
                        'img' => $checkupload,
                        'defalut' => $default
                    ]);
                } else {
                    $fileName = time() . '_' . $image->getClientOriginalName();
                    $checkupload = Storage::disk('sv')->put('vitrin', $image);
                    VitrinPhotos::create([
                        'img' => $checkupload,
                        'user_id' => $req->user()->id,
                        'defalut' => $default
                    ]);
                }
            }
        }
        return response()->json([
            'isDone' => true,

        ]);
    }
    public function UserVitrin(request $req)
    {
        $photos = VitrinPhotos::where('user_id', $req->user()->id)->take(5)->get()->toArray();
        return response()->json([
            'isDone' => true,
            'data' => $photos
        ]);
    }

    public function CreateLanguage(request $req)
    {
        $languages = json_decode($req->languages, true);
        foreach ($languages as $lang) {
            $ulang = UserLanguages::where('name', $lang->name)->where('user_id', $lang->user()->id)->first();
            if (empty($ulang)) {
                $createlang = UserLanguages::create([
                    'name' => $lang->name,
                    'user_id' => $req->user()->id
                ]);
            }
        }
    }
}
