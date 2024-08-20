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
            // 各ユーザーに対してランダムに0から5個の出勤記録を作成
            $numAttendances = rand(0, 5); // 0から5までのランダムな整数を生成
            $attendances = Attendance::factory($numAttendances)->create(['user_id' => $user->id]);

            // 各出勤記録に対して休憩を生成（０個または１個）
            $attendances->each(function ($attendance) {
                // 0または1をランダムに生成
                if (rand(0, 1)) {
                    Rest::factory()->create(['attendance_id' => $attendance->id]);
                }
            });
        });
    }
}
