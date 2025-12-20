<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';
    protected $primaryKey = 'C_Code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'C_Code',
        'C_Name',
        'C_Hour',
        'C_Prerequisites',
        'C_SemOffered',
        'coordinator_id', // Replaced C_Instructor with this
        'C_Description',
    ];

    protected $casts = [
        'C_Prerequisites' => 'array',
    ];

    /**
     * Relationship: One Course has one Coordinator (User).
     */
    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    /**
     * Relationship: A course can have many involved lecturers.
     * Connects to 'course_lecturer' pivot table.
     */
    public function lecturers()
    {
        return $this->belongsToMany(
            User::class, 
            'course_lecturer', 
            'course_code', 
            'user_id'
        )->withTimestamps();
    }

    /**
     * Relationship: A course can have many students (Participants).
     * This connects to your 'course_student' pivot table.
     */
    public function participants()
    {
        return $this->belongsToMany(
            Student::class,      
            'course_student',    
            'course_code',       
            'student_matric',    
            'C_Code',            
            'MatricID'           
        )->withPivot('semester', 'year')->withTimestamps();
    }

    /**
     * Relationship: A course has many learning materials.
     */
    public function materials()
    {
        return $this->hasMany(LearningMaterial::class, 'course_code', 'C_Code');
    }
}