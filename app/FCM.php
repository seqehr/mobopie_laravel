<?php

namespace App;

class FCM
{
    public static function SendPVM($data)
    {
        $fields = array(
            'registration_ids' => [$data['token']],
            "notification" => [
                "title" => $data['title'],
                "body" => $data['body'],
            ],
            "data" => [
                "title" => $data['title'],
                "body" => $data['body'],
                'token2' => $data['token2'],
                'attachment' => $data['attachment'] ?? ' ',
                'type' => $data['type'] ?? ' '
            ]
        );
        $fields = json_encode($fields);
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key=' . "AAAAV3Ao31g:APA91bGnr03w_1PplPHvfFTlNe9oY6p5Cs9PehdbcEYreiBfw8_v1377zI2QlSL_tc5eU6Kh9FoOFwsSm9XgP5LsQUB8fGH8hmnUYH3hyGRfxmuccWzJ_bGAJZHg9E9Z7JwY13wC7lPM",
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);
        return true;
    }
}
