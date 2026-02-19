@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات ثبت تراکنش ها در نرم افزار مالی (نوسا)
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد انبار</th>
                                <th>عنوان انبار</th>
                                <td>تعداد رخدادهایی که ثبت می شود</td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("utility.financial_software.setting.warehouse",[$item,1])}}">{{$item->code}}</a>
                                    </td>
                                    <td>
                                        <a href="{{route("utility.financial_software.setting.warehouse",[$item,1])}}">{{$item->caption}}</a>
                                    </td>
                                    <td>
                                        {{count($item->financial_software_trans_kind)}}
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
        <div class="col-md-12 center">
            <a class="btn btn-outline-dark" href="{{route("wh.financial_software.index")}}"> بارگشت (انتقال به نرم افزارهای مالی)</a>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
