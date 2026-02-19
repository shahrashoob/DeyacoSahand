@extends('layouts.admin._master',["keypress_enable"=>1])
@section('page_header_title'," اتوماسیون اداری")
@section("content")

    <form id="form1" action="{{route("utility.office_automation.dashboard.store")}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate"
          enctype="multipart/form-data">
        @csrf
        <div class="row">

            <div class="col-md-12">

                {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
                <div class="card">
                    <div class="card-header">
                        <h5>ایجاد کار جدید
                        </h5>
                    </div>
                    <div class="card-block">

                        <div class="row">
                            @include("component.input._hidden",["id"=>"office_automation_to_do_list_parent_id","value"=>$to_do_list_parent_id??null])
                            @include("component.input._hidden",["id"=>"office_automation_work_parent_id","value"=>$work_parent_id??null])
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان ","value"=>$caption??"","autofocus"=>1,"class_col"=>"col-md-3"])
                            @include("component.input.datepicker._datepicker",["id"=>"end_datetime",'label'=>"تاریخ پایان ","value"=>$end_datetime??null,"class_col"=>"col-md-3"])

                            <div class="col-md-9">
                                <label>توضیحات</label>
                                <textarea class="form-control max-textarea" name="description"
                                          rows="8">{{$description??""}}</textarea>
                                <br/>
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-9" data-select2-id="119">

                                @include("component.input.select2._select2",[
                               "id"=>"post_user_operation",
                               "label"=>" ارجاع جهت اقدام   ",
                               "option"=>$post_option["items"],"class_col"=>""
                               ])
                            </div>
                            <div class="col-md-9" data-select2-id="119">

                                @include("component.input.select2._select2",[
                               "id"=>"post_user_view",
                               "label"=>" رونوشت   ",
                               "option"=>$post_option["items"],
                               "class_col"=>""
                               ])
                            </div>

                            <div class="w-100"></div>

                            <div class="w-100"></div>
                            <div class="col-md-3" data-select2-id="119">

                                @include("component.input._select",[
                               "id"=>"priority_id",
                               "label"=>" اولویت",
                               "option"=>$priority_option["items"],"class_col"=>""
                               ])
                            </div>
                            <div class="col-md-3" data-select2-id="119">

                                @include("component.input._select",[
                               "id"=>"office_automation_to_do_type_id",
                               "label"=>"نوع",
                               "option"=>$office_automation_to_do_type_option["items"],"class_col"=>""
                               ])
                            </div>
                            @include("component.input._file_upload",["id"=>"work_file","label"=>"فایل (ها)","value"=>"","class_col"=>"col-md-3","multiple"=>1])


                            <div class="col-md-12">
                                @if(!$office_automation_work)
                                    <a href="{{route("utility.office_automation.dashboard.index")}}"
                                       class="btn btn-outline-dark">بازگشت</a>
                                @else
                                    <a href="{{route("utility.office_automation.dashboard.view",$office_automation_work->id)}}"
                                       class="btn btn-outline-dark">بازگشت</a>
                                @endif

                                <button type="submit" class="btn btn-primary"> افزودن</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


@endsection
@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    @include("component.input.select2._script")
    <style>
        li {
            direction: rtl !important;
        }
    </style>

@endsection

@section("scripts")

    @include("component.input._file_upload_script",["id"=>"work_file","max_file_size"=>$max_file_size])
    <script>

        $('#form1').validate({
            rules: {
                priority_id: "required",
                office_automation_to_do_type_id: "required",
                caption: {required: true, minlength: 5, maxlength: 50},
                description: {required: true},
                start_datetime_value: "required",
                end_datetime_value: "required",

            }
        });


    </script>
@endsection
