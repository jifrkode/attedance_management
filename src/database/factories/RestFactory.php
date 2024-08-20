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
        // Create an instance of Attendance to get start_time and end_time
        $attendance = Attendance::factory()->create();

        // Generate start_time and end_time for Rest within the range of Attendance times
        $start_time = $this->faker->dateTimeBetween($attendance->start_time, $attendance->end_time);
        $end_time = $this->faker->dateTimeBetween($start_time, $attendance->end_time);

        return [
            'attendance_id' => $attendance->id,
            'start_time' => $start_time->format('Y-m-d H:i:s'),
            'end_time' => $end_time->format('Y-m-d H:i:s'),
        ];
    }
}
