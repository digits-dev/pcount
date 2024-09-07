<?php

namespace App\Http\Controllers;

use App\Models\CountTempLine;
use Illuminate\Http\Request;
use CRUDBooster;

class CountTempLineController extends Controller
{

    public function saveCountLines(Request $request)
    {
        $tempLines = new CountTempLine();

        $tempLines->count_temp_headers_id = $request->count_header;
        $tempLines->item_code = $request->item_code;
        $tempLines->qty = $request->qty;
        $tempLines->line_color = $request->line_color;

        $tempLines->save();
        return $tempLines->id;
    }

    public function updateItemQty(Request $request)
    {
        return json_encode(CountTempLine::where('id',$request->line_id)->update([
            'qty' => $request->line_qty
        ]));//->increment('qty'));
    }

    public function updateItemRevisedQty(Request $request)
    {
        return json_encode(CountTempLine::where('id',$request->line_id)
            ->update([
                'revised_qty' => $request->revised_qty,
                'line_remarks' => $request->remarks
            ]));
    }
}
