<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'note',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function modificationRequests()
    {
        return $this->hasMany(ModificationRequest::class);
    }

    public function activeBreak()
    {
        return $this->breaks()->whereNull('end_time')->first();
    }

    public function totalBreakTime()
    {
        $totalMinutes = 0;
        foreach ($this->breaks as $break) {
            if ($break->end_time) {
                $start = strtotime($break->start_time);
                $end = strtotime($break->end_time);
                $totalMinutes += ($end - $start) / 60;
            } elseif ($this->status === 'break') {
                $start = strtotime($break->start_time);
                $now = strtotime(now()->format('H:i:s'));
                $totalMinutes += ($now - $start) / 60;
            }
        }
        return max(0, $totalMinutes);
    }

    public function workingHours()
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        $start = strtotime($this->check_in);
        $end = strtotime($this->check_out);
        $totalMinutes = ($end - $start) / 60;

        return $totalMinutes - $this->totalBreakTime();
    }
}
