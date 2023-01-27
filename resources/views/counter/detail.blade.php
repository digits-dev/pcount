@extends('crudbooster::admin_template')
@section('content')

@push('head')


@endpush

    @if(g('return_url'))
    <p><a title='Return' href='{{g("return_url")}}' class="noprint"><i class='fa fa-chevron-circle-left'></i>
    &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>
    @else
    <p><a title='Main Module' href='{{CRUDBooster::mainpath()}}' class="noprint"><i class='fa fa-chevron-circle-left'></i>
    &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>
    @endif
  <!-- Your html goes here -->
  <div class='panel panel-default'>
    <div class='panel-heading'>
        <h4 class="box-title text-center"><b>{{ $header->count_type_code }} </b></h4>
    </div>
    <div class='panel-body'>

        <div class="col-md-4">
            <div class="table-responsive">
                <table class="table table-bordered" id="scan-details-1">
                    <tbody>
                        <tr>
                            <td style="width: 25%">
                                <b>Count Tag:</b>
                            </td>
                            <td colspan="3">
                                {{ $header->category_tag_number }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 25%">
                                <b>Category:</b>
                            </td>
                            <td colspan="3">
                                {{ $header->warehouse_category_description }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-4">
            <div class="table-responsive">
                <table class="table table-bordered" id="scan-details-2">
                    <tbody>
                        <tr>
                            <td style="width: 25%">
                                <b>Scanned By:</b>
                            </td>
                            <td>
                                {{ $header->scan_by }}
                            </td>

                        </tr>

                        <tr>
                            <td style="width: 25%">
                                <b>Scanned Date:</b>
                            </td>
                            <td>
                                {{ $header->scan_at }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-4">
            <div class="table-responsive">
                <table class="table table-bordered" id="scan-details-3">
                    <tbody>
                        <tr>
                            <td style="width: 25%">
                                <b>Verified By:</b>
                            </td>
                            <td>
                                {{ $header->verify_by }}
                            </td>

                        </tr>

                        <tr>
                            <td style="width: 25%">
                                <b>Verified Date:</b>
                            </td>
                            <td>
                                {{ $header->verify_at }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <br>

        <div class="col-md-12">
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-bordered" id="scan-items">
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
  </div>
@endsection
