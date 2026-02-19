@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار  ")

@section('content')

    <div class="row">
        <form id="form1" autocomplete="off" action="{{route("wh.entry_form_to_warehouse_confirm")}}"
              method="post"
              novalidate="novalidate">
            @csrf

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>تایید اطلاعات فرم</h5>
                    </div>
                    <div class="card-block">


                        <div class="row">
                                @include("component.input._lable",[
                                    "label"=>"  کارخانه / شرکت ",
                                    "value"=>$request->factory_id_auto
                                    ])

                                @include("component.input._lable",[
                                    "id"=>"warehouse_id",
                                    "label"=>"  انبار ",
                                    "value"=>$request->warehouse_id_auto
                                    ])


                                @include("component.input._lable",[
                                    "id"=>"opp_kind_id",
                                    "label"=>" طرف حساب ",
                                    "value"=>$request->opp_kind_id_auto
                                    ])



                                @include("component.input._lable",[
                                    "id"=>"trans_kind_id",
                                    "label"=>" نوع تراکنش  ",
                                    "value"=>$request->trans_kind_id_auto
                                    ])

                            @include("component.input._lable",["label"=>"مرکز هزینه","id"=>"ic","value"=>$request->ic,"class_col"=>"col-md-4"])

                            @include("component.input._lable",["label"=>"شرح ","value"=>$request->description])

                        </div>


                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>لیست محصولات </h5>
                    </div>
                    <div class="card-block">

                        <div class="row">
                            @php $error=0; @endphp
                            @for($i= 1; $i < 11; $i++)
                                @php $pid="product_".$i."_auto"; @endphp
                                @if($request->$pid!="")
                                    @include("component.input._lable",[
                                        "id"=>"product_".$i,
                                        "label"=>"  محصول ".$i,
                                    "value"=>$request->$pid
                                        ])

                                    @include("component.input._lable",["label"=>"مقدار ","value"=>$request->data[$i]["carton"],"class_col"=>"col-md-2"])
                                    <div class="w-100"></div>
                                    @php

                                        $error+=$request->data[$i]["carton"]==""?1:0;
                                    @endphp
                                @endif
                            @endfor

                        </div>


                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <a href="{{route("wh.entry_form_to_warehouse")}}" class="btn btn-outline-dark">بازگشت</a>
                @if($error > 0)
                    <div class="alert alert-danger">مرکز هزینه / مقدار {{$error}} مورد از محصولات به درستی وارد نشده است.
                    </div>
                @else
                    <button type="submit" onclick="return confirm('آیا از ثبت فرم اطمینان دارید؟')" class="btn btn-success"> تایید فرم</button>

                @endif

            </div>

        </form>
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
                "opp_kind_id_auto": "required",
                "warehouse_id_auto": "required",
                "product_1_auto": "required"
            }
        });
    </script>
@endsection

