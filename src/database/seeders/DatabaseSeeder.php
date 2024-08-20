<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ユーザーを100人作成
        User::factory(100)->create()->each(function ($user) {
            // 各ユーザーに対して出勤記録を作成
            $attendances = Attendance::factory(5)->create(['user_id' => $user->id]);

            // 各出勤記録に対して休憩を作成
            $attendances->each(function ($attendance) {
                Rest::factory(3)->create(['attendance_id' => $attendance->id]);
            });
        });
    }
}
