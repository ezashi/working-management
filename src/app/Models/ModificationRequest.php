<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModificationRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ModificationRequestFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'attendance_id',
        'user_id',
        'modified_check_in',
        'modified_check_out',
        'modified_breaks',
        'modified_note',
        'status',
        'modified_approval_by',
        'modified_approval_at',
    ];

    protected $casts = [
        'modified_breaks' => 'array',
        'modified_approval_at' => 'datetime',
    ];


    /**
     * Get the attendance associated with the modification request.
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approval()
    {
        return $this->belongsTo(User::class, 'modified_approval_by');
    }
}
