@extends('layouts.admin._master')
@section("page_header_title","کارتابل مدیریت شرکت ها")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست شرکت ها
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>  نام شرکت</th>
                                <th>نام و نام خانوادگی مدیر عامل</th>
                                <th>شناسه ملی</th>
                                <th>شماره ثبت</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)

                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->caption??""}}</td>
                                    <td>{{$item->worker->fullname()??""}}</td>
                                    <td>{{$item->national_code??""}}</td>
                                    <td>{{$item->register_code??""}}</td>
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
