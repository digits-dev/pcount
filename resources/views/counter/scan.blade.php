@extends('crudbooster::admin_template')
@section('content')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">
<style>
    .swal2-popup {
        font-size: 1.5rem !important;
    }

    /* .swal2-container {
        zoom: 1.5;
    } */

</style>
@endpush

<div class='panel panel-default'>
    <div class='panel-heading'>
        <h4 class="box-title text-center"><b>{{ $count_type->count_type_code }} : <span id="totalScanQty" style="color:red;">0 Qty</span></b></h4>
    </div>
    <div class='panel-body'>
        <form method='post' action="{{ route('count.save-scan') }}" id="count-scan" autocomplete="off" role="form" enctype="multipart/form-data">
            <input type='hidden' name='_token' value="{{ csrf_token() }}">
            <input type="hidden" name="count_type" id="count_type" value="{{ $count_type->id }}">
            <input type="hidden" id="header_id" name="temp_headers_id" value="{{ ($headers != null) ? $headers->id : '' }}">

            <div class="col-md-3">
                <b>Scan Item Code</b>
                <div class='form-group'>
                    <input type="text" class="form-control" id="item_search" disabled="disabled">
                </div>
            </div>

            <div class="col-md-3">
                <b>Category</b>
                <div class='form-group'>

                    <select name="warehouse_category" id="category" class="form-control category" required title="Category">
                        <option value="">Please select category</option>
                        @foreach ($categories as $category)
                            @if($headers != null)
                                <option {{ $headers->warehouse_categories_id == $category->id ? "selected" : "disabled" }} value="{{ $category->id }}">{{ $category->warehouse_category_description }}</option>
                            @else
                                <option value="{{ $category->id }}">{{ $category->warehouse_category_description }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <b>Count Tag</b>
                <div class='form-group'>

                    <select name="category_tag" id="category_tag" class="form-control category_tag" required title="Count Tag">
                        <option value="">Please select count tag</option>
                        @if ($headers != null)
                            <option selected value="{{ $headers->category_tag_number }}">{{ $headers->category_tag_number }}</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <b>Verifier</b>
                <div class='form-group'>
                    <select name="verified_by" id="verifier" class="form-control verifier" required title="Verifier">
                        <option value="">Please select verifier</option>
                        @foreach ($verifiers as $verifier)
                            <option value="{{ $verifier->id }}">{{ $verifier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-12 col-sm-12">

                <div class="box-body no-padding">
                    <div class="table-responsive" >
                        <table class="table table-bordered noselect items" id="scan-items">
                            <thead>
                                <tr style="background: #0047ab; color: white">
                                    <th width="15%" class="text-center" data-title="{{ trans('label.table.digits_code') }}">{{ trans('label.table.digits_code') }}</th>
                                    <th width="35%" class="text-center" data-title="{{ trans('label.table.item_description') }}">{{ trans('label.table.item_description') }}</th>
                                    <th width="10%" class="text-center" data-title="{{ trans('label.table.qty') }}">{{ trans('label.table.qty') }}</th>
                                    <th width="15%" class="text-center" data-title="{{ trans('label.table.category') }}">{{ trans('label.table.category') }}</th>
                                    <th width="10%" class="text-center" data-title="{{ trans('label.table.revised_qty') }}">{{ trans('label.table.revised_qty') }}</th>
                                    <th width="10%" class="text-center" data-title="{{ trans('label.table.remarks') }}">{{ trans('label.table.remarks') }}</th>
                                    <th width="5%" class="text-center" data-title="{{ trans('label.table.action') }}">{{ trans('label.table.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr class="dynamicRows"> </tr>
                                @if (!empty($lines))
                                    @foreach ($lines as $item)
                                        <tr class="nr" id="rowid{{$item->item_code}}" data-tr="row" style="color:{{ $item->line_color }}">
                                            <td class="text-center">{{$item->item_code}}
                                                <input type="hidden" id="item_code{{$item->item_code}}" data-id="{{$item->item_code}}" class="item-codes" name="item_code[]" value="{{$item->item_code}}">
                                            </td>
                                            <td>{{$item->item_description}}
                                                <input type="hidden" id="item_line_id{{$item->item_code}}" name="item_line_id[]" value="{{$item->id}}">
                                            </td>
                                            <td class="text-center scan scanqty{{$item->item_code}}">
                                                <input class="form-control text-center count_qty" data-id="{{$item->item_code}}" type="text" name="qty[]" id="qty_{{$item->item_code}}" value="{{ $item->qty }}" readonly/>
                                            </td>
                                            <td class="text-center">{{$item->warehouse_category_description}}</td>
                                            <td class="text-center revised revised_qty{{$item->item_code}}">
                                                <input class="form-control text-center revised_qty" data-id="{{$item->item_code}}" type="number" name="revised_qty[]" id="revised_qty_{{$item->item_code}}" value="{{ $item->revised_qty }}" readonly/>
                                            </td>
                                            <td class="text-center remarks{{$item->item_code}}">
                                                <input class="form-control text-center remarks" data-id="{{$item->item_code}}" type="text" name="remarks[]" id="remarks_{{$item->item_code}}" value="{{ $item->line_remarks }}" readonly/>
                                            </td>
                                            <td class="text-center"><button data-id="{{$item->item_code}}" id="btn_edit{{$item->item_code}}" class="btn btn-xs btn-warning edit_item"><i class="glyphicon glyphicon-pencil"></i></button></td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr class="tableInfo">
                                    <td align="center"> <strong>{{ trans('label.table.total_skus') }} : <span id="totalSKUS"></span></strong> </td>
                                    <td align="right"> <strong>{{ trans('label.table.total_quantity') }}</strong> </td>
                                    <td align="center"> <span id="totalQty"></span> <input type='hidden' name="total_quantity" class="form-control text-center" id="totalQuantity" value="0" readonly> </td>
                                    <td colspan="4"> </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>



            </div>

    </div>
    <div class='panel-footer'>
        <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">Back</a>
        <input type='submit' class='btn btn-primary pull-right' id="btnSubmit" value='Save'/>
    </div>
    </form>
</div>

<div class='modal fade' tabindex='-1' role='dialog' id='modal-edit-passcode' data-backdrop="static" data-keyboard="false">
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'><i class='fa fa-lock'></i> Passcode</h4>
            </div>
            <div class='modal-body'>
                <div class='form-group'>
                    <label>Enter Passcode</label>
                    <input type="password" id="passcode" class="form-control" value="">
                </div>

                <div class='form-group'>
                    <label>Revised Qty</label>
                    <input type="number" id="revised_qty" class="form-control" value="">
                </div>

                <div class='form-group'>
                    <label>Remarks</label>
                    <input type="text" id="remarks" class="form-control" value="">
                </div>

            </div>
            <div class='modal-footer' align='right'>
                <button class='btn btn-default' type='button' data-dismiss='modal' onclick="clearInputs()">Cancel</button>
                <button class='btn btn-primary pull-right' type='button' data-dismiss='modal' onclick="checkEditPassCode()">Submit</button>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' tabindex='-1' role='dialog' id='modal-new-item-passcode' data-backdrop="static" data-keyboard="false">
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'><i class='fa fa-lock'></i> Passcode & New Item Details</h4>
            </div>
            <div class='modal-body'>
                <div class='form-group'>
                    <label>Enter Passcode</label>
                    <input type="password" id="new_item_passcode" class="form-control" value="">
                </div>

                <div class='form-group'>
                    <label>Item Code</label>
                    <input type="text" id="new_item_code" class="form-control" value="">
                </div>

                <div class='form-group'>
                    <label>Item Description</label>
                    <input type="text" id="new_item_description" class="form-control" value="">
                </div>

                <div class='form-group'>
                    <label>Category</label>
                    <select id="new_item_category" class="form-control">
                        <option value="">Please select item category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->warehouse_category_description }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class='modal-footer' align='right'>
                <button class='btn btn-default' type='button' data-dismiss='modal' onclick="clearInputs()">Cancel</button>
                <button class='btn btn-primary pull-right' type='button' data-dismiss='modal' onclick="checkNewItemPassCode()">Submit</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('bottom')
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"
        integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src='https://cdn.rawgit.com/admsev/jquery-play-sound/master/jquery.playSound.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.js" integrity="sha256-CT21YfDe01wscF4AKCPn7mDQEHR2OC49jQZkt5wtl0g=" crossorigin="anonymous"></script>
    <script>
        var countItems = {};
        var countItemsUpc = {};
        var digits_code = '';
        var edited_item = '';

        $(document).ready( function () {

            $(function(){
                $('body').addClass("sidebar-collapse");

                if($("#header_id").val() != ''){
                    $("#item_search").removeAttr('disabled');
                    getTotalComputations();
                }

                $('.item-codes').each(function () {
                    let item_code = $(this).attr('data-id');

                    countItems[item_code] = 1;
                });
            });

            var sel_category = '';

            $("#category").change(function(){
                sel_category = $(this).val();

                $.ajax({
                    url:"{{ route('count.get-category-tags') }}",
                    type:"POST",
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        category: sel_category,
                    },
                    success:function(data) {

                        $("#category_tag").removeAttr("disabled");
                        $("#category_tag").empty();
                        $("#category_tag").append($("<option></option>").attr("value", "").text("Select a count tag"));
                        $.each(data, function(key,value) {
                            $("#category_tag").append($("<option></option>").attr("value", value.category_tag_number).text(value.category_tag_number));
                        });
                    }
                });

                $(".category option:not(:selected)").prop('disabled', true);

            });

            $("#category_tag").change(function(){

                let sel_category_tag = $(this).val();

                $.ajax({
                    url:"{{ route('count.set-used-category-tags') }}",
                    type:"POST",
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        category: sel_category,
                        category_tag: sel_category_tag,
                    },
                    success:function(data) {
                        if(data == 1){

                            $(".category_tag option:not(:selected)").prop('disabled', true);
                            //save headers
                            $.ajax({
                                url:"{{ route('count.save-temp-header') }}",
                                type:"POST",
                                dataType: "json",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    count_type: $("#count_type").val(),
                                    category: sel_category,
                                    category_tag: sel_category_tag,
                                },
                                success:function(data) {
                                    $("#header_id").val(data);
                                }
                            });
                        }
                    },
                    error:function($err){
                        console.error();
                    }

                });

            });

            $("#verifier").change(function(){
                let sel_verifier = $(this).val();
                if(sel_verifier != ''){
                    $("#item_search").removeAttr('disabled');
                    $(".verifier option:not(:selected)").prop('disabled', true);
                }

            });

            $('#scan-items').on('click', '.edit_item', function () {
                edited_item = $(this).attr("data-id");

                //show audit modal for passcode
                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to edit this item?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#modal-edit-passcode").modal('show');
                    }
                });

                return false;
            });

            $("#item_search").keypress(function(event){
                if (event.which == '13') {
                    event.preventDefault();
                    $('.scan').css('background-color','white');
                    $('.revised').css('background-color','white');
                    $('#item_search').prop("disabled", true);

                    if($(this).val().length < 1 || $("#item_search").val().trim() == '') {
                        resetItemSearch();
                        return false;
                    }

                    if($('#item_search').val().trim() in countItems || $('#item_search').val().trim() in countItemsUpc){

                        $.playSound(ASSET_URL+'sounds/success.ogg');
                        digits_code = $('#item_search').val();

                        if(!$('#qty_'+digits_code).length){
                            digits_code = countItemsUpc[$('#item_search').val()];
                        }

                        setTimeout(function(){
                            $('#qty_' + digits_code).val(function (i, oldval) {
                                return ++oldval;
                            });

                            if($('#revised_qty_' + digits_code).val() !== ''){
                                $('.revised_qty' + digits_code).css('background-color', 'yellow');
                                $('#revised_qty_' + digits_code).val(function (i, oldval) {
                                    return ++oldval;
                                });
                            }
                            else{
                                $('.scanqty' + digits_code).css('background-color', 'yellow');
                            }
                            getTotalComputations();

                        },500);

                        $.ajax({
                            url:"{{ route('count.update-temp-line') }}",
                            type:"POST",
                            dataType: "json",
                            data: {
                                _token: "{{ csrf_token() }}",
                                line_id: $("#item_line_id"+digits_code).val(),
                            },
                            success:function(data) {
                                console.log(data);
                            }
                        });

                        resetItemSearch();
                    }
                    else{

                        $.ajax({
                            url:"{{ route('count.get-item') }}",
                            type:"POST",
                            dataType: "json",
                            data: {
                                _token: "{{ csrf_token() }}",
                                item_code: $('#item_search').val().trim(),
                            },
                            success:function(items) {

                                if(JSON.stringify(items) === "[]"){
                                    $.playSound(ASSET_URL+'sounds/error.ogg');
                                    Swal.fire({
                                        title: "Do you want to add this item?",
                                        text: "Item not found!",
                                        icon: "info",
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $("#new_item_code").val($('#item_search').val().trim());
                                            $("#modal-new-item-passcode").modal('show');
                                        }
                                        else{
                                            resetItemSearch();
                                        }
                                    });
                                    return false;
                                }

                                if(items[0].warehouse_category_description != $("#category option:selected").text()){
                                    $.playSound(ASSET_URL+'sounds/error.ogg');
                                    Swal.fire({
                                        title: "Do you want to scan this item?",
                                        text: "Item not included to your selected category!",
                                        icon: "info",
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            createNewRow(items,'#FF0000');
                                            $('#rowid' + items[0].digits_code).css('color', 'red');
                                        }
                                        else{
                                            resetItemSearch();
                                        }
                                    });
                                    return false;
                                }

                                else{
                                    createNewRow(items,'#000000');
                                }

                            },
                            error:function(error){
                                console.log(error);
                            }
                        });

                    }
                }
            });

            $('#btnSubmit').bind('keypress keydown keyup', function(e){
                if(e.keyCode == 13) { e.preventDefault(); }
            });

            $("#btnSubmit").click(function(event) {
                event.preventDefault();

                let rowCount = parseInt($('#scan-items tr.nr').length);

                if(rowCount == 0){
                    Swal.fire('Warning!','Please scan at least 1 item!','warning');
                    return false;
                }

                if($("#count-scan").valid()){

                    Swal.fire({
                    title: 'Do you want to save the changes?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).prop("disabled", true);
                            $("#count-scan").submit();
                        }
                    });
                }

            });
        });

    function calculateTotalQty() {
    var totalQty = 0;

    $('.count_qty').each(function () {
        let item_id = $(this).attr('data-id');

        if(isNaN(parseInt($('#revised_qty_'+item_id).val()))){

        }
        if(parseInt($('#revised_qty_'+item_id).val()) > 0){
            totalQty += parseInt($('#revised_qty_'+item_id).val());
        }
        else{
            totalQty += parseInt($(this).val());
        }

    });

    return totalQty;
    }

    function getTotalComputations() {
        let skuCount = parseInt($('#scan-items tr.nr').length);
        $("#totalQuantity").val(calculateTotalQty());
        $("#totalSKUS").text(skuCount);
        $("#totalQty").text(calculateTotalQty());
        $("#totalScanQty").text(calculateTotalQty() + " Qty");
    }

    function resetItemSearch()
    {
        $('#item_search').removeAttr("disabled");
        $('#item_search').val('');
        $('#item_search').focus();
    }

    function checkEditPassCode(){

        $.ajax({
            url:"{{ route('count.get-passcode') }}",
            type:"POST",
            dataType: "json",
            data: {
                _token: "{{ csrf_token() }}",
                count_type: "{{ $count_type->count_type_code }}",
                count_passcode: $("#passcode").val(),
            },
            success:function(data) {

                if(data == null){
                    setTimeout(function(){
                        $.playSound(ASSET_URL+'sounds/error.ogg');
                        Swal.fire('Warning!','Invalid password!','error');
                        $("#passcode").val('');
                        $("#revised_qty").val('');
                        $("#remarks").val('');
                        return false;
                    },500);
                }

                else if(data.count_passcode == $("#passcode").val()){

                    setTimeout(function(){
                        if($("#revised_qty").val() == 0){
                            $.playSound(ASSET_URL+'sounds/error.ogg');
                            Swal.fire('Warning!','Zero (0) qty is not allowed!','error');
                            $("#passcode").val('');
                            $("#revised_qty").val('');
                            $("#remarks").val('');
                            return false;
                        }
                        else if($("#revised_qty").val() > $("#qty_"+edited_item).val()){
                            $.playSound(ASSET_URL+'sounds/error.ogg');
                            Swal.fire('Warning!','You can\'t input qty greater than the actual scanned qty!','error');
                            $("#passcode").val('');
                            $("#revised_qty").val('');
                            $("#remarks").val('');
                            return false;
                        }
                        else if($("#revised_qty").val() == $("#qty_"+edited_item).val()){
                            $.playSound(ASSET_URL+'sounds/error.ogg');
                            Swal.fire('Warning!','You can\'t input qty equal to the actual scanned qty!','error');
                            $("#passcode").val('');
                            $("#revised_qty").val('');
                            $("#remarks").val('');
                            return false;
                        }
                        else{
                            $.playSound(ASSET_URL+'sounds/success.ogg');
                            $("#revised_qty_"+edited_item).val($("#revised_qty").val());
                            $("#remarks_"+edited_item).val($("#remarks").val());

                            //update temp table
                            $.ajax({
                                url:"{{ route('count.update-temp-line-revised') }}",
                                type:"POST",
                                dataType: "json",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    line_id: $("#item_line_id"+edited_item).val(),
                                    revised_qty: $("#revised_qty").val(),
                                    remarks: $("#remarks").val(),
                                },
                                success:function(data) {
                                    console.log(data);
                                }
                            });

                            $("#passcode").val('');
                            $("#revised_qty").val('');
                            $("#remarks").val('');
                            getTotalComputations();
                            return false;
                        }
                    },500);

                }

            }
        });
    }

    function checkNewItemPassCode(){

        $.ajax({
            url:"{{ route('count.get-passcode') }}",
            type:"POST",
            dataType: "json",
            data: {
                _token: "{{ csrf_token() }}",
                count_type: "{{ $count_type->count_type_code }}",
                count_passcode: $("#new_item_passcode").val(),
            },
            success:function(data) {

                if(data == null){
                    $.playSound(ASSET_URL+'sounds/error.ogg');
                    Swal.fire('Warning!','Invalid password! No new item added!','error');
                    $("#new_item_passcode").val('');
                    $("#new_item_code").val('');
                    $("#new_item_description").val('');
                    $("#new_item_category").val('');
                    return false;
                }
                if(data.count_passcode == $("#new_item_passcode").val()){
                    $.playSound(ASSET_URL+'sounds/success.ogg');
                    let new_item_code = $("#new_item_code").val().trim();
                    let new_item_description = $("#new_item_description").val();
                    let new_item_category = $("#new_item_category option:selected").val();
                    let new_item_category_text = $("#new_item_category option:selected").text();
                    //add new item row

                    if(new_item_code in countItems || new_item_code in countItemsUpc){
                        Swal.fire('Warning!','Existing item detected! No new item added!','error');
                        $("#new_item_passcode").val('');
                        $("#new_item_code").val('');
                        $("#new_item_description").val('');
                        $("#new_item_category").val('');
                        resetItemSearch();
                        return false;
                    }

                    countItems[new_item_code] = 1;

                    let new_item_row = '<tr class="nr" id="rowid'+new_item_code+'" data-tr="row" style="color:#0000FF">' +
                        '<td class="text-center">'+new_item_code+' <input type="hidden" id="item_code'+new_item_code+'" name="new_item_code[]" value="'+new_item_code+'"></td>'+
                        '<td>'+new_item_description+' <input type="hidden" name="new_item_description[]" value="'+new_item_description+'">'+
                            '<input type="hidden" id="item_line_id'+new_item_code+'" name="item_line_id[]" value="">'+
                        '</td>'+
                        '<td class="text-center scan scanqty'+new_item_code+'">'+
                            '<input class="form-control text-center count_qty" data-id="'+new_item_code+'" type="text" name="new_item_qty[]" id="qty_'+new_item_code+'" value="1" readonly/>'+
                        '</td>'+
                        '<td class="text-center">'+new_item_category_text+'<input type="hidden" name="new_item_category[]" value="'+new_item_category+'">'+
                            '<input type="hidden" id="line_color'+new_item_code+'" name="new_line_color[]" value="#0000FF">'+
                        '</td>'+
                        '<td class="text-center revised revised_qty'+new_item_code+'">'+
                            '<input class="form-control text-center revised_qty" data-id="'+new_item_code+'" type="number" name="new_item_revised_qty[]" id="revised_qty_'+new_item_code+'" value="" readonly/>'+
                        '</td>'+
                        '<td class="text-center remarks'+new_item_code+'">'+
                            '<input class="form-control text-center remarks" data-id="'+new_item_code+'" type="text" name="new_item_remarks[]" id="remarks_'+new_item_code+'" value="" readonly/>'+
                        '</td>'+
                        '<td class="text-center"><button data-id="'+new_item_code+'" id="btn_edit'+new_item_code+'" class="btn btn-xs btn-warning edit_item"><i class="glyphicon glyphicon-pencil"></i></button></td>' +
                        '</tr>';

                    $(new_item_row).insertAfter($('table tr.dynamicRows:last'));
                    getTotalComputations();
                    $('.scanqty' + new_item_code).css('background-color', 'yellow');
                    resetItemSearch();

                    $.ajax({
                        url:"{{ route('count.save-temp-line') }}",
                        type:"POST",
                        dataType: "json",
                        data: {
                            _token: "{{ csrf_token() }}",
                            count_header: $("#header_id").val(),
                            item_code: new_item_code,
                            line_color: "#0000FF",
                            qty: 1,
                        },
                        success:function(data) {
                            $("#item_line_id"+new_item_code).val(data);
                        }
                    });

                    $("#new_item_passcode").val('');
                    $("#new_item_code").val('');
                    $("#new_item_description").val('');
                    $("#new_item_category").val('');
                    return false;
                }

            }
        });
    }

    function createNewRow(items,color) {
        $.playSound(ASSET_URL+'sounds/success.ogg');

        countItems[items[0].digits_code] = 1;
        if(items[0].upc_code != '' || items[0].upc_code != null){
            countItemsUpc[items[0].upc_code] = items[0].digits_code;
        }
        if(items[0].upc_code2 != '' || items[0].upc_code2 != null){
            countItemsUpc[items[0].upc_code2] = items[0].digits_code;
        }
        if(items[0].upc_code3 != '' || items[0].upc_code3 != null){
            countItemsUpc[items[0].upc_code3] = items[0].digits_code;
        }
        if(items[0].upc_code4 != '' || items[0].upc_code4 != null){
            countItemsUpc[items[0].upc_code4] = items[0].digits_code;
        }
        if(items[0].upc_code5 != '' || items[0].upc_code5 != null){
            countItemsUpc[items[0].upc_code5] = items[0].digits_code;
        }

        let new_row = '<tr class="nr" id="rowid'+items[0].digits_code+'" data-tr="row">' +
            '<td class="text-center">'+items[0].digits_code+' <input type="hidden" id="item_code'+items[0].digits_code+'" name="item_code[]" value="'+items[0].digits_code+'"></td>'+
            '<td>'+items[0].item_description+' <input type="hidden" id="item_line_id'+items[0].digits_code+'" name="item_line_id[]" value=""></td>'+
            '<td class="text-center scan scanqty'+items[0].digits_code+'">'+
                '<input class="form-control text-center count_qty" data-id="'+items[0].digits_code+'" type="text" name="qty[]" id="qty_'+items[0].digits_code+'" value="1" readonly/>'+
            '</td>'+
            '<td class="text-center">'+items[0].warehouse_category_description+' <input type="hidden" id="line_color'+items[0].digits_code+'" name="line_color[]" value="'+color+'"></td>'+
            '<td class="text-center revised revised_qty'+items[0].digits_code+'">'+
                '<input class="form-control text-center revised_qty" data-id="'+items[0].digits_code+'" type="number" name="revised_qty[]" id="revised_qty_'+items[0].digits_code+'" value="" readonly/>'+
            '</td>'+
            '<td class="text-center remarks'+items[0].digits_code+'">'+
                '<input class="form-control text-center remarks" data-id="'+items[0].digits_code+'" type="text" name="remarks[]" id="remarks_'+items[0].digits_code+'" value="" readonly/>'+
            '</td>'+
            '<td class="text-center"><button data-id="'+items[0].digits_code+'" id="btn_edit'+items[0].digits_code+'" class="btn btn-xs btn-warning edit_item"><i class="glyphicon glyphicon-pencil"></i></button></td>' +
            '</tr>';

        $(new_row).insertAfter($('table tr.dynamicRows:last'));
        getTotalComputations();
        $('.scanqty' + items[0].digits_code).css('background-color', 'yellow');
        resetItemSearch();

        $.ajax({
            url:"{{ route('count.save-temp-line') }}",
            type:"POST",
            dataType: "json",
            data: {
                _token: "{{ csrf_token() }}",
                count_header: $("#header_id").val(),
                item_code: items[0].digits_code,
                line_color: color,
                qty: 1,
            },
            success:function(data) {
                $("#item_line_id"+items[0].digits_code).val(data);
            }
        });
    }

    function clearInputs(){
        resetItemSearch();
    }

    </script>

@endpush
