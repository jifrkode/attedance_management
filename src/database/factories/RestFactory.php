<?php

namespace Database\Factories;

use App\Models\Rest;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestFactory extends Factory
{
    protected $model = Rest::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(), // Attendance ファクトリーを利用して attendance_id を設定
            'start_time' => $this->faker->dateTimeThisMonth(),
            'end_time' => $this->faker->dateTimeThisMonth(),
        ];
    }
}


