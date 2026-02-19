@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ساعت های ورود و خروج {{$worker->fullname()}}  در تاریخ  {{$persian_date}}</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>
                                <td>ردیف</td>
                                <th>تاریخ و ساعت ورود</th>
                                <th>اقدام کننده</th>
                                <th>تاریخ و ساعت خروج</th>
                                <th>اقدام کننده</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($entry_log_list as $item)
                                <tr>
                                    <td title="{{$item->id}}">  {{$row++}}</td>
                                    <td class='{{$item->entry_permit_status_id==461000100?"alert-danger":""}}'>
                                        {{$item->entry_datetime(" Y/m/d - %A - H:i:s ")}}

                                    </td>
                                    <td>
                                        {{$item->entry_register_worker?$item->entry_register_worker->fullname():""}}
                                    </td>
                                    <td class='{{$item->exit_permit_status_id==461000100?"alert-danger":""}}'>
                                        {{$item->exit_datetime(" Y/m/d - %A - H:i:s ")}}

                                    </td>
                                    <td>
                                        {{$item->exit_register_worker?$item->exit_register_worker->fullname():""}}
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="row">
                        <div class="md-col-12 " style="margin: auto">
                          <a href="{{url()->previous()}}" class="btn btn-outline-dark">بازگشت</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>



@endsection
@section("styles")
@endsection
@section("scripts")
@endsection

