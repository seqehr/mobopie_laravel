<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Fcm;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static function isDone($db)
    {
        if ($db) {
            return true;
        } else {
            return false;
        }
    }

    public static function Status($db, $data)
    {
        if ($db) {
            return response()->json([
                'isDone' => true,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'isDone' => true,
                'data' => $data
            ]);
        }
    }

    public static function CheckDB($db)
    {
        if ($db) {
            return true;
        } else {
            return false;
        }
    }

    public function SendPush(request $req)
    {

        $topic = 'a-topic';
        $url = 'https://fcm.googleapis.com/fcm/send';
        // $to = 'cHXoFt86SRyNN4sTVASn8h:APA91bHq7cjDFwBazMinQWoWoNCbsm2dShJkJdMqr4cG8TLLiEcUf9I-cBr2hUzeP9GVhA2dUmMnbwDpjtXAzicXk25RvDpHnK3odIRhwOJqpF4SIBXMatHPbaudNhX89ncD0j8Mx0EB';
        $to = $req->token;
        // $fields = array(
        //     'to' => $to,
        //     'data' => ["test" => "hi this is nabi"],
        // );

        $fields = array(
            'registration_ids' => [$to],
            "notification" => [
                "title" => $req->title,
                "body" => $req->body,
            ],
            "data" => [
                "title" => $req->title,
                "body" => $req->body,
            ]
        );
        $fields = json_encode($fields);

        $headers = array(
            'Authorization: key=' . "AAAAVHOCCyk:APA91bEH55k6jCGUmu1qZb0nlo5ZUgyv1xc4uqaiIlQr4BAcJoEFwnSwjKoDZv2Xo9Q-bYfNcJnmcZHHr6WOfd_uOfYcQi39f7VdP7DemL61pymJ3UY_A1EPEdk-FZ4fe2kqr7M4Z92l ",
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        echo $result;
        curl_close($ch);
    }
}
