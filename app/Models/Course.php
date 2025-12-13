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
}
