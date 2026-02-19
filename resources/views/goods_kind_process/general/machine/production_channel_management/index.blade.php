@extends('layouts.admin._master',["keypress_enable"=>1])
@section('page_header_title',"داشبورد  بافندگی")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5> مدیریت کانال های تولید
                        {{$machine->caption??""}}</h5>
                </div>
                <div class="card-block">
                    <div class="row">
                        <div class="col-md-12 center">
                            <table class="table">
                                <tr class="col-md-12 center">
                                    <th>
                                    </th>
                                    <th>اولویت</th>
                                    <th>کد کانال</th>
                                    <th>تاریخ ایجاد کانال</th>
                                    <th>
                                        نام کانال تولید
                                    </th>
                                    <th>
                                        وضعیت کانال تولید
                                    </th>
                                    <th>
                                        حداقل ظرفیت
                                    </th>
                                    <th>
                                        حداکثر ظرفیت
                                    </th>
                                    <th>
                                        ظرفیت باقی مانده
                                    </th>
                                    <th></th>
                                </tr>
                                @php $row=1;@endphp
                                @foreach($list as $item)
                                    <tr @if($item->status_id == 3358001) style="font-weight: bold"
                                        class=" text-success" @endif>
                                        <td>{{$row++}}</td>
                                        <td>
                                            {{$item->priority_number}}
                                        </td>
                                        <td>
                                            {{$item->getCode()}}
                                        </td>
                                        <td>
                                            {{$item->create_datetime()}}
                                        </td>
                                        <td>
                                            {{$item->production_channel_type->caption}}
                                        </td>
                                        <td>
                                            {{$item->status->caption}}
                                        </td>
                                        <td>
                                            {{$item->min_capacity}}
                                        </td>
                                        <td>
                                            {{$item->max_capacity}}
                                        </td>
                                        <td>
                                            {{$item->remaining_capacity}}
                                        </td>
                                        <td>
{{--                                        @if($item->status_id == 3358002)--}}
{{--                                            <a href="{{route($route_path."update_to_canceled",[$machine,$item])}}" class="btn btn-danger btn-sm" onclick="return confirm('آیا از کنسل کردن کانال تولید اطمینان دارید؟')">--}}
{{--                                                کنسل کردن--}}
{{--                                            </a>--}}
{{--                                        @endif--}}
                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                        </div>
                        <div class="col-md-12">
                            <a href="{{route($dashboard_route."view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "trans_kind_id_auto": "required",
            }
        });
    </script>
@endsection
