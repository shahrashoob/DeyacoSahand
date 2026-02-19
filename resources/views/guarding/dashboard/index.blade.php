@extends('layouts.admin._master')
@section("page_header_title","داشبورد نگهبانی ")
@section("content")
    <div class="row">


        @include("utility.transport.public._transport_list",[
         "transport_list"=>$list_input_transport,
         "header_caption"=>"لیست بارهای در انتظار تایید  ",
         "route_name"=>"guarding.dashboard.show_transport"
     ])



    <div class="col-sm-12">
        {{--            @include("orders._search_view",["route"=>"wh.product.list"])--}}
        <div class="card">
            <div class="card-header">
                <h5>لیست فرم های در انتظار تایید ورود </h5>

            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>کد فرم انبار</th>
                            <th>درخواست دهنده</th>
                            <th>تاریخ درخواست</th>
                            <th> وضعیت</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($list_input_form as $item)
                            <tr>
                                
                                <td>{{++$row}}</td>
                                <td>
                                    <a href="{{route("guarding.dashboard.show_input_form",$item)}}">{{$item->getCode()}}</a>
                                </td>
                                <td>{{$item->worker->fullname()}}</td>
                                <td>{{$item->get_create_date_and_time()}}</td>
                                <td>{{$item->status->caption??""}}</td>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
                <div class="float-left">
                    نمايش رکوردهای
                    <b>{{$list_input_form->firstItem()}}</b>
                    تا
                    <b>{{$list_input_form->lastItem()}}</b>
                    از
                    <b>{{$list_input_form->total()}}</b>
                    رکورد موجود


                </div>
            </div>
            <div class="text-center">
                {{$list_input_form->links('pagination::bootstrap-4')}}
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        {{--            @include("orders._search_view",["route"=>"wh.product.list"])--}}
        <div class="card">
            <div class="card-header">
                <h5>لیست فرم های در انتظار تایید خروج</h5>

            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>کد برگ خروج</th>
                            <th>ایجاد کننده</th>
                            <th>تاریخ ایجاد درخواست</th>
                            <th> وضعیت</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($list_output_form as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>
                                    <a href="{{route("guarding.dashboard.show_exit_form",$item)}}">{{$item->getCode()}}</a>
                                </td>

                                <td>    {{$item->getApplicantCaption()}}</td>
                                <td>{{$item->get_create_date_and_time()}}</td>
                                <td>{{$item->status->caption??""}}</td>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
                <div class="float-left">
                    نمايش رکوردهای
                    <b>{{$list_output_form->firstItem()}}</b>
                    تا
                    <b>{{$list_output_form->lastItem()}}</b>
                    از
                    <b>{{$list_output_form->total()}}</b>
                    رکورد موجود


                </div>
            </div>
            <div class="text-center">
                {{$list_output_form->links('pagination::bootstrap-4')}}
            </div>
        </div>
    </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
