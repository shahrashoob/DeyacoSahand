@extends('layouts.admin._master')
@section('page_header_title'," مجوزها")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("utility.special_license.panel.dashboard._search",["route"=>"utility.special_license.panel.dashboard.index"])

            <div class="card">
                <div class="card-header">
                    <h5>مجوز های در انتظار تایید
{{--                        <a href="{{route("utility.office_automation.dashboard.create")}}"--}}
{{--                        ><i class="fa fa-plus-circle"></i> ایجاد کار جدید </a>--}}
                    </h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد</th>
                                <th> نوع مجوز</th>
                                <th>ایجاد کننده</th>
                                <th>تاریخ ایجاد</th>
                                <th>وضعیت</th>

                            </tr>

                            </thead>
                            <tbody>

                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr >
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("utility.special_license.panel.dashboard.view_confirm",[$item->id])}}">
                                            {{$item->code}}
                                        </a>
                                    </td>
                                    <td>
                                        {{$item->special_license_type->caption}}
                                    </td>
                                    <td>
                                        {{$item->worker->fullname()}}
                                    </td>
                                    <td>
                                        {{$item->get_create_date_and_time()}}
                                    </td>
                                    <td>
                                        {{$item->status->caption}}
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
            <div>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        td {
            vertical-align: middle !important;
        }
    </style>
@endsection
