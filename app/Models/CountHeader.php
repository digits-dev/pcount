<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CountHeader extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'count_headers';

    protected $fillable = [
        'count_types_id',
        'category_tag_number',
        'warehouse_categories_id',
        'total_qty',
        'audited_by',
        'audited_at',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public function scopeGetDetail($query, $id){
        return $query->leftJoin('cms_users as scanby','count_headers.created_by','scanby.id')
            ->leftJoin('cms_users as verifyby','count_headers.updated_by','verifyby.id')
            ->where('count_headers.id', $id)
            ->select(
                'count_headers.id',
                'count_headers.count_types_id',
                'count_headers.warehouse_categories_id',
                'count_headers.category_tag_number',
                'count_headers.total_qty',
                'scanby.name as scan_by',
                'count_headers.created_at as scan_at',
                'verifyby.name as verify_by',
                'count_headers.updated_at as verify_at');
    }

    public function scopeGetExport($query){
        return $query->leftJoin('count_types','count_headers.count_types_id','=','count_types.id')
            ->leftJoin('warehouse_categories','count_headers.warehouse_categories_id','warehouse_categories.id')
            ->leftJoin('cms_users as scanby','count_headers.created_by','scanby.id')
            ->leftJoin('cms_users as verifyby','count_headers.updated_by','verifyby.id')
            ->leftJoin('count_lines','count_headers.id','=','count_lines.count_headers_id')
            ->leftJoin('items','count_lines.item_code','=','items.digits_code')
            ->leftJoin('warehouse_categories as item_category','items.warehouse_categories_id','=','item_category.id')
            ->select(
                'count_headers.id',
                'count_types.count_type_code',
                'count_headers.category_tag_number',
                'warehouse_categories.warehouse_category_description as count_category',
                'count_headers.total_qty',
                'scanby.name as scan_by',
                'count_headers.created_at as scan_at',
                'verifyby.name as verify_by',
                'count_headers.updated_at as verify_at',
                'count_lines.item_code',
                'count_lines.qty',
                'count_lines.revised_qty',
                'count_lines.line_color',
                'count_lines.line_remarks',
                'items.item_description',
                'item_category.warehouse_category_description as item_category')
            ->whereNull('count_headers.deleted_at')
            ->whereNull('count_lines.deleted_at');
    }

    public static function boot() {
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

    public function countType() : BelongsTo {
        return $this->belongsTo(CountType::class, 'count_types_id', 'id');
    }

    public function warehouseCategory() : BelongsTo {
        return $this->belongsTo(WarehouseCategory::class, 'warehouse_categories_id', 'id');
    }

    public function lines() : HasMany {
        return $this->hasMany(CountLine::class, 'count_headers_id', 'id');
    }
}
