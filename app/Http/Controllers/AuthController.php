<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use App\Jobs\ForgetEmailJob;
use App\Jobs\SendVerifyEmail;
use App\Models\VerifyCode;
use Auth;
use Hekmatinasser\Verta\Verta;

class AuthController extends Controller
{

    public function requestToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'isDone' => false,
            ]);
        }
        if ($user->status == 'deactive') {
            return response()->json([
                'isDone' => false,
                'data' => 'deactived'
            ]);
        }
        return response()->json([
            'isDone' => true,
            'data' => [
                'token'  => $user->createToken($request->email)->plainTextToken
            ]
        ]);
    }
    public function CheckEmail(Request $request)
    {
        $user = User::where('email', $request->email)->get()->first();
        if (empty($user)) {
            return Controller::Response('', true, 'free');
        } else {
            return Controller::Response('', false, 'taken');
        }
    }
    public function CheckUsername(request $request)
    {
        $user = User::where('name', $request->name)->get()->first();
        if (empty($user)) {
            return Controller::Response('', true, 'free');
        } else {
            return Controller::Response('', false, 'taken');
        }
    }
    public function SendVEmail(request $request)
    {
        $user = User::where('email', $request->email)->get()->first();

        $code = mt_rand(100000, 999999);
        if (empty($user)) {

            $vcode = VerifyCode::where('email', $request->email)->get()->first();
            $details = [
                'title' => 'Your Verifcation Code SeeuLand',
                'body' => $vcode->code ?? $code
            ];
            $emaildata = [
                'email' => $request->email,
                'details' => $details
            ];
            SendVerifyEmail::dispatch($emaildata);
            if (empty($vcode)) {
                VerifyCode::create([
                    'code' => $vcode->code ?? $code,
                    'email' => $request->email
                ]);
            }
            // Session::put('data', [
            //     'email' => $request->email,
            //     'password' => $request->password,
            //     'code' => $code
            // ]);
            $emailstatus = SendVerifyEmail::dispatchSync($emaildata);
            if ($emailstatus == 'failed') {
                $isDone  = false;
                $code = 0;
            } else {
                $isDone  = true;
            }
        } else {
            $isDone  = false;
            $code = 0;
        }
        $code = $vcode ?? $code;
        return response()->json([
            'isDone' => $isDone,
            'data' => [
                'code' => $code->code ?? $code
            ]
        ]);
    }
    public function VerifyCode(request $req)
    {
        $data = VerifyCode::where('email', $req->email)->get()->first();

        if ($req->code == $data->code) {
            $status = true;
            return response()->json([
                'isDone' => $status,
            ]);
        } else {
            $status = false;
            return response()->json([
                'isDone' => $status,
            ]);
        }
    }
    public function Register(request $req)
    {
        $date = Verta();
        $checkusr = User::where('name', $req->name)->get()->first();
        if (empty($checkusr)) {
            $usr = User::create([
                'name' => $req->name,
                'bg' => '/sv/img/def.png',
                'email' => $req->email,
                'password' => hash::make($req->password),
                'date' => $date->timestamp,
                'img' => '/sv/img/def.png',
                'status' => 'active',
                'fcm_token' => $req->fcm_token,
                'bio' => ' ',
                'fname' => ' ',
                'lname' => ' ',
                'region' => ' ',
                'gender' => ' ',
                'title' => ' ',
                'birthday' => ' ',
            ]);
            $user = User::find($usr->id);
            $token = $user->createToken($req->email)->plainTextToken;
            $status = true;
        } else {
            $status = false;
        }
        return response()->json([
            'isDone' => $status,
            'data' => [
                'token' => $token
            ],
        ]);
    }

    public function ForgetPass(Request $req)
    {
        $code = mt_rand(100000, 999999);
        $details = [
            'title' => 'Your Verifcation Code SeeuLand',
            'body' => $code
        ];
        $emaildata = [
            'email' => $req->email,
            'details' => $details
        ];
        ForgetEmailJob::dispatch($emaildata);
        $emailstatus = ForgetEmailJob::dispatchSync($emaildata);
        if ($emailstatus == 'failed') {
            $status = false;
        } else {
            $status = true;
        }
        return response()->json([
            'isDone' => $status,
        ]);
    }

    public function ChangePass(request $req)
    {
        $user = User::find($req->user()->id)->first();

        if (Hash::check($req->current_password, $user->password)) {
            $user = User::find($req->user()->id)->update([
                'password' => hash::make($req->password)
            ]);
            return response()->json([
                'isDone' => true,

            ]);
        } else {
            return response()->json([
                'isDone' => false,
            ]);
        }
    }
    public function GetAdminLogin()
    {
        if (auth::user()) {
            return redirect('/admin/dashboard');
        }
        return view('admin.login');
    }
    public function PostAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('/admin/dashboard')
                ->withSuccess('Signed in');
        }
        return redirect("/admin/login")->withSuccess('Login details are not valid');
    }

    public function AdminLogOut()
    {
        Session::flush();
        Auth::logout();
        return Redirect('/admin/login');
    }

    public function SearchMaps(Request $request)
    {

        $param = 'q=' . $request->all()['query'] . '&format=jsonv2';
        // $content = file_get_contents("https://nominatim.openstreetmap.org/search.php?" . $param);

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, 'https://nominatim.openstreetmap.org/search.php?' . $param);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
        $query = curl_exec($curl_handle);
        curl_close($curl_handle);
        $data  = json_decode($query);
        return response()->json([
            'isDone' => true,
            'data' => $data
        ]);
    }
    public function weblogin(Request $req)
    {
        if (!Auth::attempt(['email' => $req->phone, 'password' => $req->password])) {
            return response([
                'message' => 'invalid'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = Auth::User();
        $token = $user->createToken('token')->plainTextToken;
        $cookie = cookie('jwt', $token, 60 * 24);
        return response([
            'jwt' => $token
        ])->withCookie($cookie);
    }
    public function user()
    {
        return Auth::user();
    }
    public function logout()
    {
        $cookie = Cookie::forget('jwt');
        return response([
            'message' => 'success'
        ])->withCookie($cookie);
    }

    public function NotLogin(request $req)
    {
        return Controller::Response('', false, "login failed");
    }
}
