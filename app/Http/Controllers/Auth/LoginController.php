<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    protected function redirectTo()
    {
        return '/admin';
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function login(Request $request){
        //dd($request);
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'errors'=>$validator->errors(),
            ]);
        }
        if($this->guard()->attempt($this->credentials($request)))
        {
            $user = \Auth::user();
            $token = hash('sha256',$user->id);
            $user->forceFill([
                'api_token' => $token,
            ])->save();
            return response()->json([
                'message'=>'success',
                'token' => $token
            ]);
        }
        return response()->json([
            'errors'=>[
                'password'=>'Wrong credentials'
            ]
        ]);
    }
    protected function authenticated(Request $request, $user)
    {

        //return view('backend.app',['token'=>$token]);
    }
    public function logout(Request $request)
    {
        $user = \Auth::user();
        //$user->api_token = hash('sha256', 'olakka');
        //$user->save();
        //$request->session()->invalidate();
        return response()->json(['message'=>'success']);
        //return $this->loggedOut($request) ?: redirect('/login');
        //return redirect('/login');
    }
    public function loggedOut(Request $request)
    {

        return redirect('/login');
    }
}
