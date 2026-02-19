@extends('layouts.admin._master')
@section('page_header_title'," اتوماسیون اداری"." / کار ".$office_automation_action->office_automation_work->getCode())
@section("content")

    <div class="page-wrapper">

        <div class="row">

            @foreach($office_automation_action->to_do_list_chaild as $to_do)
                @include("utility.office_automation.dashboard._to_do_item",["to_do"=>$to_do,"current_user"=>$current_user,"parent_action"=>$office_automation_action])
            @endforeach
            @foreach($siblingToDoList as $to_do)
                @include("utility.office_automation.dashboard._to_do_item",["to_do"=>$to_do,"current_user"=>$current_user,"parent_action"=>$office_automation_action])
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
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")

    <script>

        $('.form_to_do').validate({
            rules: {
                description: "required",

            }
        });
    </script>
@endsection
