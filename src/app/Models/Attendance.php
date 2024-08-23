<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
    ];

    // Attendance.php
    public function rests()
    {
        return $this->hasMany(Rest::class, 'attendance_id'); // 'attendance_id' は外部キー
    }

    /**
     * ユーザーへのリレーションシップ
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
