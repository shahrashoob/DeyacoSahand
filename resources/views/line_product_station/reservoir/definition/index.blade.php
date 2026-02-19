@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>
                        مخزن های کالا

                        <a href="{{ route("line_product_station.reservoir.definition.create")}}">
                            <i class="fa fa-plus"></i> افزودن مخزن جدید
                        </a>

                    </h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> نام </th>
                                <th> نوع مخزن</th>
                                <th> کد </th>
                                <th>کالاهای مجاز </th>
                                <th>مقدار موجودی</th>
{{--                                <th>مقدار  فرعی موجودی مخزن</th>--}}
                                <th>حداکثر ظرفیت</th>
                                <th>موجودی</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{ route("line_product_station.reservoir.dashboard.index",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>
                                        {{$item->reservoir_type->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->packing_form->code??""}}
                                    </td>
                                    <td>
                                        {{$item->warehouse->caption??""}}
                                    </td>
                                    <td>
                                        @foreach($item->products as $product_reservoir)
                                            {{$product_reservoir->product->fullCaption()}} <br/>
                                        @endforeach
                                    </td>

                                    <td>
                                        {{$item->capacity}} {{$item->unit->caption}}
                                    </td>

                                    <td>
                                        {{$item->getAmount()}} {{$item->unit->caption}}
                                    </td>

                                    <td>{{$item->active_status->caption??""}}</td>
                                    <th>
                                        <a href="{{ route("line_product_station.reservoir.definition.edit",$item)}}"><i class="fa fa-edit"></i> </a>

                                        <a href="{{route("line_product_station.reservoir.definition.print_label",$item)}}"><i class="fa fa-print"></i> </a>
                                    </th>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
