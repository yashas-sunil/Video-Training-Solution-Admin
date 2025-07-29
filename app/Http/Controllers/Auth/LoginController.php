<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Modify credentials to include role check.
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['role'] = ['1','2','3','8','9','10','12','13','14','15','16'];
        return $credentials;
    }

    /**
     * Redirect after login based on role.
     */
protected function authenticated(Request $request, $user)
{
    if ($user->role == 2) {
        return redirect()->route('user.dashboard');
    }

    

    return redirect()->intended($this->redirectTo);
}

}
