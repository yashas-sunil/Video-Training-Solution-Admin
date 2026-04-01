<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Override Laravel default resetPassword
     * (login ko remove kar diya)
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));

        // ❌ yeh line Laravel me hoti hai:
        // $this->guard()->login($user);
    }

    /**
     * Reset ke baad hamesha login page
     */
    protected function redirectTo()
    {
        return '/login';
    }
}