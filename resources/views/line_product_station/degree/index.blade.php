@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @if($degree_type_master_count!=1)
                <div class="alert alert-danger">
                    هشدار: در لیست درجه ها، تنها یک درجه می تواند به عنوان 'درجه اصلی' انتخاب گردد.
                </div>
                @endif
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست درجه های رسته کالا
                        <b>
                            {{ $goods_kind->caption}}
                        </b>
                        <a href="{{route("line_product_station.degree.create", $goods_kind)}}"
                           class="btn btn-outline-success">افزودن درجه جدید</a>
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
                                <th> نوع درجه</th>
                                <th> انبار</th>
                                <th> درصد کاهش قیمت</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->getCode()}}

                                    </td>
                                    <td>
                                        <a href="{{ route("line_product_station.degree.edit",[$goods_kind,$item])}}">{{$item->caption}}</a>

                                    </td>
                                    <td>{{$item->degree_type->caption??""}}</td>
                                    <td>
                                        {{$item->warehouse->caption??""}}
                                    </td>
                                    <td>{{$item->percent_of_price_reduction}} %</td>
                                    <td>{{$item->active_status->caption}}</td>

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
            <div>
                <a href="{{route("line_product_station.goods_kind.index")}}"
                   class="btn btn-outline-dark">بازگشت</a>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
