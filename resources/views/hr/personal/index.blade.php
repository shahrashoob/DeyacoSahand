@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> {{$worker->fullname()}}</h5>
                </div>
                <div class="card-block">

                    @include("hr.personal._info")
                    <div class="row">
                        <div class="md-col-12 " style="margin: auto">
                            @include("hr.personal._action")
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include("hr.personal.leave._leave_waiting_for_confirm_parent_list")
    @include("hr.personal.leave._leave_waiting_for_confirm_replace_post_list")
    @include("hr.personal.mission._mission_for_confirm_replace_post_list")
    @include("hr.personal.replacement._replacement_waiting_for_confirm_replace_post_list")
    @include("hr.personal.absence._absence_waiting_form_confirm_parent_list")
    @include("hr.personal.absence._absence_waiting_form_confirm_replace_post_list")

    <div class="row">

        <div class="col-sm-12">
            <div class="col-sm-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">

                    <li class="nav-item">
                        <a class="nav-link active show text-uppercase" id="tab4-tab"
                           data-toggle="tab" href="#tab4"
                           role="tab"
                           aria-controls="tab4" aria-selected="false">سابقه ورود/خروج</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link    text-uppercase" id="tab1-tab" data-toggle="tab"
                           href="#tab1"
                           role="tab" aria-controls="tab1" aria-selected="false">درخواست های مرخصی</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link   text-uppercase" id="tab2-tab"
                           data-toggle="tab" href="#tab2"
                           role="tab"
                           aria-controls="tab2" aria-selected="true">
                            درخواست های اضافه کاری
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  text-uppercase" id="tab3-tab"
                           data-toggle="tab" href="#tab3"
                           role="tab"
                           aria-controls="tab3" aria-selected="false"> درخواست های ماموریت</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  text-uppercase" id="tab5-tab"
                           data-toggle="tab" href="#tab5"
                           role="tab"
                           aria-controls="tab5" aria-selected="false"> درخواست های جابجایی </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  text-uppercase" id="tab6-tab"
                           data-toggle="tab" href="#tab6"
                           role="tab"
                           aria-controls="tab5" aria-selected="false"> درخواست های جانشینی غیبت </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  text-uppercase"
                           href="{{route("hr.personal.evaluation_form.index")}}"

                        > ارزیابی عملکرد </a>
                    </li>


                </ul>
                <div class="tab-content " id="myTab1">
                    <div class="tab-pane fade  active show " id="tab4" role="tabpanel"
                         aria-labelledby="tab4-tab">

                        @include("hr.personal._input_logs")

                    </div>
                    <div class="tab-pane fade " id="tab1" role="tabpanel"
                         aria-labelledby="tab1-tab">
                        @include("hr.personal.leave._leaving_list")
                    </div>
                    <div class="tab-pane fade " id="tab2" role="tabpanel"
                         aria-labelledby="tab2-tab">

                        @include("hr.personal.overtime._overtime_list")

                    </div>
                    <div class="tab-pane fade " id="tab3" role="tabpanel"
                         aria-labelledby="tab3-tab">

                        @include("hr.personal.mission._mission_list")

                    </div>

                    <div class="tab-pane fade " id="tab5" role="tabpanel"
                         aria-labelledby="tab5-tab">

                        @include("hr.personal.replacement._replacement_list")

                    </div>
                    <div class="tab-pane fade " id="tab6" role="tabpanel"
                         aria-labelledby="tab6-tab">

                        @include("hr.personal.absence._absence_list")

                    </div>

                </div>
            </div>

        </div>


        @endsection
        @section("styles")
            @include("component.modal.md-modal._style")
            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
        @endsection
        @section("scripts")
            @include("component.modal.md-modal._script")

            <script>
                function setLeaveId(id, type) {
                    $(".leaveId").val(id);
                    $(".leaveConformType").val(type);
                }

            </script>
@endsection

@section("modals")

    @include("component.modal.md-modal._modal_input",[
        "id"=>"19",
        "theme"=>"-danger",
        "title"=>" آیا از عدم تایید درخواست اطمینان دارید؟ ",
        "content"=>view("hr.personal._form_confirm",[])->render(),
        "url"=>route("hr.personal.confirm_parent_post")
    ])


    @include("component.modal.md-modal._modal_input",[
        "id"=>"16",
        "theme"=>"",
        "title"=>" آیا از  تایید درخواست اطمینان دارید؟ ",
        "content"=>view("hr.personal._form_confirm",[])->render(),
        "url"=>route("hr.personal.confirm_parent_post")
    ])

    @include("component.modal.md-modal._modal_input",[
        "id"=>"17",
        "theme"=>"",
        "title"=>" آیا از  اخذ توضیح برای درخواست اطمینان دارید؟ ",
        "content"=>view("hr.personal._form_confirm",[])->render(),
        "url"=>route("hr.personal.confirm_parent_post")
    ])

    @include("component.modal.md-modal._modal_input",[
        "id"=>"15",
        "theme"=>"",
        "title"=>" لطفا توضیحات را در کادر زیر وارد نمایید: ",
        "content"=>view("hr.personal._form_confirm",[])->render(),
        "url"=>route("hr.personal.set_user_comment")
    ])

@endsection
