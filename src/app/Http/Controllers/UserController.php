<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $attendances = Attendance::where('user_id', $user->id)->get();
        return view('users.show', compact('user', 'attendances'));
    }
}
