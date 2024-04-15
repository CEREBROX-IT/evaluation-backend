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
    protected $fillable = ['evaluation_id', 'question_id', 'evaluation_for', 'question_type', 'question_description', 'rating'];

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
