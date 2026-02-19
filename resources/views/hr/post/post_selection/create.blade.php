@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>افزودن  گزینش جدید به {{$post->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route('hr.post.post_selection.store',$post)}}" method="post"
                         autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._aotocomplet2",[

                                   "id"=>"selection_id",
                                   "label"=>"لیست گزینش ها ",
                                   "option"=>$selection_active_option["items"],
                                   ])
                            @include("component.input._text",["id"=>"minimum_score_to_confirm_selection",'label'=>"حداقل امتیاز برای تایید ","value"=>""])
                            @include("component.input._text",["id"=>"priority_number",'label'=>"اولویت","value"=>""])

                        </div>

                        <a href="{{route('hr.post.employment.index',$post)}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">افزودن</button>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "selection_id_auto": "required",
                "minimum_score_to_confirm_selection": "required",
                "priority_number": "required",
            }
        });
    </script>
@endsection
