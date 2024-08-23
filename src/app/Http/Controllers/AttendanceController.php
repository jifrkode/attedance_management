<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-users')->only('list');
    }

    public function index(Request $request)
    {
        // セッションから値を取得
        $workstatus = $request->session()->get('workstatus', false);
        $reststatus = $request->session()->get('reststatus', false);

        // 今日の出席データを取得し、ユーザー情報も含める
        $attendances = Attendance::with('user')->whereDate('created_at', Carbon::today())->get();
        $user = Auth::user(); // 現在のログインユーザーを取得

        return view('attendance.index', compact('attendances', 'user', 'workstatus', 'reststatus'));
    }

    public function start(Request $request)
    { 
        Attendance::create([
            'user_id' => auth()->id(),
            'date' => Carbon::today()->format('Y-m-d'),
            'start_time' => Carbon::now(),
        ]);
        
        $request->session()->put([
            'workstatus' => true,
            'reststatus' => false,
        ]);

        return redirect()->route('attendance.index');
    }

    public function end(Request $request)
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('created_at', Carbon::today())
            ->latest('start_time')
            ->first();

        if ($attendance) {
            $attendance->update([
                'end_time' => Carbon::now(),
            ]);
        }

        $request->session()->put('workstatus', false);

        return redirect()->route('attendance.index');
    }


    public function startBreak(Request $request)
    {
        $request->session()->put([
            'workstatus' => true,
            'reststatus' => true,
        ]);

        $attendance = Attendance::where
        ([
            ['user_id', auth()->id()],
            [DB::raw('DATE(created_at)'), Carbon::today()],
        ])->latest('start_time')->first();


        if ($attendance) {
            $rest = Rest::create([
                'attendance_id' => $attendance->id,
                'start_time' => Carbon::now(),
            ]);
        }

        return redirect()->route('attendance.index');
    }

    public function endBreak(Request $request)
    {
        $request->session()->put([
            'workstatus' => true,
            'reststatus' => false,
        ]);

        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', Carbon::today())
            ->latest('start_time')
            ->first();

        if ($attendance) {
            $rest = Rest::where('attendance_id', $attendance->id)
                ->latest('start_time')
                ->first();

            if ($rest && !$rest->end_time) {
                $rest->update([
                    'end_time' => Carbon::now(),
                ]);
                
            }
        }

        return redirect()->route('attendance.index');
    }
    
    //日付一覧ページ

    public function list(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        // 前の日付と次の日付を計算
        $previousDate = \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d');
        $nextDate = \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d');

        // 指定された日付でフィルタリングしてページネーション
        $attendances = Attendance::whereDate('date', $date)->with('rests')->paginate(5);

        // 各 attendance の休憩時間差と勤務時間差を計算
        foreach ($attendances as $attendance) {
            // 休憩時間の合算を初期化
            $totalBreakDuration = 0;

            // 各休憩レコードに対して処理
            foreach ($attendance->rests as $rest) {
                if ($rest->start_time && $rest->end_time) {
                    $totalBreakDuration += $this->calculateDuration($rest->start_time, $rest->end_time);
                }
            }

            // 勤務時間の計算（最初の休憩時間を基に計算）
            if ($attendance->start_time && $attendance->end_time) {
                $totalWorkDuration = $this->calculateDuration($attendance->start_time, $attendance->end_time) - $totalBreakDuration;
                $attendance->formattedWorkDuration = $this->formatDuration($totalWorkDuration);
            } else {
                $attendance->formattedWorkDuration = null;
            }

            // 休憩時間のフォーマット
            $attendance->formattedBreakDuration = $this->formatDuration($totalBreakDuration);
        }

        return view('attendance.list', [
            'attendances' => $attendances,
            'date' => $date,
            'previousDate' => $previousDate,
            'nextDate' => $nextDate,
        ]);
    }

    private function calculateDuration($startTime, $endTime)
    {
        $start = \Carbon\Carbon::parse($startTime);
        $end = \Carbon\Carbon::parse($endTime);
        return $end->diffInSeconds($start); // 秒単位で勤務時間を計算
    }

    private function formatDuration($totalSeconds)
    {
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    // public function showByDate($date)
    // {
    //     $attendances = Attendance::whereDate('created_at', $date)->get();
    //     return view('attendance.show', compact('attendances', 'date'));
    // }
}
