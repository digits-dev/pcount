<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'digits_code',
        'upc_code',
        'upc_code2',
        'upc_code3',
        'upc_code4',
        'upc_code5',
        'item_description',
        'model',
        'brands_id',
        'warehouse_categories_id'
    ];

    public function scopeGetItem($query, $itemCode){
        return $query->where('items.digits_code',$itemCode)
        ->orWhere('items.upc_code',$itemCode)
        ->orWhere('items.upc_code2',$itemCode)
        ->orWhere('items.upc_code3',$itemCode)
        ->orWhere('items.upc_code4',$itemCode)
        ->orWhere('items.upc_code5',$itemCode)
        ->leftJoin('warehouse_categories','items.warehouse_categories_id','=','warehouse_categories.id')
        ->select('items.digits_code',
            'items.upc_code',
            'items.upc_code2',
            'items.upc_code3',
            'items.upc_code4',
            'items.upc_code5',
            'items.item_description',
            'warehouse_categories.warehouse_category_description',
            'items.warehouse_categories_id as wh_category_id');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            $model->created_at = date('Y-m-d H:i:s');
        });

        static::updating(function($model) {
            $model->updated_at = date('Y-m-d H:i:s');
        });

        static::deleting(function($model) {
            $model->deleted_at = date('Y-m-d H:i:s');
        });
   }
}
