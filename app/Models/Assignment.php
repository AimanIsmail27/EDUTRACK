<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'instructions',
        'course_code',
        'lecturer_id',
        'due_at',
        'total_marks',
        'status',
        'attachment_path',
    ];

    protected $attributes = [
        'status' => 'Published',
    ];

    protected $casts = [
        'due_at' => 'datetime',
    ];

    protected $appends = ['attachment_url'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_code', 'C_Code');
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
