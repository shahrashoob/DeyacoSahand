@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت
                        <b>
                           عملیات های
                        </b>
                        <b>
                            {{$contractor->caption}}
                        </b>
                        <a class="btn btn-success"
                           href="{{route("contractor.definition.operation.create",$contractor)}}"><i
                                class="fa fa-plus-circle"></i> افزودن عملیات جدید </a>
                    </h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد عملیات</th>
                                <th>عنوان عملیات</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($contractor->operations as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>{{$item->getCode()}}</td>
                                    <td>
                                        <a href="{{route("contractor.definition.operation.edit",[$contractor,$item])}}">{{$item->caption}}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
            <div>
                <a href="{{route("contractor.definition.dashboard.index")}}"
                   class="btn btn-outline-dark">بازگشت</a>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
