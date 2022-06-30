<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// use App\FCM;
use App\Models\PrivateChats;
use App\Models\Messages;
use App\Models\User;
use App\Models\MessageFiles;

class SendPrivateMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $this->fail();
        $data = $this->data;

        $recevier_id = User::where('fcm_token', $data['token'])->get()->first();
        $recevier_id = $recevier_id->id;
        $chat = PrivateChats::where([
            ['first_user', '=', $recevier_id],
            [
                'secound_user', '=', $data['sender_id']
            ],
        ])->orwhere([
            ['first_user', '=', $data['sender_id']],
            [
                'secound_user', '=', $recevier_id
            ],
        ])->where('mode', $data['mode'])->get()->first();
        if (!empty($chat)) {
            $chat_id = $chat->id;
        } else {
            $newchat = PrivateChats::create([
                'first_user' => $data['sender_id'],
                'secound_user' => $recevier_id,
                'mode' => $data['mode'],
            ]);
            $chat_id = $newchat['id'];
        }
        $message = Messages::create([
            'user_id' => $data['sender_id'],
            'chat_id' => $chat_id,
            'body' => $data['body'],
            'read' => false
        ]);
        if (!empty($data['filepath'])) {
            $multimedia = MessageFiles::create([
                'user_id' => $data['sender_id'],
                'chat_id' => $chat_id,
                'input' => $data['filepath'],
                'message_id' => $message->id,
                'type' => $data['type']
            ]);
        }
    }
}
