<?php

namespace App\Http\Controllers;

use App\Models\CountTempHeader;
use Illuminate\Http\Request;
use CRUDBooster;

class CountTempHeaderController extends Controller
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
     * @param  \App\Models\CountTempHeader  $countTempHeader
     * @return \Illuminate\Http\Response
     */
    public function show(CountTempHeader $countTempHeader)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CountTempHeader  $countTempHeader
     * @return \Illuminate\Http\Response
     */
    public function edit(CountTempHeader $countTempHeader)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CountTempHeader  $countTempHeader
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CountTempHeader $countTempHeader)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CountTempHeader  $countTempHeader
     * @return \Illuminate\Http\Response
     */
    public function destroy(CountTempHeader $countTempHeader)
    {
        //
    }

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
