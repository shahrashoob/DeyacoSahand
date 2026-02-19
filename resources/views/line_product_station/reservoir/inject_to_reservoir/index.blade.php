@extends('layouts.admin._master')

@section('page_header_title',"داشبورد مدیریت  ")

@section('content')
    <div class="row">


        @include("line_product_station.reservoir.dashboard._item_list",["packing_form"=>$reservoir->packing_form])



        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>مخزن شماره {{$reservoir->id}} - {{$reservoir->caption}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.reservoir.inject_to_reservoir.submit",$reservoir)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                    <div class="row">


                        @include("component.input._number",["id"=>"packing_form_code","lable"=>"کد بسته بندی (DCPK/)","value"=>"","class_col"=>"col-md-4"])


                       <div class="col-md-12">
                           <a href="{{route("line_product_station.reservoir.dashboard.index",$reservoir)}}"
                              class="btn  btn-outline-dark">بازگشت</a>
                           <button type="submit" class="btn btn-primary">تزریق بسته بندی</button>
                       </div>

                    </div>
                    </form>




                </div>
            </div>
        </div>


    </div>

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "packing_form_code": "required",
            }
        });
    </script>
@endsection
