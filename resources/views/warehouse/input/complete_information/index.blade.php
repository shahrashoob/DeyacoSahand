@extends('layouts.admin._master')

@section('page_header_title',"داشبورد انبار  ")
@section('content')
    <form id="form1" autocomplete="off" action="{{route("wh.input.complete_information.submit",$form)}}"
          method="post"
          novalidate="novalidate">
        @csrf

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


            @include("warehouse.dashboard._general_item_list")
            @if(isset($form->general_items->first()->warehouse_storage_type_id) && $form->general_items->first()->warehouse_storage_type_id==2)
                @include("warehouse.input.complete_information._complete_information")
            @elseif(isset($form->general_items->first()->warehouse_storage_type_id) && $form->general_items->first()->warehouse_storage_type_id==3)
                @include("warehouse.input.complete_information._complete_information_reservoirs")
            @else
                @include("warehouse.input.complete_information._complete_information_without_storage")
            @endif

            @include("warehouse.input.complete_information._script")
            @include("warehouse.dashboard._log")

        </div>
    </form>

@endsection
@section("scripts")
    <script>
        $("#save_data").click(function () {
            $('input').removeAttr('required');
            $("#save_data_status").val("save_data");
        })
        $('#form1').validate({
            rules: {
                "product_id": "required",
            }
        });
    </script>
@endsection


