<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountTempLine extends Model
{
    use HasFactory;

    protected $table = 'count_temp_lines';

    protected $fillable = [
        'count_temp_headers_id',
        'item_code',
        'qty',
        'revised_qty',
        'line_color',
        'line_remarks'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            $model->created_at = date('Y-m-d H:i:s');
        });

        static::updating(function($model) {
            $model->updated_at = date('Y-m-d H:i:s');
        });
   }
}
