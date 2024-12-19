<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = $this->create($validated);

        // Send email verification notification if the user implements MustVerifyEmail
        if ($user instanceof MustVerifyEmail) {
            $user->sendEmailVerificationNotification();
        }

        return redirect()->intended('attendance');
    }

    protected function create(array $data)
    {
        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Log the user creation
        Log::info('User created: ' . $user->email);

        return $user;
    }
}
