<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // ADDED THE 'role' FIELD HERE
        'role',
        'matric_id',
        'course',
        'year',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get courses where the user is the Coordinator.
     * (One-to-Many)
     */
    public function coordinatedCourses()
    {
        return $this->hasMany(Course::class, 'coordinator_id', 'id');
    }

    /**
     * Get courses where the user is part of the Teaching Team (Involved Lecturer).
     * (Many-to-Many)
     */
    public function teachingCourses()
    {
        return $this->belongsToMany(
            Course::class, 
            'course_lecturer', 
            'user_id', 
            'course_code',
            'id',
            'C_Code'
        )->withTimestamps();
    }
}