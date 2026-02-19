@extends('layouts.admin._master')

@section('page_header_title'," داشبورد جاری تولید - ".$production->product->goods_kind->caption)

@section('content')


        <div class="row">

            <div class="w-25"></div>
            <div class="col-sm-12 col-md-6 col-md-offset-3">
                <h6>شما می توانید
                    کالای دیگری
                    را بر روی
                    باندهای آزاد
                    ماشین
                    {{$machine->code." ".$machine->caption}}
                    ببافید، در صورت تایید یکی از کارت های تولید را انتخاب کنید.
                </h6>
                <div class="card">

                    @foreach($production_allow_list as $item)
                        <a
                            href="{{route("fabric_raw.jacquard.machine_allocation.select_other_band_production",[$machine->id,$production,$item])}}"
                            class="btn-check"
                            onclick="return confirm('آیا از انتخاب کارت تولید اطمینان دارید؟')"
                        >

                        <div class="card-block border-bottom">
                            <div class="row d-flex align-items-center">
                                <div class="col-auto">
                                        <i class="feather f-30 text-c-green  icon-square"></i>

                                  </div>
                                <div class="col">

                                    <h3 class="f-w-300">کارت تولید {{$item->serial()}}  </h3>
                                    <span class="d-block text-uppercase"> {{$item->product->caption}}  <b></b></span>
                                </div>
                            </div>
                        </div>
                        </a>
                        @endforeach


                </div>
            </div>
            <div class="col-sm-12" style="text-align: center">

{{--                <a href="{{route("fabric_raw.machine_allocation.select_input_line",[$machine ,0])}}"  class="btn btn-success" id="btn_replace">صرف نظر و ادامه تخصیص</a>--}}
                <a href="{{route("fabric_raw.production_card.view_card",$production)}}" class="btn btn-outline-dark">بازگشت
                    به کارتابل تولید</a>
            </div>


        </div>

@endsection

@section("style")
    <style>
        .card-block{
            padding: 15px 20px !important;
        }
    </style>
    @endsection


