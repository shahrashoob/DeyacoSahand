@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست پرینتر های سازمان
                        <a class="btn btn-success" href="{{route("utility.printer.create")}}"> <i
                                class="fa fa-plus"></i> افزودن پرینتر جدید </a>
                    </h5>
                    <div class="label float-right ">
                        <a class="btn btn-primary" href="{{url("upload/utility_files/DCPrinter.zip")}}">دانلود اسکریپت پرینتر</a>
                    </div>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> کد</th>
                                <th> عنوان</th>
                                <th> نوع پرینتر</th>
                                <th>رمز پرینتر</th>
                                <th> عرض کاغذ پرینتر (پیکسل)</th>
                                <th> طول کاغذ پرینتر (پیکسل)</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->code??""}}</td>
                                    <td>
                                        <a href="{{route("utility.printer.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>{{$item->printer_type->caption??""}}</td>
                                    <td>
                                    ******
                                        <a href="#" onclick="alert('پسورد پرینتر برابر است با: {{$item->password}}')" ><i class="fa fa-eye"></i></a>
                                    </td>
                                    <td>{{$item->width??""}}</td>
                                    <td>{{$item->height??""}}</td>
                                    <td>
                                        <a href="{{route("utility.printer.test",$item)}}">تست پرینتر</a>
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
