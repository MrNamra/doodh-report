<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogsModel extends Model
{
    protected $table = "logs";
    protected $fillable = [
        'trangaction_id',
        'preson_id',
        'trangcation',
        'ammount',
        'note',
        'created_at'
    ];
    public function preson(){
        return $this->belongsTo(Accounting::class, 'preson_id', 'id');
    }
}
