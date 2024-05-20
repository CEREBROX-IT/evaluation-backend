<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    // Define the table name
    protected $table = 'user_information';

    // Define the fillable attributes
    protected $fillable = ['user_id', 'gender', 'category', 'length_of_service'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
