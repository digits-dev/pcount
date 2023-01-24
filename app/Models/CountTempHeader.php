<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class CountTempHeader extends Model
{
    use HasFactory;

    protected $table = 'count_temp_headers';

    protected $fillable = [
        'count_types_id',
        'category_tag_number',
        'warehouse_categories_id',
        'total_qty',
        'audited_by',
        'audited_at'
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
