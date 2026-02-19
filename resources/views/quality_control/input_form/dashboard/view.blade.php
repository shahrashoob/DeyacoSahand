@extends('layouts.admin._master')

@section('page_header_title',"داشبورد کنترل کیفیت  ")
@section('content')
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم ورود به انبار {{$form->code}}</h5>
                </div>
                <div class="card-block  ">
                    @include("warehouse.dashboard._input_form_info")
                </div>
            </div>
        </div>


        @php
            if(count($form->item)>0){
				    $sum_amount     =round($form->item()->sum( "amount"),2 );
                       $sum_sub_amount =round( $form->item()->sum( "sub_amount") ,2);
                       $unit=$form->item->first()->product->unit;
                       $sub_unit=$form->item->first()->product->sub_unit;
                       }
        @endphp

        @include("warehouse.dashboard._general_item_list")
        @include("warehouse.out.exit_form.qr._list_group_by_products",["caption"=>"فرم ورود"])
        @include("warehouse.dashboard._item_list",["show_packing_code"=>!($form->status_id ==500000410 || $form->status_id==500000420)])
        @include("warehouse.dashboard._log")

        <div class="col-md-12 center">

            <a href="{{route("quality_control.dashboard.index")}}"
               class="btn btn-outline-dark"
               style="width: 130px">بازگشت</a>
            @if ( $form->status_id == 500000535 && $post_user->checkButtonPermission( "quality_control.reject_product.cheek_quality.index" ))
                <form id="form1" autocomplete="off"
                      action="{{route("quality_control.input_form.confirm_quality.confirm_input_form",[$form])}}"
                      method="post"
                      novalidate="novalidate"
                      style="display: inline"
                >
                    @csrf
                    <button type="submit" class="btn btn-primary"
                            onclick="return confirm('آیا از تایید فرم ورود اطمینان دارید؟')">
                         تایید کیفی کل فرم
                    </button>


                    <a href="{{route("quality_control.input_form.confirm_quality.reject_input_form",[$form])}}" type="submit" class="btn btn-danger"
                       onclick="return confirm('آیا از عدم تایید فرم ورود اطمینان دارید؟')">عدم تایید
                    </a>
                </form>
            @endif

        </div>

    </div>

@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
            }
        });
    </script>
@endsection
