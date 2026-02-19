@extends('layouts.admin._master')
@section("page_header_title","داشبورد پالت ها ")
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست کالاهای داخل پالت {{$pallet->code}} </h5>

                    <a class="btn btn-primary" href="{{route("wh.pallet.add_packing_form.index",$pallet)}}" >
                        <i class="fa  feather icon-log-in fa-rotate-90" ></i>
                        افزود به پالت
                    </a>
                    <a class="btn btn-danger" href="{{route("wh.pallet.remove_packing_form.index",$pallet  )}}" >
                        <i class="fa  feather icon-log-out fa-rotate-270   " ></i>
                        کسر از پالت
                    </a>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>

                                <th>کد کالا</th>
                                <th>نام کالا</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($product_list as $item)
                                <tr>
                                    <td>{{$row++}}</td>

                                    <td>
                                        {{$item->code}}
                                    </td>

                                    <td>
                                        {{$item->caption}}
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
            <div class="card">
                <div class="card-header">
                    <h5> پالت {{$pallet->getCodeNumber()}} </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>

                                <th>کد بسته بندی</th>
                                <th>وضعیت بسته بندی</th>
                                <th>شماره فرم ورود به انبار</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($pallet->items as $item)
                                <tr>
                                    <td>{{$row++}}</td>

                                    <td>
                                        {{$item->packing_form->code}}
                                    </td>
                                    <td>{{$item->packing_form->status->caption}}</td>
                                    <td>{{$item->form->code??""}}</td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <a href="{{route("wh.pallet.dashboard.index")}}"
                       class="btn btn-outline-dark"
                       style="width: 130px">بازگشت</a>
                </div>

            </div>

        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
