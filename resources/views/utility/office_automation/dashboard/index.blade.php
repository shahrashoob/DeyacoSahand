@extends('layouts.admin._master')
@section('page_header_title'," اتوماسیون اداری")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("utility.office_automation.dashboard._search_view",["route"=>"utility.office_automation.dashboard.index"])
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>میز کار من


                    </h5>
                    <a href="{{route("utility.office_automation.dashboard.create")}}"
                    ><i class="fa fa-plus-circle"></i> ایجاد کار جدید </a>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد</th>
                                <th> عنوان</th>
                                <th>اولویت</th>
                                <th>ایجاد کننده کار</th>
                                <th>نوع (ایجاد شده/ورودی)</th>
                                <th>تاریخ ایجاد</th>
                                <th>تاریخ پایان</th>
                                <th>وضعیت</th>

                            </tr>

                            </thead>
                            <tbody>

                            @php $row=0;@endphp
                            @foreach($list as $item)
                                    <tr style="{{$item->user_view_all_actions($user_id)? "":"font-weight:bold"}};  {{$item->status_id==5250005?"background-color:#0A0A23; color:#fff":""}}" >
                                        <td>{{++$row}}</td>
                                        <td>
                                            <a href="{{route("utility.office_automation.dashboard.view",[$item->id,$user_id])}}">
                                                {{$item->getCode()}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$item->caption}}
                                        </td>
                                        <td>
                                            {!! $item->office_automation_user($user_id)->priority->getHtml() !!}
                                        </td>
                                        <td>
                                            {{$item->office_automation_user($user_id)->CreateFullName()}}
                                        </td>
                                        <td>
                                            {{$item->office_automation_user($user_id)->getInputCreate()}}
                                        </td>
                                        <td>
                                            {{$item->get_create_date_and_time()}}
                                        </td>
                                        <td>
                                            {{$item->end_datetime()}}
                                        </td>
                                        <td>
                                            {{$item->office_automation_user($user_id)->status->caption}}
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
