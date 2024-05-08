<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationResult extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'evaluation_result';

    // Define the fillable attributes
    protected $fillable = ['evaluation_id', 'question_id', 'type', 'question_group', 'evaluation_type', 'question_description', 'rating', 'status'];

    // Define relationships
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
