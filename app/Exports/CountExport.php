<?php

namespace App\Exports;

use App\Models\CountHeader;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use CRUDBooster;

class CountExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function headings():array{
        return [
            'COUNT TYPE',
            'COUNT TAG',
            'COUNT CATEGORY',
            'TOTAL QTY',
            'SCANNED BY',
            'SCANNED DATE',
            'VERIFIED BY',
            'VERIFIED DATE',
            'ITEM CODE',
            'ITEM DESCRIPTION',
            'WH CATEGORY',
            'QTY',
            'REVISED QTY',
            'REMARKS',
            'FINAL QTY'
        ];
    }

    public function map($counts): array {
        return [
            $counts->count_type_code,
            $counts->category_tag_number,
            $counts->count_category,
            $counts->total_qty,
            $counts->scan_by,
            $counts->scan_at,
            $counts->verify_by,
            $counts->verify_at,
            $counts->item_code,
            $counts->item_description,
            $counts->item_category,
            $counts->qty,
            $counts->revised_qty,
            $counts->line_remarks,
            ($counts->revised_qty != '') ? $counts->revised_qty : $counts->qty
        ];
    }

    public function query()
    {
        $counts = CountHeader::query()
            ->leftJoin('count_types','count_headers.count_types_id','=','count_types.id')
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
                'item_category.warehouse_category_description as item_category'
            )->whereNull('count_headers.deleted_at');

        if(in_array(CRUDBooster::myPrivilegeName(), ["Scanner","Counter"])){
            $counts->where('count_headers.created_by',CRUDBooster::myId());
        }
        if(in_array(CRUDBooster::myPrivilegeName(), ["Verifier"])){
            $counts->where('count_headers.updated_by',CRUDBooster::myId());
        }

        if (request()->has('filter_column')) {
            $filter_column = request()->filter_column;

            $counts->where(function($w) use ($filter_column) {
                foreach($filter_column as $key=>$fc) {

                    $value = @$fc['value'];
                    $type  = @$fc['type'];

                    if($type == 'empty') {
                        $w->whereNull($key)->orWhere($key,'');
                        continue;
                    }

                    if($value=='' || $type=='') continue;

                    if($type == 'between') continue;

                    switch($type) {
                        default:
                            if($key && $type && $value) $w->where($key,$type,$value);
                        break;
                        case 'like':
                        case 'not like':
                            $value = '%'.$value.'%';
                            if($key && $type && $value) $w->where($key,$type,$value);
                        break;
                        case 'in':
                        case 'not in':
                            if($value) {
                                if($key && $value) $w->whereIn($key,$value);
                            }
                        break;
                    }
                }
            });

            foreach($filter_column as $key=>$fc) {
                $value = @$fc['value'];
                $type  = @$fc['type'];
                $sorting = @$fc['sorting'];

                if($sorting!='') {
                    if($key) {
                        $counts->orderby($key,$sorting);
                        $filter_is_orderby = true;
                    }
                }

                if ($type=='between') {
                    if($key && $value) $counts->whereBetween($key,$value);
                }

                else {
                    continue;
                }
            }
        }
        return $counts;
    }
}
