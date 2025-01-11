<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'workstatus',
        'reststatus'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Attendanceとのリレーションシップ
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    //勤務状況の管理
    public function getWorkstatusAttribute($value)
    {
        return (bool) $value;
    }

    public function getReststatusAttribute($value)
    {
        return (bool) $value;
    }

    // ミューテータ
    public function setWorkstatusAttribute($value)
    {
        $this->attributes['workstatus'] = (bool) $value;
    }

    public function setReststatusAttribute($value)
    {
        $this->attributes['reststatus'] = (bool) $value;
    }
}
