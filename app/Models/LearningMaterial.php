<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningMaterial extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Useful if Laravel doesn't pluralize correctly.
     */
    protected $table = 'learning_materials';

    /**
     * The attributes that are mass assignable.
     * This matches the columns we created in the migration.
     */
    protected $fillable = [
        'course_code',
        'user_id',
        'week_number',
        'title',
        'category',
        'file_path',
        'file_original_name',
        'file_extension',
    ];

    /**
     * Relationship: Get the course this material belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_code', 'C_Code');
    }

    /**
     * Relationship: Get the lecturer who uploaded this material.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}