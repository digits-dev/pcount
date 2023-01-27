@extends('crudbooster::admin_template')
@section('content')

@push('head')
<style type="text/css">

table.table.table-bordered td {
  border: 1px solid black;
}

table.table.table-bordered tr {
  border: 1px solid black;
}

table.table.table-bordered th {
  border: 1px solid black;
}

.noselect {
  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Old versions of Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently supported by Chrome, Edge, Opera and Firefox */
}

table { page-break-after:auto }
tr    { page-break-inside:avoid; page-break-after:auto }
td    { page-break-inside:avoid; page-break-after:auto }
thead { display:table-header-group }
tfoot { display:table-footer-group }

@media print {}

    a[href]:after {
        content: none !important;
        visibility: hidden;
        color: white;
    }

    @page {
        size: letter;
        margin-left: 0in;
        margin-right: 0in;
		margin-top: 0.5in;
		margin-bottom: 0.5in;
	}

    @page :header {
        color: white;
        display: none;
    }

    @page :footer {
        color: white;
        display: none;
    }

    .no-print {
        display: none !important;
    }

    .panel{
        border: 0;
    }

    .print-data {
        padding: 0em;
        border: 0;
        border-width: 0;
    }

    .policy{
        font-size: 10px;
    }

}



</style>
@endpush

    <div class='panel panel-default' id="print">

        <h4 class="text-center"><b>{{ $header->count_type_code }}</b></h4>
        <div class='panel-body'>

            <div class="col-md-12">
                <div class="table-responsive print-data">
                    <table class="table-bordered" id="count-header" style="width: 100%">
                        <tbody>
                            <tr>
                                <td width="15%">
                                    <b>Count Tag:</b>
                                </td>
                                <td width="35%">
                                    {{$header->category_tag_number}}
                                </td>
                                <td>
                                    <b>Category:</b>
                                </td>
                                <td>
                                    {{$header->warehouse_category_description}}
                                </td>
                            </tr>
                            <tr>
                                <td width="15%">
                                    <b>Scanned By:</b>
                                </td>
                                <td width="35%">
                                    {{ $header->scan_by }}
                                </td>
                                <td>
                                    <b>Scanned Date:</b>
                                </td>
                                <td>
                                    {{$header->scan_at}}
                                </td>
                            </tr>
                            <tr>
                                <td width="15%">
                                    <b>Verified By:</b>
                                </td>
                                <td width="35%">
                                    {{$header->verify_by}}
                                </td>
                                <td>
                                    <b>Verified Date:</b>
                                </td>
                                <td>
                                    {{$header->verify_at}}
                                </td>
                            </tr>

                            <tr>
                                <td width="15%">
                                    <b>Audited By:</b>
                                </td>
                                <td width="35%">

                                </td>
                                <td>
                                    <b>Audited Date:</b>
                                </td>
                                <td>

                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <br>

            <div class="col-md-12">
                <div class="box-header text-center no-print">
                    <h3 class="box-title no-print"><b>Scanned Items</b></h3>
                </div>

                <div class="box-body no-padding">

                    <div class="table-responsive" id="st-items">
                        <table class="table-bordered noselect" style="width: 100%">
                            <thead>
                                <tr style="background: #0047ab; color: white">
                                    <th width="15%" class="text-center" data-title="{{ trans('label.table.digits_code') }}">{{ trans('label.table.digits_code') }}</th>
                                    <th width="35%" class="text-center" data-title="{{ trans('label.table.item_description') }}">{{ trans('label.table.item_description') }}</th>
                                    <th width="10%" class="text-center" data-title="{{ trans('label.table.qty') }}">{{ trans('label.table.qty') }}</th>
                                    <th width="20%" class="text-center" data-title="{{ trans('label.table.category') }}">{{ trans('label.table.category') }}</th>
                                    <th width="10%" class="text-center" data-title="{{ trans('label.table.revised_qty') }}">{{ trans('label.table.revised_qty') }}</th>
                                    <th width="10%" class="text-center" data-title="{{ trans('label.table.remarks') }}">{{ trans('label.table.remarks') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr class="nr" id="rowid{{$item->item_code}}" data-tr="row" style="color:{{ $item->line_color }}">
                                        <td class="text-center">{{$item->item_code}}</td>
                                        <td>{{$item->item_description}}</td>
                                        <td class="text-center">{{ $item->qty }}</td>
                                        <td class="text-center">{{$item->warehouse_category_description}}</td>
                                        <td class="text-center">{{ $item->revised_qty }}</td>
                                        <td class="text-center">{{ $item->line_remarks }}</td>
                                    </tr>

                                @endforeach

                                <tr class="tableInfo">
                                    <td align="center"> <strong>{{ trans('label.table.total_skus') }} : {{ $sku_count }}</strong> </td>
                                    <td align="right"> <strong>{{ trans('label.table.total_quantity') }}</strong> </td>
                                    <td align="center"> {{ $header->total_qty }}</td>
                                    <td colspan="4"> </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class='panel-footer no-print'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default no-print">{{ trans('message.form.back') }}</a>
        </div>
    </div>



@endsection

@push('bottom')
<script type="text/javascript">
$(document).ready(function () {
    window.print();
});
</script>
@endpush
