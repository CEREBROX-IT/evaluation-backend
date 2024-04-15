<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'evaluation';

    // Define the fillable attributes
    protected $fillable = ['user_id', 'evaluated_id', 'evaluated_result_id', 'evaluated_first_name', 'evaluated_last_name', 'subject_name', 'semester', 'comment', 'strand', 'year_level'];

    // Define the relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evaluatedUser()
    {
        return $this->belongsTo(User::class, 'evaluated_id');
    }

    public function evaluationResult()
    {
        return $this->hasOne(EvaluationResult::class, 'evaluation_id');
    }
}
