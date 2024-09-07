<?php

namespace App\Http\Controllers;

use App\Models\CountTempHeader;
use Illuminate\Http\Request;
use CRUDBooster;

class CountTempHeaderController extends Controller
{

    public function saveCountHeaders(Request $request)
    {
        $tempHeader = new CountTempHeader();

        $tempHeader->count_types_id = $request->count_type;
        $tempHeader->category_tag_number = $request->category_tag;
        $tempHeader->warehouse_categories_id = $request->category;
        $tempHeader->created_by = CRUDBooster::myId();
        $tempHeader->created_at = date('Y-m-d H:i:s');

        $tempHeader->save();
        return $tempHeader->id;
    }

    public function getCountHeaders(Request $request)
    {
        return CountTempHeader::whereNull('deleted_at')
            ->where('category_tag_number', $request->category_tag)
            ->first();
    }
}
