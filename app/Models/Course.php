<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /**
     * Table name
     */
    protected $table = 'courses';

    /**
     * Primary key
     */
    protected $primaryKey = 'C_Code';

    /**
     * Primary key type
     */
    protected $keyType = 'string';

    /**
     * Disable auto-incrementing
     */
    public $incrementing = false;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'C_Code',
        'C_Name',
        'C_Hour',
        'C_Prerequisites',
        'C_SemOffered',
        'C_Instructor',
        'C_Description',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'C_Prerequisites' => 'array',
    ];

    /**
     * Relationship: A course can have many students.
     * This connects to the 'course_student' pivot table.
     */
    public function participants()
    {
        return $this->belongsToMany(
            Student::class,      // The Model we are connecting to
            'course_student',    // The name of the pivot table
            'course_code',       // The foreign key for THIS model in the pivot table (C_Code)
            'student_matric',    // The foreign key for the OTHER model in the pivot table (MatricID)
            'C_Code',            // The local key on the 'courses' table
            'MatricID'           // The local key on the 'student' table
        )->withPivot('semester', 'year');
    }

    /**
     * Relationship: A course can have many assignments.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'course_code', 'C_Code');
    }
}