<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Carbon;

class Assignment extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'Draft';
    public const STATUS_SCHEDULED = 'Scheduled';
    public const STATUS_PUBLISHED = 'Published';
    public const STATUS_CLOSED = 'Closed';

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

    protected $casts = [
        'due_at' => 'datetime',
    ];

    protected $appends = ['attachment_url'];

    public static function editableStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SCHEDULED,
            self::STATUS_PUBLISHED,
        ];
    }

    public static function closeExpired(?int $lecturerId = null): void
    {
        static::query()
            ->when($lecturerId, fn ($query) => $query->where('lecturer_id', $lecturerId))
            ->whereNotNull('due_at')
            ->where('due_at', '<', Carbon::now())
            ->where('status', '!=', self::STATUS_CLOSED)
            ->update(['status' => self::STATUS_CLOSED]);
    }

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
