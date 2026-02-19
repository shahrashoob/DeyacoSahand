@extends('layouts.admin._master')

@section('page_header_title'," ")

@section('content')
    <div class="row">

        <div class="col-sm-12" style="text-align: center">
            {{--       <img src="{{asset("assets/images/bg_2.png")}}" style="height:350px; margin-bottom: 30px" />--}}

            <br/>
            <br/>


        </div>
        <div class="col-md-12" >

            <div class="text" style="padding: 10px; font-size:16px;  ">

                <br/>
                بروز رسانی سامانه:
                <br/>
                <br/>
                با توجه به اینکه نسخه جدید از سامانه موجود می باشد، برای بروزرسانی سامانه به آخرین نسخه، درخواست بروز رسانی را ثبت نمایید.

                <form id="form1" action="{{route("utility.update.dashboard.submit")}}" method="post" novalidate="novalidate">
                    @csrf

                    <div class="row">
                        @include("component.input._lable",["id"=>"code","lable"=>" زمان آخرین بروز رسانی  ","value"=>$updated_at])
                        @include("component.input._lable",["id"=>"code","lable"=>" ورژن فعلی سامانه ","value"=>"1.7.6"])
                        @include("component.input._lable",["id"=>"code","lable"=>" آخرین ورژن سامانه  ","value"=>"1.7.19"])


                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary">ثبت درخواست</button>

                    </div>
                </form>

            </div>
        </div>
        {{--        @include("component.pup_up.type1")--}}

    </div>

@endsection
@section("styles")
    <script>



    </script>
    {{--    @include("component.input.datepicker._script")--}}
@endsection