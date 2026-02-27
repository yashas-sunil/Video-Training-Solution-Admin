<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

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
        $credentials['role'] = ['1', '2', '3', '8', '9', '10', '12', '13', '14', '15', '16'];
        return $credentials;
    }

    /**
     * Check if user is active before login
     */
    protected function attemptLogin(Request $request)
    {
    //      $userAgent = $request->header('User-Agent');

    // if (stripos($userAgent, 'Firefox') !== false) {
    //     return false;
    // }
    //     $credentials = $this->credentials($request);

        // Pehle user ko find karte hain
        $user = User::where($this->username(), $request->input($this->username()))->first();

        // Agar user ka status "blocked" hai
        if ($user && strtolower($user->status) === 'blocked') {
            return false;
        }

        // Otherwise normal login attempt
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    /**
     * Handle failed login response.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
         $userAgent = $request->header('User-Agent');

    // ===== FIREFOX BLOCK MESSAGE =====
    // if (stripos($userAgent, 'Firefox') !== false) {
    //     return back()
    //         ->withInput($request->only($this->username(), 'remember'))
    //         ->with('browser_error',
    //             'Login from Mozilla Firefox is not supported. Please login by using Chrome, Edge And Brave .'
    //         );
    // }
        $user = User::where($this->username(), $request->input($this->username()))->first();

        if ($user && strtolower($user->status) === 'blocked') {
            return back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => 'Your account is currently blocked. Please contact the administrator.',
                ]);
        }

        return back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('auth.failed'),
            ]);
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
