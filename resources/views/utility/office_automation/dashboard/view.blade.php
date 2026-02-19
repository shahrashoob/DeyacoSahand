@extends('layouts.admin._master')
@section('page_header_title'," اتوماسیون اداری"." / کار ".$office_automation_work->getCode())
@section("content")

    <div class="page-wrapper">

        <div class="row">

            {{--                        Work--}}
            @if($office_automation_work->user_id == $current_user->id)
                @include("utility.office_automation.dashboard._work_view",["work"=>$office_automation_work])

            @endif


            {{--                @include("utility.office_automation.dashboard._to_do_item",["to_do"=>$office_automation_action->office_automation_to_do_list,"current_user"=>$current_user,"parent_action"=>$office_automation_action])--}}
            @foreach($to_do_list_action as $to_do)
                @include("utility.office_automation.dashboard._to_do_item",["to_do"=>$to_do,"current_user"=>$current_user,"allow_new_to_do"=>true])
            @endforeach

            @foreach($siblingToDoList as $to_do)
                @include("utility.office_automation.dashboard._to_do_item",["to_do"=>$to_do,"current_user"=>$current_user,"allow_new_to_do"=>false])
            @endforeach


            {{--            --}}{{--            اگر یک کار to_do وجود دارد، همه فرزندان آن را نمایش دهد.--}}
            {{--            @if($to_do && $to_do->office_automation_work_id !=$office_automation_work->id)--}}
            {{--                @foreach($to_do->office_automation_work->work_child()->where("user_id",$user_id)->get() as $work)--}}
            {{--                    @include("utility.office_automation.dashboard._to_do_item",["work"=>$work,"user_id"=>$user_id,"to_do_parent_id"=>$to_do->id??0])--}}
            {{--                @endforeach--}}
            {{--            @endif--}}

            {{--            اگر ریشه است، همه فرزندان را نمایش دهد--}}
            {{--            && $office_automation_work->office_automation_work_parent_id ==null--}}
            {{--            @if(!$to_do )--}}
            {{--                @foreach($office_automation_work->work_child()->where("user_id",$user_id)->get() as $work)--}}
            {{--                    @include("utility.office_automation.dashboard._to_do_item",["work"=>$work,"user_id"=>$user_id,"to_do_parent_id"=>$to_do->id??0])--}}
            {{--                @endforeach--}}

            {{--            @else--}}

            {{--                @foreach($to_do->work_child()->get() as $work)--}}
            {{--                    @include("utility.office_automation.dashboard._to_do_item",["work"=>$work,"user_id"=>$user_id,"to_do_parent_id"=>$to_do->id??0])--}}
            {{--                @endforeach--}}
            {{--            @endif--}}

            <div class="col-md-12">
                {{--                @if($to_do)--}}

                {{--                    <a href="{{route("utility.office_automation.dashboard.view",$to_do->office_automation_work_id)}}"--}}
                {{--                       class="btn btn-outline-dark">بازگشت</a>--}}
                {{--                @endif--}}
                <a href="{{route("utility.office_automation.dashboard.index")}}"
                   class="btn btn-outline-dark">بازگشت به میز کار</a>

            </div>
        </div>


    </div>

@endsection

@section("modals")


    @include("component.modal.md-modal._modal_input",[
        "id"=>"19",
        "theme"=>"",
        "title"=>" آیا از تایید انجام کار اطمینان دارید؟ ",
        "content"=>view("utility.office_automation.dashboard._content_form",["hidden_id"=>"action_confirm"]),
        "btn_class"=>"btn-danger",
        "btn_title"=>"",
        "url"=>route("utility.office_automation.work_done_confirm.submit")
    ])
    @include("component.modal.md-modal._modal_input",[
        "id"=>"17",
        "theme"=>"-danger",
        "title"=>" آیا از ارجاع مجدد کار اطمینان دارید؟ ",
        "content"=>view("utility.office_automation.dashboard._content_form",["hidden_id"=>"action_reject"]),
        "btn_class"=>"btn-danger",
        "btn_title"=>"",
        "url"=>route("utility.office_automation.reject.submit")
    ])

@endsection

@section("styles")
    @include("component.modal.md-modal._style")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    @include("component.input._file_upload_script",["id"=>"work_file","max_file_size"=>$max_file_size])

    @include("component.modal.md-modal._script")
    <script>
        function setActionConfirmId(id) {

            $("#action_confirm").val(id);
        }
        function setActionRejectId(id) {

            $("#action_reject").val(id);
        }

        $('.form_to_do').validate({
            rules: {
                description: "required",

            }
        });

        $('#form-17').validate({
            rules: {
                description: "required",

            }
        });

        $('#form-19').validate({
            rules: {
                description: "required",

            }
        });
    </script>
@endsection
