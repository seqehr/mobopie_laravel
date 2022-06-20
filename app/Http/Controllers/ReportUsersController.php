<?php

namespace App\Http\Controllers;

use App\Models\ReportUsers;
use Illuminate\Http\Request;

class ReportUsersController extends Controller
{
    public function CreateReport(Request $req)
    {
        $report = ReportUsers::create([
            'user_id' => $req->user()->id,
            'reported_id' => $req->reported_id,
            'category' => $req->category,
            'description' => $req->description
        ]);
        return response()->json([
            'isDone' => true
        ]);
    }
}
