<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'session';

    // Define the fillable attributes
    protected $fillable = ['school_year', 'session_status'];
}
