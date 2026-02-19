@extends('layouts.admin._master')

@section('page_header_title',"داشبورد بسته بندی  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ادغام بسته بندی {{$packing_form->code}}</h5>
                </div>
                <div class="card-block ">

                    <form id="form1" action=" {{route("fabric_raw.packing_form.merger.submit",$packing_form)}}"
                          method="post">
                        @csrf
                        @include("component.input._number",["id"=>"code","lable"=>"کد بسته بندی مقصد(بدون DCPK)","value"=>"","class_col"=>"col-md-4"])
                        <div class="col-md-12"   >


                            <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
                               class="btn btn-outline-dark" style="width: 100px">بازگشت</a>
                            <button type="submit" class="btn btn-primary" style="width: 100px" onclick="return confirm('آیا از ادعام بسته بندی اطمینان دارید؟')">تایید</button>

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
                "code":{ required:true},
            }
        });
    </script>
@endsection
