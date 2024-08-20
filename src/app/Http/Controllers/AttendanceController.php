<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::whereDate('created_at', Carbon::today())->get();
        return view('attendance.index', compact('attendances'));
    }

    public function start(Request $request)
    {
        $attendance = new Attendance();
        $attendance->user_id = auth()->id();
        $attendance->start_time = Carbon::now();
        $attendance->save();

        return redirect()->route('attendance.index');
    }

    public function end(Request $request)
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('created_at', Carbon::today())
            ->latest('start_time')
            ->first();
        
        if ($attendance) {
            $attendance->end_time = Carbon::now();
            $attendance->save();
        }

        return redirect()->route('attendance.index');
    }

    public function startBreak(Request $request)
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('created_at', Carbon::today())
            ->latest('start_time')
            ->first();

        if ($attendance) {
            $attendance->break_start = Carbon::now();
            $attendance->save();
        }

        return redirect()->route('attendance.index');
    }

    public function endBreak(Request $request)
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('created_at', Carbon::today())
            ->latest('start_time')
            ->first();

        if ($attendance) {
            $attendance->break_end = Carbon::now();
            $attendance->save();
        }

        return redirect()->route('attendance.index');
    }

    public function showByDate($date)
    {
        $attendances = Attendance::whereDate('created_at', $date)->get();
        return view('attendance.show', compact('attendances', 'date'));
    }
}
