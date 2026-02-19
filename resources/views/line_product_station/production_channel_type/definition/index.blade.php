@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست کانال های تولید
                        <a class="btn btn-success" href="{{route("line_product_station.production_channel_type.definition.create")}}"> <i
                                class="fa fa-plus"></i> افزودن کانال تولید جدید </a>

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> </th>
                                <th>کد </th>
                                <th> عنوان کانال تولید</th>
                                <th> دسته بندی</th>
                                <th>حداقل تولید </th>
                                <th>حداکثر تولید </th>
                                <th>تعداد کانال مشابه

                                </th>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    تعداد کانال مشابه:
                                    تعداد کانال هایی که از یک نوع کانال می توانند پست سر هم برای ماشین ایجاد شوند
                                </td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                       @include("component.input._color_label",["color"=>$item->color])
                                    </td>
                                    <td>
                                        {{$item->id}}
                                    </td>
                                    <td>
                                        <a href="{{route("line_product_station.production_channel_type.definition.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>
                                        {{$item->production_channel_category->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->min_capacity}}
                                    </td>
                                    <td>
                                        {{$item->max_capacity}}
                                    </td>
                                    <td>
                                        {{$item->max_number_of_sequences}}
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
