@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")

    <form id="form1" style="display: inline" action="{{route("hr.post.smart_object.submit",$post)}}" method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">


            <div class="col-sm-12">
                <h5> تنظیمات اشیاء هوشمند برای پست {{$post->code ." - ".$post->caption}} ({{$post->shift->caption??""}})
                    <a href="{{route("hr.post.index")}}" class="btn btn-dark btn-sm">بازگشت</a>
                </h5>
                <hr>
                <ul class="nav nav-tabs" id="myTab" role="tablist">

                    <li class="nav-item">
                        <a class="nav-link text-uppercase show active" id="scale-tab" data-toggle="tab"
                           href="#scale" role="tab"
                           aria-controls="contact" aria-selected="false">باسکول هوشمند </a>
                    </li>


                </ul>
                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane fade show active" id="scale" role="tabpanel"
                         aria-labelledby="scale-tab">
                        <div class="col-md-9" data-select2-id="119">

                            @include("component.input.select2._select2",[
                           "id"=>"smart_object_ids",
                           "label"=>" اشیاء هوشمند مرتبط  ",
                           "option"=>$smart_object_option["items"],
                           "class_col"=>""
                           ])
                        </div>
                        <div class="col-md-12">


                            @include("component.input._checkbox",["id"=>"allow_enter_gross_weight_by_worker_to_posts","label"=>"در صورتی که هیچ باسکولی برای ثبت وزن ناخالص برای پست ها تعریف نشده بود، آیا مقدار را از اپراتور دریافت کند ","checked"=>$post->allow_enter_gross_weight_by_worker_to_posts])


                        </div>
                    </div>


                </div>
            </div>

            <div class="col-md-12">
                <br/>
                <a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

                <button type="submit" class="btn btn-primary"> ذخیره</button>
            </div>

        </div>
        @include("component.input._hidden",["id"=>"last_tab_open","value"=>isset($last_tab_open)?$last_tab_open:"home"])
    </form>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    @include("component.input.select2._script")
@endsection
@section("scripts")


@endsection
