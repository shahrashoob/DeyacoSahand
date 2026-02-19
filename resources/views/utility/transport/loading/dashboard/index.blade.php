@extends('layouts.admin._master')
@section("page_header_title","داشبورد ارسال بار ")
@section("content")
    <div class="row">


        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست برگ های خروج در انتظار ارسال</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره برگ خروج</th>
                                <th>تاریخ ایجاد</th>
                                <th>درخواست دهنده</th>
                                <th>تعداد بسته بندی</th>
                                <th> وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("utility.transport.loading.dashboard.show_form",$item)}}">{{$item->getCode()}}</a>
                                    </td>
                                    <td>{{$item->get_create_date()}}</td>
                                    <td>
                                        {{$item->getApplicantCaption()}}
                                    </td>
                                    <td>
                                        {{$item->getPackingFromCount()}}
                                    </td>
                                    <td>{{$item->status->caption??""}}</td>


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
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست بارها</h5>



                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره بار</th>
                                <th>ایجاد کننده </th>
                                <th>تاریخ ایجاد</th>
                                <th>نام و نام خانوادگی راننده</th>
                                <th>شماره تماس راننده</th>
                                <th>شماره پلاک خودرو</th>
                                <th> وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($transport_list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("utility.transport.loading.load_registration.show_transport",$item)}}">{{$item->getCode()}}</a>
{{--                                        <a href="{{route("DCBL_QR",[$item,$item->getRandom()])}}">{{$item->getCode()}}</a>--}}
                                    </td>
                                    <td>{{$item->worker->fullname()}}</td>
                                    <td>{{$item->create_datetime()}}</td>
                                    <td>{{$item->car->driver_firstname??""}} {{$item->car->driver_lastname??""}}</td>
                                    <td>{{$item->car->driver_mobile??""}}</td>
                                    <td>{{$item->car->car_plaque??""}}</td>
                                    <td>{{$item->status->caption??""}}</td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$transport_list->firstItem()}}</b>
                        تا
                        <b>{{$transport_list->lastItem()}}</b>
                        از
                        <b>{{$transport_list->total()}}</b>
                        رکورد موجود


                    </div>
                </div>
                <div class="text-center">
                    {{$transport_list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
