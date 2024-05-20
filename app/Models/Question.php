<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'question';

    // Define the fillable attributes
    protected $fillable = ['type', 'question_group', 'evaluation_type', 'question_description', 'status', 'created_at', 'updated_at'];
}
