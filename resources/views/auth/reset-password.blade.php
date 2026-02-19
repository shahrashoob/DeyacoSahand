@extends('layouts.admin._master')
@section("page_header_title","پروفایل کاربری ")
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>تغییر کلمه عبور </h5>
                </div>
                <div class="card-block">
                    <div class="row">

                        <div class="col-sm-12">


                            <br/>
                            <form id="form1" style="display: inline" action="{{route("submit_change_pass",$worker)}}"
                                  method="post"
                                  novalidate="novalidate">
                                @csrf


                                <div class="col-md-6">
                                    <div class="row">
                                        <h5> تغییر کلمه عبور</h5>
                                        <div class="col-md-6 offset-md-6">
                                            <div class="form-group">
                                                <label>کلمه عبور جدید </label>
                                                <input name="password" id="password" value="" type="password"
                                                       class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 offset-md-6">
                                            <div class="form-group">
                                                <label>تکرار کلمه عبور </label>
                                                <input name="confirm_password" id="confirm_password" value=""
                                                       type="password"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <a href="{{route("hr.worker.index")}}" class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"> ذخیره</button>
                            </form>


                        </div>
                    </div>

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

                password: {required: true},
                confirm_password: {equalTo: "#password"},
            }
        });

    </script>
@endsection
