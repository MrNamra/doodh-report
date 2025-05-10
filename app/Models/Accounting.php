<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
    protected $table = "accounting";
    protected $fillable = [
        'user_id',
        'preson_name',
        'money',
        'note',
        'date'
    ];
}
