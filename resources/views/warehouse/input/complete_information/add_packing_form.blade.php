@extends('layouts.admin._master')

@section('page_header_title',"داشبورد انبار  ")
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


            @include("warehouse.dashboard._general_item_list")
            @include("warehouse.input.complete_information._complete_information")
            @include("warehouse.input.complete_information._script")
            @include("warehouse.dashboard._log")

        </div>


@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "product_id": "required",
            }
        });
    </script>
@endsection


