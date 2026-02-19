@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>
                        لیست حامل ها برای {{$carrier_type->caption}}
                        <a class=" text-success" href="{{route("line_product_station.carrier.carrier_type.create_carrier",$carrier_type)}}" >
                            <i class="fa fa-plus-circle "></i> افزودن حامل جدید
                        </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> شماره حامل</th>
                                <th>وزن</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($carrier_type->carriers as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.carrier.carrier_type.edit_carrier",[$carrier_type,$item])}}">{{$item->code}}</a>
                                    </td>
                                    <td>{{$item->weight??""}}</td>
                                    <td>{{$item->status->caption??""}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.carrier.carrier_type.download",[$item])}}"><i class="fa fa-download"></i> </a>
                                        <a href="{{route("line_product_station.carrier.carrier_type.direct_print",[$item])}}"><i class="fa fa-print"></i> </a>


                                    </td>

                                </tr>
                                @if( $item->log_message)
                                    <tr>
                                        <td colspan="5" class="alert-info">
                                            {!! $item->log_message !!}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>

                        </table>
                    </div>


                </div>

            </div>
        </div>
<div class="col-md-12 center">
    <a class="btn btn-outline-dark" href="{{route("line_product_station.carrier.carrier_type.index")}}" >
       بازگشت
    </a>
</div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
