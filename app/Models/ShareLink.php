<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShareLink extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'share_links';
    protected $fillable = ['preson_id', 'month', 'year'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
