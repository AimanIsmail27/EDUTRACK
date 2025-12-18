<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // Tell Laravel to look at the 'student' table
    protected $table = 'student';

    // Set 'MatricID' as the primary key (Laravel usually expects 'id')
    protected $primaryKey = 'MatricID';

    // Since 'MatricID' is a string (e.g., CB21001), disable auto-increment
    public $incrementing = false;

    // Set the primary key type to string
    protected $keyType = 'string';

    // Set to false if your student table doesn't have created_at/updated_at columns
    public $timestamps = false;

    // Allow mass assignment for these columns
    protected $fillable = [
        'MatricID',
        'Name',
        'Course',
        'Year',
    ];

    /**
     * Relationship: A student can belong to many courses.
     * This connects to the 'course_student' pivot table.
     */
    public function courses()
    {
        return $this->belongsToMany(
            Course::class,      // The Model we are connecting to
            'course_student',   // The name of the pivot table you created
            'student_matric',   // The foreign key for THIS model in the pivot table
            'course_code',      // The foreign key for the OTHER model in the pivot table
            'MatricID',         // The local key on the 'student' table
            'C_Code'            // The local key on the 'courses' table
        )->withPivot('semester', 'year');
    }
}