@extends('layouts.admin._master')

@section('page_header_title',"داشبورد بسته بندی  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تکمیل اطلاعات بسته بندی {{$packing_form->code}}</h5>
                </div>
                <div class="card-block ">

                    <form id="form1"
                          action=" {{route("fabric_raw.packing_form.complete_information.submit",$packing_form)}}"
                          method="post">
                        @csrf

                        @include("goods_kind_process.fabric_raw.packing_form.complete_information._info")

                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
                               class="btn btn-outline-dark" style="width: 100px">بازگشت</a>
                            <button type="submit" class="btn btn-primary" style="width: 100px">تایید</button>

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
                "gross_weight": {required: true, min: .0001},
            }
        });
    </script>

@endsection
