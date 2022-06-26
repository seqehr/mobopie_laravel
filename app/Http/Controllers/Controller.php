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

    public function Response($data, $isDone, $message)
    {
        return response()->json([
            'isDone' => $isDone,
            'data' => $data,
            'message' => $message
        ]);
    }
}
