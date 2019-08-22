<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

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
        /*
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
        ]);*/
        $http = new Client([
            'base_uri' => config('app.url'),
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $response = $http->post('erp/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'thZERIGFZZCNhBa5ruNBOjOX9rtnltMFlA5UtQJv',
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '*',
            ],
        ]);
        return json_decode((string) $response->getBody(), true);

    }
    protected function authenticated(Request $request, $user)
    {

        //return view('backend.app',['token'=>$token]);
    }
    public function logout(Request $request)
    {
        $user = \Auth::user();
        $user->token()->revoke();
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
