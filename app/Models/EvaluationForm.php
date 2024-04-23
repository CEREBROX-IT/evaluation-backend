<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationForm extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'evaluation';

    // Define the fillable attributes
    protected $fillable = ['session_id', 'school_year', 'user_id', 'evaluated_id', 'evaluated_result_id', 'evaluated_full_name', 'subject_name', 'semester', 'comment', 'suggestion', 'strand', 'year_level', 'approve_status', 'status'];

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

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}
