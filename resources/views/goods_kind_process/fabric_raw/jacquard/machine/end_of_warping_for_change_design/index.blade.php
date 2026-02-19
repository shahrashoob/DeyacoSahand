@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> پایان گره زنی تغییر کالیته   {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("fabric_raw.jacquard.machine.end_of_warping_for_change_design.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="w-100"></div>

                            @include("component.input._radio",
                            ["lable"=>"آیا ماشین نیاز به لامل ریزی دارد؟","id"=>"has_pining","radios"=>[["value"=>1,"label"=>"بله"],["value"=>-1,"label"=>"خیر"]]])

                        <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>


                            <button type="submit" class="btn btn-success">ثبت فرم</button>


                    </form>
                </div>
            </div>
        </div>


    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "shift_work_id_auto": "required",

            }
        });
    </script>
@endsection
