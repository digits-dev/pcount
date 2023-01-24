<?php

namespace App\Http\Controllers;

use App\Models\CountTempLine;
use Illuminate\Http\Request;
use CRUDBooster;

class CountTempLineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CountTempLine  $countTempLine
     * @return \Illuminate\Http\Response
     */
    public function show(CountTempLine $countTempLine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CountTempLine  $countTempLine
     * @return \Illuminate\Http\Response
     */
    public function edit(CountTempLine $countTempLine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CountTempLine  $countTempLine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CountTempLine $countTempLine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CountTempLine  $countTempLine
     * @return \Illuminate\Http\Response
     */
    public function destroy(CountTempLine $countTempLine)
    {
        //
    }

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
        return json_encode(CountTempLine::where('id',$request->line_id)
            ->increment('qty'));
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
