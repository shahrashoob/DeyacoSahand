@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست تخفیف ها
                        <a class="btn btn-success" href="{{route("accounting.offer.create",500)}}" > <i class="fa fa-users"></i> افزودن تخفیف برای کانال </a>
                        <a class="btn btn-success" href="{{route("accounting.offer.create",510)}}" > <i class="fa fa-user"></i> افزودن تخفیف مشتری </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد تخفیف</th>
                                <th>کد کالا</th>
                                <th> نام کالا</th>
                                <th> درجه کالا</th>
                                <td>حداقل خرید </td>
                                <td>حداکثر خرید </td>
                                <td>تاریخ شروع</td>
                                <td>تاریخ پایان</td>
                                <td>درصد تخفیف</td>
                                <td> تعداد رایگان  (کد کالا - کد درجه)</td>
                                <td>کانال توزیع</td>
                                <td>مشتری </td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->id}}</td>
                                    <td>{{$item->product->code}}</td>
                                    <td>{{$item->product->caption}}</td>
                                    <td>{{$item->degree->code??""}}</td>
                                    <td>{{$item->min_buy}}</td>
                                    <td>{{$item->max_buy}}</td>
                                    <td>
                                        {{$item->get_start_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->get_end_datetime()}}
                                    </td>
                                    <td>{{$item->percent_off}} %</td>
                                    <td>{{$item->percent_free}} % ({{$item->product_free->code??""}} - {{$item->degree_free->code??""}})</td>
                                    <td>{{$item->channel_type->caption??""}}</td>
                                    <td>{{$item->customer->caption??""}}</td>
                                    <td>
                                        <a class="text-danger" href="{{route("accounting.offer.de_active",$item)}}" onclick="return confirm('آیا از حذف تخفیف اطمینان دارید؟')">
                                            <i class="fa fa-trash"></i> حذف
                                        </a>
                                    </td>
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
