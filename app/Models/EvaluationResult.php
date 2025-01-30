<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationResult extends Model
{
    use HasFactory;

    // Define the table name (if necessary to match the exact table name)
    protected $table = 'evaluation_result'; // Ensure this matches your DB table name

    // Define the fillable attributes (make sure all fields exist in the table)
    protected $fillable = [
        'evaluation_id',
        'question_id',
        'type',
        'question_group',
        'evaluation_type',
        'question_description',
        'rating',
        'status',
    ];

    // Define relationships
    // Evaluation relation: Each evaluation result belongs to an evaluation (evaluation_id)
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id'); // Explicit foreign key if needed
    }

    // Question relation: Each evaluation result is associated with a question (question_id)
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id'); // Explicit foreign key if needed
    }

    public function evaluationForm()
    {
        return $this->belongsTo(EvaluationForm::class, 'evaluation_form_id'); // adjust the foreign key as needed
    }
}
