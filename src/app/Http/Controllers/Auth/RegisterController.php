<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $this->create($validated);

            if ($user instanceof MustVerifyEmail) {
                $user->sendEmailVerificationNotification();
            }

            return redirect()->intended('attendance')->with('success', '登録が完了しました。');
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => '登録中に問題が発生しました。もう一度お試しください。']);
        }
    }

    protected function create(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);

                Log::info('User created successfully.', ['email' => $user->email, 'id' => $user->id]);
                return $user;
            });
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
