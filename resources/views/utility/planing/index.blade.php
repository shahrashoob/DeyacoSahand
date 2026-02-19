@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","کارتابل برنامه ریزی ")
@section("content")



    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5> برگ برنامه ریزی سفارش</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("utility.planing.view_order")}}" method="post" novalidate="novalidate">
                        @csrf

                        <div class="row">
                            @include("component.input._text",["id"=>"code","lable"=>" کد سفارش  "])
                            @include("component.input._text",["id"=>"series","lable"=>" سری   "])

                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary">مشاهده برگ سفارش</button>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5> برگ برنامه ریزی کارت تولید</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("utility.planing.view_production")}}" method="post" novalidate="novalidate">
                        @csrf

                        <div class="row">
                            @include("component.input._text",["id"=>"serial","lable"=>" سریال کارت تولید   "])


                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary">مشاهده برگ تولید</button>

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
                "serial":"required",
            }
        });
    </script>
@endsection
