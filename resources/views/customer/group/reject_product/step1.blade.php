@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان - سفارش  ".$order->code())

@section('content')
    <div class="row">

        <div class="col-sm-12">


            <form id="form1" autocomplete="off"
                  action="{{route($route_path."confirm",[$order,$form])}}"
                  method="post"
                  novalidate="novalidate">
                @csrf


                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5> لیست بسته بندی های در حال مرجوع </h5>
                        </div>

                        <div class="card-block">
                            <div class="table-responsive">
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>کد بسته بندی</th>
                                        <th>مقدار</th>
                                        <th> بسته بندی سالم</th>
                                        <th>مقدار مرجوعی</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($reject_packing_list as $item)
                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>
                                                <input type="hidden" value="{{$item->id}}" name="data[reject_packing][{{$item->id}}]">
                                                {{$item->getCode()}}
                                            </td>
                                            <td>{{$item->getFinalAmount()}} {{$item->getUnitCaption("unit","caption")}}</td>
                                            <td>
                                                <input type="checkbox" disabled
                                                    {{$amount_remaining[$item->id]?"":"checked"}}
                                                >
                                            </td>
                                            <td>
                                                {{$amount_remaining[$item->id]!=null ? $amount_remaining[$item->id]." ".$item->getUnitCaption("unit","caption"):""}}
                                                <input type="hidden" value="{{$amount_remaining[$item->id]}}" name="data[amount_remaining][{{$item->id}}]">

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
                            <h5> ثبت نهایی مرجوعی کالا </h5>
                        </div>

                        <div class="card-block">

                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                    {{$reject_product_description}}
                                    </div>
                                </div>

                                    @include("component.input._aotocomplet2",["id"=>"reject_product_reason_type_id","label"=>"علت مرجوعی","option"=>$reject_product_reason_type_option["items"],"class_col"=>"col-md-4"])

                            <div class="col-md-12">
                                <input type="checkbox" id="confirm_form">
                                از مرجوعی کالا ها اطمینان دارم
                            </div>
                            </div>



                    </div>
                </div>


                <div class="center">
                    <a class="btn btn-outline-dark"
                       href="{{route($dashboard_route,$order)}}">بارگشت</a>

                    <button id="submit_btn" type="submit" class="btn btn-primary hidden">تایید و ثبت نهایی</button>

                </div>
            </form>

        </div>


    </div>

@endsection

@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

    <style>


        .hidden {
            display: none;
        }
    </style>
@endsection
@section("scripts")
    <script>
        $("#check_all").click(function () {
            $(".check_box").prop('checked', $("#check_all").is(':checked'));
        })
        $('#form1').validate({
            rules: {
                "reject_product_reason_type_id_auto": "required",
            }
        });


        $("#confirm_form").click(function () {

            if ($(this).is(':checked')) {

                $("#submit_btn" ).removeClass("hidden");
            } else {
                $("#submit_btn" ).addClass("hidden");
            }
        })


    </script>
@endsection
