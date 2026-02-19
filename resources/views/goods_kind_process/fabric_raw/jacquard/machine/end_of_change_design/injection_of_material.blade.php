@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')

                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.machine.end_of_change_design.submit_injection_of_material",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        @include("goods_kind_process.general.machine.injection_of_material._injection")

                        <br/>
                        <div class="col-sm-12 center" >
                            <h5> آیا نخ پود {{$machine->caption}} را تغییر داده اید؟</h5>


                            <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}" class="btn btn-lg btn-danger"
                               onclick="return alert('لطفا تنظیمات را انجام دهید.')">خیر</a>


                            <button type="submit" class="btn btn-lg btn-success">بله و ادامه</button>
                        </div>

                    </form>

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
