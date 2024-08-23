<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        // 2024年の日付を生成するための開始日と終了日を設定
        $startDate = '2024-01-01';
        $endDate = '2024-12-31';

        // 2024年内でランダムな日付を生成
        $date = $this->faker->dateTimeBetween($startDate, $endDate)->format('Y-m-d');

        // ランダムな開始時間を生成
        $startTime = $this->faker->dateTimeBetween("$date 00:00:00", "$date 23:59:59");

        // 開始時間以降でランダムな終了時間を生成
        $endTime = $this->faker->dateTimeBetween($startTime, "$date 23:59:59");

        return [
            'user_id' => User::factory(), // 関連するユーザーを作成
            'date' => $date, // 日付を設定
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
        ];
    }
}
