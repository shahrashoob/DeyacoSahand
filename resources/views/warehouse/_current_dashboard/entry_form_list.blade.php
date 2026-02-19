@extends('layouts.admin._master')
@section("page_header_title","داشبورد انبار")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست فرم های دریافت از تولید </h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره فرم </th>
                                <th>شماره انبار </th>
                                <th>نوع تراکنش </th>
                                <th>تاریخ ثبت  </th>
                                <th>تنظیم کننده</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->code()}}</td>
                                    <td>{{$item->warehouse->code}}</td>
                                    <td>{{$item->trans_kind_item->caption??""}}</td>
                                    <td>{{$item->get_create_date_and_time()}}</td>
                                    <td>{{$item->worker->fullname()}}</td>
                                    <td>
                                        <a href="{{route("wh.print.form",$item)}}" >
                                            <i class="fa fa-print"></i> دانلود فرم
                                        </a>
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
                <div class="text-center" >
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
