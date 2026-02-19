@extends('layouts.admin._master')

@section('page_header_title'," ")

@section('content')
    <div class="row">

        <div class="col-sm-12" style="text-align: center">
            {{--       <img src="{{asset("assets/images/bg_2.png")}}" style="height:350px; margin-bottom: 30px" />--}}

            <br/>
            <br/>
            <h4 style="font-weight: bold">
                {{$setting["software_name"]->string_value??""}}
                <br/>
            </h4>
            <img src="{{asset("assets/images/dashboard_logo.png?random=".rand(1,100))}}"
                 style="width: 300px; margin-top:50px; "/>
        </div>

{{--        @env('local')--}}
{{--            // The application is running in "local"...--}}
{{--        @endenv--}}

{{--        @env(['staging', 'production'])--}}
{{--            // The application is running in "staging" or "production"...--}}
{{--        @endenv--}}
{{--        @production--}}
{{--            sdfds dsasadffffffffffffffffffffdsfdf--}}
{{--        @endproduction--}}
        <div class="col-md-12"  style="display: none">
            <img id="PlayMe" src="{{asset("assets/images/ceo.jpg")}}" style="width:180px; float:right; margin-left:10px"/>

            <div class="text" style="padding: 10px; font-size:16px;  ">

                <br/>
                پیام مدیرعامل:
                <br/>
                <br/>
                ما بر این باوریم که توسعه ی گروه روکو جز با اتکا به منابع انسانی توانمند و متخصص، تحقق
                نخواهد یافت. در همین راستا تلاش در جهت رضایت کارکنان ، مشتری مداری، ارتقای کیفیت خدمات و تأمین منافع
                استفاده کنندگان از محصولات و خدمات ارائه شده، نه یک شعار، بلکه برای ما یک اعتقاد و باور است.

            </div>
        </div>

        @include("component.pup_up.type1")

    </div>

@endsection
@section("styles")
    <script>


    </script>
    @include("component.input.datepicker._script")
@endsection
