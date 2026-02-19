@extends('layouts.admin._master',["no_persian"=>1])
@section("page_header_title","داشبورد انبار ")
@section("content")

    <form id="form1" action="{{route("wh.warehouse_handling.end_of_review.submit_reading_packing_form_after",[$warehouse_handling,$warehouseHandlingPackingForm])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>خواندن بسته بندی
                        </h5>
                    </div>
                    <div class="card-block">

                        <div class="row">

                            <div class="col-md-12 alert alert-info">
                                لطفا کد بسته بندی مورد نظر را در کادر زیر وارد نمایید.
                            </div>


                            @include("component.input._text",["id"=>"packing_code","value"=>"","label"=>"کد بسته بندی (بدون DCPK)","class_col"=>"col-md-3"])


                            <div class="col-md-12">

                                <a href="{{route("wh.warehouse_handling.dashboard.view",$warehouse_handling)}}"
                                   class="btn btn-outline-dark">بازگشت</a>


                                <button type="submit" class="btn btn-primary"> ثبت بسته بندی </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
@section("styles")
@endsection
@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "packing_code": "required",

            }
        });
    </script>
@endsection
