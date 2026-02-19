@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست جدول کارکرد روزانه در تاریخ {{$date}} - {{$worker->fullname()}}</h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>ساعت شروع</th>
                                <th>ساعت پایان</th>
                                <th>گروه شیفت</th>
                                <th> شیفت</th>
                                <th> وضعیت حضور</th>
                                <th> مرخصی</th>
                                <th>کد اضافه کاری</th>
                                <th> ماموریت</th>
                                <th> جابجایی</th>
                                <th> جانشین</th>
                                <th>تعجیل مجاز ورود</th>
                                <th>تاخیر مجاز ورود</th>
                                <th>تعجیل مجاز خروج</th>
                                <th>تاخیر مجاز خروج</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($intervals as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        {{$item->start_time()}}
                                    </td>
                                    <td>
                                        {{$item->end_time()}}
                                    </td>
                                    <td title="{{$item->split_shift_type_group_id}}">
                                        {{$item->split_shift_type_group_id}}
                                    </td>
                                    <td title="{{$item->shift_work_day_id}}">
                                        {{$item->shift_work_day_id}}
                                    </td>
                                    <td title="{{$item->present_in_organ}}">
                                        {{$item->present_in_organ}}
                                    </td>
                                    <td title="{{$item->leave_id}}">
                                        {{$item->leave_id}}
                                    </td>
                                    <td title="{{$item->overtime_id}}">
                                        {{$item->overtime_id}}
                                    </td>
                                    <td title="{{$item->mission_id}}">
                                        {{$item->mission_id}}
                                    </td>
                                    <td title="{{$item->replacement_id}}">
                                        {{$item->replacement_id}}
                                    </td>
                                    <td title="{{$item->present_in_organ_for_other}}">
                                        {{$item->present_in_organ_for_other}}
                                    </td>
                                    <td title="{{$item->allowed_earlier_time_for_entry}}">
                                        {{$item->allowed_earlier_time_for_entry}}
                                    </td>
                                    <td title="{{$item->allowed_delay_time_for_entry}}">
                                        {{$item->allowed_delay_time_for_entry}}
                                    </td>
                                    <td title="{{$item->allowed_earlier_time_for_exit}}">
                                        {{$item->allowed_earlier_time_for_exit}}
                                    </td>
                                    <td title="{{$item->allowed_delay_time_for_exit}}">
                                        {{$item->allowed_delay_time_for_exit}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>
        </div>


        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>گزارش کارکرد {{$date}} - {{$worker->fullname()}}</h5>

                </div>
                <div class="card-block">
                    <table>
                        <thead></thead>
                        <tbody>
                        @if($operation)
                            <tr>
                                <td>تعجیل مجاز ورود</td>
                                <td>{{$operation->allowed_earlier_time_for_entry}}</td>
                            </tr>
                            <tr>
                                <td>تاخیر مجاز ورود</td>
                                <td>{{$operation->allowed_delay_time_for_entry}}</td>
                            </tr>
                            <tr>
                                <td>تعجیل مجاز خروج</td>
                                <td>{{$operation->allowed_earlier_time_for_exit}}</td>
                            </tr>
                            <tr>
                                <td>تاخیر مجاز خروج</td>
                                <td>{{$operation->allowed_delay_time_for_exit}}</td>
                            </tr>
                            <tr>
                                <td>غیبت داخلی</td>
                                <td>{{$operation->internal_absence}}</td>
                            </tr>
                            <tr>
                                <td>غیبت قانونی</td>
                                <td>{{$operation->legal_absence}}</td>
                            </tr>
                            <tr>
                                <td>کل مدت حضور  </td>
                                <td>{{$operation->present_in_organ}}</td>
                            </tr>
                            <tr>
                                <td>حضور مجاز </td>
                                <td>{{$operation->allowed_present_in_organ}}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
