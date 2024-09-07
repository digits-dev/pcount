<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CountLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'count_lines';

    protected $fillable = [
        'count_headers_id',
        'item_code',
        'qty',
        'revised_qty',
        'line_color',
        'line_remarks'
    ];

    public static function boot() {
        parent::boot();
        static::creating(function($model) {
            $model->created_at = date('Y-m-d H:i:s');
        });

        static::updating(function($model) {
            $model->updated_at = date('Y-m-d H:i:s');
        });
    }

    public function header() : BelongsTo {
        return $this->belongsTo(CountHeader::class, 'count_headers_id', 'id');
    }

    public function item() : BelongsTo {
        return $this->belongsTo(Item::class, 'item_code', 'digits_code');
    }
}
