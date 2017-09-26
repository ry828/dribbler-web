<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


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
    protected $redirectTo = '/admin/dashboard';

    protected function redirectTo()
    {
        return '/admin/dashboard';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate() {
        $email = Input::get('email');
        $password = Input::get('password');
        $remember = Input::get('remember');

        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required | email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        if (!Auth::attempt(['email' => $email, 'password' => $password, 'role' => 'admin'], $remember)) {
            return redirect()->intended('/login')->withErrors('Email or Password is invalid');
        }

        return redirect()->intended('/admin/dashboard');
    }


}
