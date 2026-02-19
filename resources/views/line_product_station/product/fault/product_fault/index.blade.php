@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست نقص های کالا
                        <a class="btn btn-success"
                           href="{{route("line_product_station.product.fault.product_fault.create")}}"> <i
                                class="fa fa-plus"></i> افزودن نقص </a>

                        <a class="btn btn-primary"
                           href="{{route("line_product_station.product.fault.product_fault_sign.index")}}">   نمودهای بیرونی نقص کالا </a>
                    </h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> کد</th>
                                <th> عنوان</th>
                                <th>نمودهای نقص کالا</th>
                                <th> بعد اعلام نقص، نیاز به جابجایی کارت تولید می باشد؟</th>
                                <th> بعد از اعلام نقص، آیا نیاز به تایید دارد؟</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->code??""}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.product.fault.product_fault.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>
                                        @foreach($item->product_fault_signs as $product_fault_product_fault_sign)
                                            {{$product_fault_product_fault_sign->product_fault_sign->caption}},
                                        @endforeach
                                    </td>
                                    <td>{!! $item->need_to_move_shift?"<i class='fa fa-check'></i>" :""!!}</td>
                                    <td>{!! $item->need_to_confirmation?"<i class='fa fa-check'></i>" :""!!}</td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$list->firstItem()}}</b>
                        تا
                        <b>{{$list->lastItem()}}</b>
                        از
                        <b>{{$list->total()}}</b>
                        رکورد موجود
                    </div>
                </div>
                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
