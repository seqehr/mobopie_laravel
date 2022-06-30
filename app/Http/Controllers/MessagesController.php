<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\Message;
use App\Jobs\SendPrivateMessageJob;
use App\FCM;
use App\Jobs\SendPvMessage;
use App\LastSeen;
use App\Models\PrivateChats;
use App\Models\Messages;
use App\Models\User;
use App\Models\MessageFiles;
use App\Models\BlockUser;

use Illuminate\Support\Facades\Storage;

class MessagesController extends Controller
{

    public function SendPrivateMessage(request $req)
    {

        $CheckRecevierTokenExists = User::where('fcm_token', $req->token)->first();


        if (empty($CheckRecevierTokenExists)) {
            return Controller::Response('', false, 'recevier token not found');
        } else if (empty($req->user()->fcm_token)) {
            return Controller::Response('', false, 'sender token not found');
        }
        $data = [
            'title' => $req->user()->name,
            'body' => $req->body,
            'token' => $req->token,
            'sender_id' => $req->user()->id,
            'token2' =>  $req->user()->fcm_token,
            'mode' => $req->mode
        ];

        // Check Have file Or Not
        if ($req->file('input')) {
            $fileName = time() . '_' . $req->file('input')->getClientOriginalName();
            $filepath = Storage::disk('sv')->put('MessageFiles', $req->file('input'));

            $data['attachment'] =  env('DEFAULT_URL') . '/sv/' .  $filepath;
            $data['type'] = $req->type;
            $data['filepath'] =  $filepath;
            FCM::SendPVM($data);
        } else {
            FCM::SendPVM($data);
        }
        $job = SendPrivateMessageJob::dispatch($data);

        return Controller::Response('', true, 'sent');
    }

    public function UserMessages(request $req)
    {
        $newmessages = [];
        $messages = Messages::where('chat_id',  $req->id)->with('user', 'files')->get()->toArray();
        $privatechat = PrivateChats::where('id', $req->id)->get()->first();
        if ($req->user()->id == $privatechat->first_user) {
            $usrid = $privatechat->secound_user;
        } else {
            $usrid = $privatechat->first_user;
        }
        $read = Messages::where('chat_id',  $req->id)->where('user_id',  $usrid)->update([
            'read' => true
        ]);

        foreach ($messages as $message) {
            if ($message['user_id'] == $req->user()->id) {
                $message['isMe'] = true;
            } else {
                $message['isMe'] = false;
            }
            $message['user'] = $message['user']['name'];
            // $message['onChat'] = true;

            if (!empty($message['files'])) {
                $message['type'] = $message['files']['type'];
                $message['input'] =  env('DEFAULT_URL') . '/sv/' . $message['files']['input'];
            }
            unset($message['files']);
            $newmessages[] = $message;
        }
        return response()->json([
            'isDone' => true,
            'data' => $newmessages
        ]);
    }

    public function UserChats(request $req)
    {

        $chats = PrivateChats::where('mode', 'normal')->where('first_user', $req->user()->id)->orwhere('secound_user', $req->user()->id)->get()->toArray();

        $newchats = [];
        $i = 0;
        foreach ($chats as $chat) {
            if ($chat['first_user'] == $req->user()->id) {
                $user = User::find($chat['secound_user'])->toArray();
                $lastseen = LastSeen::ShowLastSeen($user['lastseen']);

                $chat['img'] = env('DEFAULT_URL') . $user['img'];
                $last_mess = Messages::where('chat_id',  $chat['id'])->get()->last()->toArray();
                $not_read = Messages::where('chat_id',  $chat['id'])->where('user_id', $user['id'])->where('read', false)->get()->count();
                // dd($last_mess['body']);
                $lms = $last_mess['body'];
                $chat['last_mssg'] = $lms;
                $chat['name'] = $user['name'];
                $chat['not_read'] = $not_read;
                $isblocked = BlockUser::where('user_id', $req->user()->id)->where('block_id', $user['id'])->get()->first();
                if ($isblocked) {
                    $isblocked = true;
                } else {
                    $isblocked = false;
                }
                $chat['isBlocked'] = $isblocked;
                $lmg = strtotime($last_mess['created_at']);
                $chat['last_mssg_date'] =  $lmg;
                $chat['fcm_token'] = $user['fcm_token'];

                $chat['lastseen'] = $lastseen;

                $newchats[] = $chat;
                $price = array_column($newchats, 'last_mssg_date');
                array_multisort($price, SORT_DESC, $newchats);
            } elseif ($chat['secound_user'] == $req->user()->id) {
                $user = User::find($chat['first_user'])->toArray();

                $lastseen = LastSeen::ShowLastSeen($user['lastseen']);
                $chat['img'] = env('DEFAULT_URL') . $user['img'];
                $last_mess = Messages::where('chat_id',  $chat['id'])->get()->last()->toArray();
                $not_read = Messages::where('chat_id',  $chat['id'])->where('user_id',  $user['id'])->where('read', false)->get()->count();
                // dd($last_mess['body']);
                $lms = $last_mess['body'];
                // dd($last_mess['created_at']);
                $lmg = strtotime($last_mess['created_at']);
                $chat['last_mssg'] = $lms;
                $isblocked = BlockUser::where('user_id', $req->user()->id)->where('block_id', $user['id'])->get()->first();

                if (!empty($isblocked)) {
                    $isblocked = true;
                } else {
                    $isblocked = false;
                }
                $chat['isBlocked'] = $isblocked;
                $chat['lastseen'] = $lastseen;
                $chat['last_mssg_date'] = $lmg;
                $chat['name'] = $user['name'];
                $chat['not_read'] = $not_read;
                $chat['fcm_token'] = $user['fcm_token'];
                $newchats[] = $chat;
                $price = array_column($newchats, 'last_mssg_date');
                array_multisort($price, SORT_DESC, $newchats);
            }
        }
        return response()->json([
            'isDone' => true,
            'data' => $newchats
        ]);
    }
    // public function SeenMessage(request $req)
    // {
    //     $privatechat = PrivateChats::where('id', $req->id)->get()->first();
    //     if ($req->user()->id == $privatechat->first_user) {
    //         $usrid = $privatechat->secound_user;
    //     } else {
    //         $usrid = $privatechat->first_user;
    //     }
    //     $message = Messages::where('user_id', $usrid)->where('')->update(['read' => true]);
    //     return response()->json([
    //         'isDone' => true
    //     ]);
    // }

    public function DeleteChat(request $req)
    {

        $chat = PrivateChats::where('id', $req->id)->delete();
        $messages = Messages::where('chat_id', $req->id)->delete();
        $message_files = MessageFiles::where('chat_id', $req->id)->delete();
        return response()->json([
            'isDone' => true
        ]);
    }

    public function DeleteMessage(request $req)
    {

        $messages = Messages::where('id', $req->id)->delete();
        $message_files = MessageFiles::where('message_id', $req->id)->delete();

        return Controller::Response('', true, 'deleted');
    }


    public function BusinessChats(Request $req)
    {
        $chats = PrivateChats::where('mode', 'business')->where('first_user', $req->user()->id)->orwhere('secound_user', $req->user()->id)->get()->toArray();
        $newchats = [];
        $i = 0;
        foreach ($chats as $chat) {
            if ($chat['first_user'] == $req->user()->id) {
                $user = User::find($chat['secound_user'])->toArray();
                $lastseen = LastSeen::ShowLastSeen($user['lastseen']);

                $chat['img'] = env('DEFAULT_URL') . $user['img'];
                $last_mess = Messages::where('chat_id',  $chat['id'])->get()->last()->toArray();
                $not_read = Messages::where('chat_id',  $chat['id'])->where('user_id', $user['id'])->where('read', false)->get()->count();
                // dd($last_mess['body']);
                $lms = $last_mess['body'];
                $chat['last_mssg'] = $lms;
                $chat['name'] = $user['name'];
                $chat['not_read'] = $not_read;
                $isblocked = BlockUser::where('user_id', $req->user()->id)->where('block_id', $user['id'])->get()->first();
                if ($isblocked) {
                    $isblocked = true;
                } else {
                    $isblocked = false;
                }
                $chat['isBlocked'] = $isblocked;
                $lmg = strtotime($last_mess['created_at']);
                $chat['last_mssg_date'] =  $lmg;
                $chat['fcm_token'] = $user['fcm_token'];

                $chat['lastseen'] = $lastseen;

                $newchats[] = $chat;
                $price = array_column($newchats, 'last_mssg_date');
                array_multisort($price, SORT_DESC, $newchats);
            } elseif ($chat['secound_user'] == $req->user()->id) {
                $user = User::find($chat['first_user'])->toArray();

                $lastseen = LastSeen::ShowLastSeen($user['lastseen']);
                $chat['img'] = env('DEFAULT_URL') . $user['img'];
                $last_mess = Messages::where('chat_id',  $chat['id'])->get()->last()->toArray();
                $not_read = Messages::where('chat_id',  $chat['id'])->where('user_id',  $user['id'])->where('read', false)->get()->count();
                // dd($last_mess['body']);
                $lms = $last_mess['body'];
                // dd($last_mess['created_at']);
                $lmg = strtotime($last_mess['created_at']);
                $chat['last_mssg'] = $lms;
                $isblocked = BlockUser::where('user_id', $req->user()->id)->where('block_id', $user['id'])->get()->first();

                if (!empty($isblocked)) {
                    $isblocked = true;
                } else {
                    $isblocked = false;
                }
                $chat['isBlocked'] = $isblocked;
                $chat['lastseen'] = $lastseen;
                $chat['last_mssg_date'] = $lmg;
                $chat['name'] = $user['name'];
                $chat['not_read'] = $not_read;
                $chat['fcm_token'] = $user['fcm_token'];
                $newchats[] = $chat;
                $price = array_column($newchats, 'last_mssg_date');
                array_multisort($price, SORT_DESC, $newchats);
            }
        }

        return Controller::Response($newchats, true, '');
    }
}
