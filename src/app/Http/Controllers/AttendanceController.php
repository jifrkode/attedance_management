<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-users')->only('list');
    }

    // 勤怠管理ページの表示
    public function index(Request $request)
    {
        $user = Auth::user(); // 現在のログインユーザーを取得

        // 今日の日付
        $today = Carbon::today();

        // セッションに今日のログインフラグが設定されているか確認
        $hasLoggedInToday = $request->session()->get('logged_in_today', false);

        // 今日の最初のログイン時にのみリセット処理を実行
        if (!$hasLoggedInToday) {
            // セッションに今日ログイン済みフラグを設定
            $request->session()->put('logged_in_today', true);

            // 前日の勤務記録を取得
            $previousDayAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today->copy()->subDay()) // 前日の日付
                ->latest('start_time')
                ->first();

            // 前日の勤務が終了していない場合、workstatus と reststatus をリセット
            if ($previousDayAttendance && !$previousDayAttendance->end_time) {
                User::where('id', $user->id)->update([
                    'workstatus' => false,
                    'reststatus' => false,
                ]);
            }
        }

        // データベースからユーザーの状態を取得
        $workstatus = $user->workstatus;
        $reststatus = $user->reststatus;

        // 今日の出席データを取得し、ユーザー情報も含める
        $attendances = Attendance::with('user')
            ->whereDate('created_at', Carbon::today())
            ->get();

        return view('attendance.index', compact('attendances', 'user', 'workstatus', 'reststatus'));
    }

    // 勤務開始
    public function start(Request $request)
    {
        $user = Auth::user();

        // 勤務開始を記録
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->format('Y-m-d'),
            'start_time' => Carbon::now(),
        ]);

        // データベースのユーザー情報を更新
        User::where('id', $user->id)->update([
            'workstatus' => true,
            'reststatus' => false,
        ]);

        return redirect()->route('attendance.index');
    }

    public function end(Request $request)
    {
        $user = Auth::user();

        // 今日の出勤記録を取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->latest('start_time')
            ->first();

        if ($attendance) {
            $attendance->end_time = Carbon::now();
            $attendance->save(); // `save` メソッドが使えない場合は、クエリビルダで更新
        }

        // データベースのユーザー情報を更新
        User::where('id', $user->id)->update([
            'workstatus' => false,
        ]);

        return redirect()->route('attendance.index');
    }

    public function startBreak()
    {
        $user = Auth::user();

        // 今日の出勤記録を取得
        $attendance = Attendance::where([
            ['user_id', $user->id],
            [DB::raw('DATE(created_at)'), Carbon::today()],
        ])->latest('start_time')->first();

        if ($attendance) {
            // 休憩開始を記録
            Rest::create([
                'attendance_id' => $attendance->id,
                'start_time' => Carbon::now(),
            ]);

            // データベースのユーザー情報を更新
            User::where('id', $user->id)->update([
                'workstatus' => true,
                'reststatus' => true,
            ]);
        }

        return redirect()->route('attendance.index');
    }

    public function endBreak()
    {
        $user = Auth::user();

        // 今日の出勤記録を取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->latest('start_time')
            ->first();

        if ($attendance) {
            // 最新の休憩記録を取得
            $rest = Rest::where('attendance_id', $attendance->id)
                ->latest('start_time')
                ->first();

            if ($rest && !$rest->end_time) {
                $rest->end_time = Carbon::now();
                $rest->save(); // `save` メソッドが使えない場合は、クエリビルダで更新
            }

            // データベースのユーザー情報を更新
            User::where('id', $user->id)->update([
                'workstatus' => true,
                'reststatus' => false,
            ]);
        }

        return redirect()->route('attendance.index');
    }

    //日付一覧ページ
    public function dayslist(Request $request)
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

        return view('attendance.dayslist', [
            'attendances' => $attendances,
            'date' => $date,
            'previousDate' => $previousDate,
            'nextDate' => $nextDate,
        ]);
    }

    //勤務者一覧ページ
    public function userslist(Request $request)
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

        return view('attendance.userslist', [
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
}
