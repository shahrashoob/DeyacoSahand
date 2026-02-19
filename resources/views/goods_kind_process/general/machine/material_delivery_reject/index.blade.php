@extends('layouts.admin._master') @section('page_header_title',"داشبورد  بافندگی")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5> عدم تایید تحویل مواد اولیه از انبار
                        برای {{$machine->warehouse->caption??""}}</h5></div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route($route_path."submit",$machine)}}"
                          method="post" autocomplete="off" novalidate="novalidate"> @csrf
                        <div class="row">
                            <div class="w-100"></div>
                            @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$product_request_form->getCreateFormUser()->fullname()])

                            @foreach($product_request_form->forms as $product_request_form_form)
                                @if($product_request_form_form->form->status_id ==500000535 )
                                    <div class="col-md-12">
                                        @include("component.input._checkbox_simple",["id"=>"form_".$product_request_form_form->form_id,"label"=> "برگ خروج" .$product_request_form_form->form->getCode(),"checked"=>false])


                                    </div>
                                    <br/>  <br/>

                                @endif

                            @endforeach
                            @include("component.input._textarea",["id"=>"description","label"=>"توضحات علت عدم تایید فرم"])

                            <div class="col-md-12 center">
                                <hr/>
                                <a href="{{route($dashboard_route."view",$machine)}}"
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

@endsection @section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    <script> $('#form1').validate({
            rules: {
                "description": "required",
            }
        }); </script>
@endsection
