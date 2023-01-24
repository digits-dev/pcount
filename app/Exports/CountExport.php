<?php

namespace App\Exports;

use App\Models\CountHeader;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

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
            'ITEM CODE',
            'ITEM DESCRIPTION',
            'WH CATEGORY',
            'QTY',
            'REVISED QTY',
            'REMARKS'
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
            $counts->item_code,
            $counts->item_description,
            $counts->item_category,
            $counts->qty,
            $counts->revised_qty,
            $counts->line_remarks
        ];
    }

    public function query()
    {
        $counts = CountHeader::query()
            ->join('count_types','count_headers.count_types_id','=','count_types.id')
            ->join('warehouse_categories','count_headers.warehouse_categories_id','warehouse_categories.id')
            ->join('cms_users as scanby','count_headers.created_by','scanby.id')
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
                'count_lines.item_code',
                'count_lines.qty',
                'count_lines.revised_qty',
                'count_lines.line_color',
                'count_lines.line_remarks',
                'items.item_description',
                'item_category.warehouse_category_description as item_category'
            );

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
