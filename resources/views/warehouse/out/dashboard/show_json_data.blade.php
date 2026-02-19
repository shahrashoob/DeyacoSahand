@extends('layouts.admin._master',["no_persian"=>1])

@section('page_header_title',"داشبورد  تحویل انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم درخواست کالا از انبار - کد {{$product_request_form->getCode()}} </h5>
                </div>

                <div class="card-block">


               @include("component.json_data.json_data_view",["object"=>$product_request_form_log->json_data_list])

                    <hr/>

                    <div style="text-align: center">
                        <a href="{{route("wh.out.dashboard.view",$product_request_form)."/".$page}}"
                           class="btn btn-outline-dark">بازگشت</a>




                    </div>

                </div>


            </div>

        </div>


    </div>

@endsection
