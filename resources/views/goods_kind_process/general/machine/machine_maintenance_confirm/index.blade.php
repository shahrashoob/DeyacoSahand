@extends('layouts.admin._master') @section('page_header_title',"داشبورد  بافندگی")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5> عملیات های تعمیرات و نگهداری
                        برای {{$machine->warehouse->caption??""}}</h5></div>
                <div class="card-block">

                    <div class="row">
                        <div class="w-25"></div>
                        @if(count($list)==0)
                            <div class="col-md-6 center">
                                <div class="alert alert-warning">
                                    هیچ عملیات در حال انجام برای ماشین وجود ندارد.
                                </div>
                            </div>
                        @else
                            <div class="col-md-6 center">
                                <table class="table">
                                    <tr class="col-md-12 center">
                                        <th>ردیف
                                        </th>
                                        <th>شماره درخواست</th>
                                        <th>تاریخ ایجاد</th>
                                        <th>
                                            نوع عملیات
                                        </th>
                                        <th>وضعیت</th>
                                        <th></th>
                                    </tr>
                                    @php $row=1;@endphp
                                    @foreach($list as $item)
                                        <tr>
                                            <td>{{$row++}}</td>
                                            <td>{{$item->code}}</td>
                                            <td>{{$item->get_create_date_and_time()}} </td>
                                            <td>{{$item->maintenance_type->caption??""}} </td>
                                            <td>{{$item->status->caption??""}} </td>
                                            <td>
                                                @if($item->status_id ==6003005)
                                                    <a href="{{route($route_path."confirm",[$machine,$item])}}"
                                                       onclick="confirm('آیا از تایید اطمینان دارید؟')"
                                                       class="btn btn-success btn-sm">
                                                        تایید
                                                    </a>
                                                    <a href="{{route($route_path."reject",[$machine,$item])}}"
                                                       onclick="confirm('آیا از عدم تایید اطمینان دارید؟')"
                                                       class="btn btn-danger btn-sm">
                                                        عدم تایید
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6">
                                                <div class="alert alert-info" style="text-align: right; ">
                                                    {!! $item->description !!}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach


                                </table>
                            </div>
                        @endif
                        <div class="col-md-12 center">

                            <a href="{{route($dashboard_route."view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>


                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    </div> @endsection @section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/> @endsection @section("scripts")
    <script> $('#form1').validate({
            rules: {
                "description": "required",
            }
        }); </script> @endsection
