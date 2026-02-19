@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لینک های عضویت بارکدی
                        <a class="btn btn-success" href="{{route("utility.other.barcode_link.create")}}"> <i
                                class="fa fa-plus"></i> افزودن بارکد جدید </a>
                    </h5>
                    <div class="label float-right ">
                    </div>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> عنوان</th>
                                <th> نوع بارکد</th>
                                <th> تعداد بازدید</th>
                                <th> تعداد ثبت نام</th>
                                <th> وضعیت </th>
                                <th>دانلود بارکد</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("utility.other.barcode_link.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
<td>{{$item->barcode_link_type->caption}}</td>
                                    <td>{{$item->barcode_link_type->caption.($item->barcode_link_type_id ==2?" (".$item->customer->caption.")":"")}}</td>
                                    <td>{{$item->number_of_visits}}</td>
                                    <td>{{$item->number_of_register}}</td>
                                    <td>{{$item->status->caption??""}}</td>
                                    <td>
                                        <a href="{{route("utility.other.barcode_link.download_qr",$item)}}" >
                                            <i class="fa fa-barcode"></i>
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
