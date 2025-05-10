<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trangactions extends Model
{
    protected $fillable = [
        'user_id',
        'preson_id',
        'date',
        'name',
        'qty',
        'price',
        'total',
        'subTotal'
    ];
    protected $appends = ['trangaction_type'];
    public function logs(){
        return $this->hasOne(LogsModel::class, 'trangaction_id','id');
    }
    public function person(){
        return $this->belongsTo(Accounting::class, 'preson_id','id');
    }
    public function getTrangactionTypeAttribute(){
        return $this->logs?->trangcation ?? null;
    }
}
