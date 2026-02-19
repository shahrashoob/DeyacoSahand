@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","کارتابل جاری انبار ")
@section("content")



    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دریافت برگ پالت </h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("wh.print.palet_sheet_post")}}" method="post" novalidate="novalidate">
                        @csrf

                        <div class="row">
                        @include("component.input._text",["id"=>"serial","lable"=>" شماره سریال کارت تولید  "])


                    </div>

                    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                    <button type="submit" class="btn btn-primary">دریافت برگ پالت</button>

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
