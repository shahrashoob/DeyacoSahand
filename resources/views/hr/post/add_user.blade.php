@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن شاغل جدید به {{$post->caption}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.post.submit_add_user",$post)}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"user_id",
                                    "label"=>" نام شاغل ",
                                    "option"=>$worker_option["items"],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"shift_work_id",
                                    "label"=>"گروه  ".$post->shift->caption,
                                    "option"=>$shift_work_option["items"],
                                    "val"=>$shift_work_option["value"],
                                    "text"=>$shift_work_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                        </div>

                        <a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت فرد جدید</button>

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
                "user_id_auto": "required"
            }
        });
    </script>
@endsection
