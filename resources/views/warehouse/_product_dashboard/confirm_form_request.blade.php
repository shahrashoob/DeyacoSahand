@extends('layouts.admin._master')

@section('page_header_title',"کارتابل جاری انبار محصول "." سفارش:".$order->code())

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تایید درخواست محصول از انبار </h5>
                </div>
                <div class="card-block">


                    <form id="form1" action="{{route("wh.product.store_form_request",[$order,$form])}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        @include("warehouse.product_dashboard._form_list_confirm",["form"=>$form])


                        <div id="button_list">

                            <a href="{{route("wh.product.delivery_form_request",$order)}}" class="btn btn-outline-dark">بازگشت</a>

                            <button id="btn_confirm" type="submit" class="btn btn-success"> تایید نهایی</button>
                            @include("component.button.loading")
                        </div>

                    </form>


                </div>
            </div>
        </div>


    </div>

@endsection

@section("scripts")
    <script>
        $("#btn_confirm").click(function () {

            if ($(this).hasClass("disabled")) {
                return false;
            }

            if (confirm(" آیا از ثبت اطمینان دارید؟ ")) {
                $(this).addClass("disabled");
                $("#button_list .btn").addClass("invisible disabled");
                $("#button_list .loading").removeClass("invisible");
                return 1;
            }

            return false;
        });
    </script>
@endsection
