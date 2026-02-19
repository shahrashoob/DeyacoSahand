@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تحویل انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم درخواست کالا از انبار - کد {{$product_request_form->getCode()}} </h5>
                </div>

                <div class="card-block">


                    <div class="row">
                        @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$product_request_form->applicant->fullCaption()])
                        @include("component.input._lable",["label"=>"وضعیت","value"=>$product_request_form->getStatus()])
                        @include("component.input._lable",["label"=>"تاریخ و زمان ارسال کالا","value"=>$product_request_form->coordinate_date_time()])


                        <div class="col-md-6 offset-md-6">
                            <div class="form-group">
                                <label>شماره مرجع ({{$product_request_form->applicant_type->reference_caption}}):</label>
                                <b>{{$product_request_form->getReferenceNumber()}}</b>
                            </div>
                        </div>


                        @include("warehouse.out.dashboard._prf_list")


                    </div>

                    <hr/>

                    <div style="text-align: center">
                        <a href="{{route("wh.out.dashboard.index")."?page=".$page}}"
                           class="btn btn-outline-dark">بازگشت</a>



                        @if(in_array( $product_request_form->status_id, [7005001,7005002,7005005,7005008,7005004]) )


                            <a class="btn btn-primary"
                               href="{{route("wh.out.delivery.index",[$product_request_form,0,$page])}}">
                                انتخاب کالا</a>

                            <a class="btn btn-primary"
                               href="{{route("wh.out.delivery.checkout",[$product_request_form,0,$page])}}">
                                تحویل کالا </a>

                        @endif

                        @if($post_user->checkButtonPermission("wh.transport.dashboard.index") && in_array( $product_request_form->status_id, [7005001,7005002,7005005,7005008,7005004])  )
                            <a href="{{route("wh.transport.dashboard.index",[$product_request_form,$page])}}"
                               class="btn btn-primary"
                            >
                                ثبت بسته بندی حمل و نقل
                            </a>
                        @endif

                    </div>

                </div>


            </div>

        </div>

        @include("warehouse.out.dashboard._form_list")
        @include("warehouse.out.dashboard.log")

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
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",
            }
        });


    </script>
@endsection


