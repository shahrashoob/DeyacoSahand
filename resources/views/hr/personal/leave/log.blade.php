@extends('layouts.admin._master')

@section("page_header_title","کارتابل  منابع انسانی ")


@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> سابقه اقدام بر روی {{$leave_overtime->leave_overtime_type->caption}} </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>تاریخ و زمان</th>
                                <th>اقدام کننده</th>
                                <th>رویداد</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($leave_overtime->logs as $item)
                                <tr>
                                    <td title="{{$item->id}}">  {{$row++}}</td>
                                    <td>
                                        {{$item->get_created_at()}}
                                    </td>
                                    <td>{{$item->worker->fullname()}}</td>
                                    <td>
                                        {{$item->event->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->getStatus()}}
                                    </td>
                                </tr>
                                @if($item->message)
                                    <tr>
                                        <td colspan="5" class="alert-info">
                                            {{$item->message->text??""}}
                                        </td>
                                    </tr>
                                @endif

                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <a href="{{route("hr.personal.current_user",[$leave_overtime->worker,$leave_overtime->worker->random])}}"
                       class="btn btn-outline-dark">بازگشت</a>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "leave_type_id_auto": "required",
                "replace_user_id_auto": "required",
                "end_datetime_value": "required",
                "end_time_h": "required",
                "start_datetime_value": "required",
                "start_time_h": "required"
            }
        });
    </script>
@endsection
