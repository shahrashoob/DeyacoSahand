@extends('layouts.admin._master',["keypress_enable"=>1])
@section('page_header_title',"داشبورد  بافندگی")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5> عدم تایید تحویل مواد اولیه از انبار
                        برای {{$warehouse->caption??""}}</h5></div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route($route_path."submit_reject",[$form,$product_request_form,$warehouse])}}"
                          method="post" autocomplete="off" novalidate="novalidate"> @csrf
                        <div class="row">
                            <div
                                class="w-100"></div>
                            @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$product_request_form->getCreateFormUser()->fullname()])

                            @include("component.input._lable",["id"=>"form_".$form->id,"label"=> "برگ خروج" ,"value"=>$form->getCode()])

                            @include("component.input._textarea",["id"=>"description","label"=>"توضحات علت عدم تایید فرم"])


                            <div class="col-md-12 center">
                                <hr/>
                                <a href="{{route($route_path."index")}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('آیا از عدم تایید تحویل کالا  اطمینان دارید؟')">عدم
                                    تایید تحویل
                                    کالا
                                </button>
                            </div>
                        </div>
                    </form>
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
                "description": "required",
            }
        });
    </script>
@endsection
