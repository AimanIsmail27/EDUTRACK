<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    protected $table = 'lecturer';

    protected $primaryKey = 'StaffID';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'StaffID',
        'Name',
        'Email',
    ];

    public function teachingCourses()
{
    return $this->belongsToMany(
        Course::class, 
        'course_lecturer', 
        'user_id', // foreign key in pivot table pointing to users table
        'course_code',
        'StaffID', // local key in lecturers table
        'C_Code'
    );
}

}
