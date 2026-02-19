@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")

    <div class="row">

        <div class="col-md-6">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card ">
                <div class="card-header">
                    <h5>بارکد پرداخت از  کیف پول تارا

                    </h5>
                </div>

                <div class="center" style="padding: 15px">
                    بارکد را مقابل اسکنر فروشگاه قرار دهید
                    <br/>
                    {!! $barcode !!}
                    <br/> <br/>
                    <div id="countdown" ></div>
                    <br/>
                    <a href="{{route("accounting.welfare_service.tara.client.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>
                </div>



            </div>
        </div>

    </div>
@endsection
@section("scripts")
    <script src="{{asset("assets/plugins/timer-countdown360/src/jquery.countdown360.js")}}" type="text/javascript"
            charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">

        var countdown = $("#countdown").countdown360({
            radius: 25,
            seconds: {{$sconds}},
            fontColor: '#FFFFFF',
            strokeStyle:"#007BFF",
            strokeWidth: undefined,
            fillStyle:"#04A9F5",

            label: ["", ""],
            autostart: false,
            onComplete: function () {
                console.log('done')
                window.location = "{{route("accounting.welfare_service.tara.client.dashboard.purchase_key")}}";
            }
        });
        countdown.start();
        console.log('countdown360 ', countdown);
    </script>
@endsection
