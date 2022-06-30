<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePassReq;
use App\Http\Requests\CheckEmailReq;
use App\Http\Requests\CheckNameReq;
use App\Http\Requests\ForgetPassReq;
use App\Http\Requests\LoginReq;
use App\Http\Requests\RegisterReq;
use App\Http\Requests\SendEmailReq;
use App\Http\Requests\VerifyCodeReq;
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

    public function requestToken(Request $request, LoginReq $valid)
    {


        $user = User::where('email', $valid->email)->first();

        if (!$user || !Hash::check($valid->password, $user->password)) {
            return Controller::Response('', false, 'wrong password');
        }
        if ($user->status == 'deactive') {
            return Controller::Response('', false, 'deactived User');
        }
        return Controller::Response(['token' => $user->createToken($valid->email)->plainTextToken], true, '');
    }
    public function CheckEmail(Request $request, CheckEmailReq $valid)
    {
        $user = User::where('email', $valid->email)->get()->first();
        if (empty($user)) {
            return Controller::Response('', true, 'free');
        } else {
            return Controller::Response('', false, 'taken');
        }
    }
    public function CheckUsername(request $request, CheckNameReq $valid)
    {
        $user = User::where('name', $valid->name)->get()->first();
        if (empty($user)) {
            return Controller::Response('', true, 'free');
        } else {
            return Controller::Response('', false, 'taken');
        }
    }
    public function SendVEmail(request $request, SendEmailReq $valid)
    {
        $user = User::where('email', $valid->email)->get()->first();
        if (empty($user)) {
            $code = (string) mt_rand(100000, 999999);
            if (empty($user)) {

                $vcode = VerifyCode::where('email', $valid->email)->get()->first();
                $details = [
                    'title' => 'Your Verifcation Code SeeuLand',
                    'body' => $vcode->code ?? $code
                ];
                $emaildata = [
                    'email' => $valid->email,
                    'details' => $details
                ];
                SendVerifyEmail::dispatch($emaildata);
                if (empty($vcode)) {
                    VerifyCode::create([
                        'code' => $vcode->code ?? $code,
                        'email' => $valid->email
                    ]);
                }

                $emailstatus = SendVerifyEmail::dispatchSync($emaildata);
                if ($emailstatus == 'failed') {
                    return Controller::Response('', false, 'email failed to send');
                }
            }
            $code = $vcode ?? $code;
            return Controller::Response(['code' => $code->code ?? $code], true, 'sent');
        } else {
            return Controller::Response('', false, 'duplicate');
        }
    }
    public function VerifyCode(request $req, VerifyCodeReq $valid)
    {
        $code = VerifyCode::where('email', $valid->email)->get()->first();
        if (empty($code)) {
            return Controller::Response('', false, 'email or code not found');
        }
        $isDone = false;
        $message = 'wrong credentials';
        $data = null;

        if ($valid->code == $code->code) {
            $isDone = true;
            $message = 'validated';
            $data = null;
        }
        return Controller::Response($data, $isDone, $message);
    }
    public function Register(request $req, RegisterReq $valid)
    {
        $code = VerifyCode::where('email', $valid->email)->get()->first();
        if (empty($code)) {
            return Controller::Response('', false, 'email or code not found');
        }
        if ($valid->code == $code->code) {
            $date = Verta();
            $checkusr = User::where('name', $valid->name)->get()->first();
            if (empty($checkusr)) {
                $usr = User::create([
                    'name' => $valid->name,
                    'bg' => '/sv/img/def.png',
                    'email' => $valid->email,
                    'password' => hash::make($valid->password),
                    'date' => $date->timestamp,
                    'img' => '/sv/img/def.png',
                    'status' => 'active',
                    'fcm_token' => $valid->fcm_token,
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

                return Controller::Response(['token' => $token], true, 'success');
            } else {
                return Controller::Response('', false, 'duplicate');
            }
        } else {
            return Controller::Response('', false, 'wrong credentials');
        }
    }

    public function ForgetPass(Request $req, ForgetPassReq $valid)
    {
        $code = mt_rand(100000, 999999);
        $details = [
            'title' => 'Your Verification Code MoboPie',
            'body' => $code
        ];
        $emaildata = [
            'email' => $valid->email,
            'details' => $details
        ];
        ForgetEmailJob::dispatch($emaildata);
        $emailstatus = ForgetEmailJob::dispatchSync($emaildata);
        if ($emailstatus == 'failed') {
            return Controller::Response('', false, '');
        }
        return Controller::Response('', true, '');
    }

    public function ChangePass(request $req, ChangePassReq $valid)
    {
        $user = User::find($req->user()->id)->first();

        if (Hash::check($valid->current_password, $user->password)) {
            $user = User::find($req->user()->id)->update([
                'password' => hash::make($valid->password)
            ]);
            return Controller::Response('', true, 'updated');
        } else {
            return Controller::Response('', false, 'something wrong');
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

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, 'https://nominatim.openstreetmap.org/search.php?' . $param);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
        $query = curl_exec($curl_handle);
        curl_close($curl_handle);
        $data  = json_decode($query);
        return Controller::Response($data, true, '');
    }

    public function NotLogin(request $req)
    {
        return Controller::Response('', false, "login failed");
    }
}
