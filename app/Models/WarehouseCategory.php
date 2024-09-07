<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseCategory extends Model
{
    use HasFactory;

    protected $table = 'warehouse_categories';

    protected $fillable = [
        'warehouse_category_code',
        'warehouse_category_description',
        'is_restricted',
        'status',
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

    public function scopeWithCategory($query, $category) {
        return $query->where('warehouse_category_description', $category)->first();
    }
}
