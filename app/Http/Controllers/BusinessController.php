<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBusinessReq;
use App\Http\Requests\FollowBusinessReq;
use App\Http\Requests\CreateBusinessPostReq;
use App\Http\Requests\UpdateBusinessProfieReq;
use App\Models\Business;
use App\Models\BusinessFollowers;
use App\Models\BusinessPosts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Storage;
use App\Models\BusinessCats;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    public function AddBusiness(Request $req, AddBusinessReq $valid)
    {
        $CheckExists = Business::where('user_id', $req->user()->id)->first();
        if (empty($CheckExists)) {

            $img = Storage::disk('sv')->put('business_img', $valid->file('img'));
            $bg = Storage::disk('sv')->put('business_bg', $valid->file('bg'));

            $Business = Business::create([
                'name' => $valid->name,
                'user_id' => $req->user()->id,
                'bio' => $valid->bio,
                'img' => $img,
                'bg' => $bg,
                'lat' => $valid->lat,
                'lon' => $valid->lon,
                'cat_id' => $valid->cat_id,
            ]);
            return Controller::Response('', true, "created");
        } else {
            return Controller::Response('', false, "duplicate");
        }
    }

    public function CreatePosts(Request $req, CreateBusinessPostReq $valid)
    {
        $inputs = [];
        foreach ($valid->file('inputs') as $input) {
            $Newinput = Storage::disk('sv')->put('business_posts', $input);
            $inputs[] = $Newinput;
        }
        $inputs = json_encode($inputs);
        $CreatePosts = BusinessPosts::create([
            'user_id' => $req->user()->id,
            'title' => $valid->title,
            'caption' => $valid->caption,
            'category' => $valid->category,
            'inputs' => $inputs,
            'price' => $valid->price,
            'offer' => $valid->offer,
            'link' => $valid->link,
        ]);
        return Controller::Response('', true, "created");
    }

    public function FollowABusiness(Request $req, FollowBusinessReq $valid)
    {
        // return $req->user()->id;
        $uid = $req->user()->id;
        BusinessFollowers::create([
            'follower_id' =>  $uid,
            'business_id' => $valid->business_id
        ]);
        return Controller::Response('', true, 'created');
    }
    public function BusinessProfile(Request $req)
    {
        if (empty($req->user_id)) {

            $Business = Business::find($req->user()->id)->with('posts')->first()->toArray();
            $flwers = BusinessFollowers::where('business_id', $req->user()->id);
            $Business['totalfollowers'] = $flwers->count();
            $Business['ratingcount'] = 40; //need dymanic
            $Business['ratingvalue'] = 4.5; //need dymanic
            $Business['followers'] = $flwers->get()->all();


            return Controller::Response($Business, true, '');
        } else {
            $Business = Business::find($req->user_id)->with('posts')->first()->toArray();
            $flwers = BusinessFollowers::where('business_id', $req->user_id);
            $Business['totalfollowers'] = $flwers->count();
            $Business['ratingcount'] = 40; //need dymanic
            $Business['ratingvalue'] = 4.5; //need dymanic
            $Business['followers'] = $flwers->with('user')->get()->all();
            return Controller::Response($Business, true, '');
        }
    }
    public function UpdateBusinessProfile(Request $req, UpdateBusinessProfieReq $valid)
    {

        $changes = [];
        $changes = $valid->all();

        if (!empty($valid->file('img'))) {
            $img = Storage::disk('sv')->put('business_img', $valid->file('img'));
            $changes['img'] = $img;
        }

        if (!empty($valid->file('bg'))) {
            $bg = Storage::disk('sv')->put('business_bg', $valid->file('bg'));
            $changes['bg'] = $bg;
        }

        $changes['user_id'] = $req->user()->id;

        // dd($changes);
        // dd($valid->all());
        $Update = Business::find($req->user()->id)->update($changes);
        return Controller::Response('', true, 'updated');
    }

    public function BusinessCats()
    {
        $BusinessCats = BusinessCats::whereNull('parent_id')->with('children')->get();
        return Controller::Response($BusinessCats, true, '');
    }
}
