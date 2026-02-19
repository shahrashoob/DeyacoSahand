@extends('layouts.admin._master')
@section("page_header_title","کارتابل جاری بازرگانی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("production.production_card._search_view",["route"=>"wh.cd.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>سفارش خرید کالا {{$purchaseOrderProduct->code()}}</h5>
                </div>
                <div class="card-block">

                    @include("component.input._lable",["id"=>"","lable"=>"نام کالا ",
                    "value"=>$order->total_weight??"","class_col"=>"col-md-3"])


                    @include("component.input._lable",["id"=>"","lable"=>"  کد کالا",
                    "value"=>$order->total_weight??"","class_col"=>"col-md-3"])

                    @include("component.input._lable",["id"=>"","lable"=>"مقدار درخواستی",
                    "value"=>$order->total_weight??"","class_col"=>"col-md-3"])

                                        
                    @include("component.input._text",["id"=>"","lable"=>"مقدار سفارش گذاری",
                    "value"=>"","class_col"=>"col-md-3"])
                    
                    
                </div>
               
            </div>
        </div>

    </div>

@endsection
