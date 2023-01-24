<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class CountType extends Model
{
    use HasFactory;

    protected $table = 'count_types';

    protected $fillable = [
        'count_type_code',
        'count_type_description',
        'count_passcode',
        'status',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = CRUDBooster::myId();
        });

        static::updating(function($model) {
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updated_by = CRUDBooster::myId();
        });

        static::deleting(function($model) {
            $model->deleted_at = date('Y-m-d H:i:s');
            $model->deleted_by = CRUDBooster::myId();
        });
   }
}
