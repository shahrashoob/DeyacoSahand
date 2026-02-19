@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    @include('hr.definition.selection.selection_indicator._list')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن شاخص برای
                        {{$selection->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route('hr.definition.selection.selection_indicator.store',$selection)}}" method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان شاخص","value"=>""])
                            @include("component.input._select",[

                                "id"=>"field_type_id",
                                "label"=>" نوع شاخص  ",
                                "option"=>$field_type_option["items"],
                                ])
                            @include("component.input._number",["id"=>"weight",'label'=>"وزن","value"=>""])
                            @include("component.input._number",["id"=>"min_score",'label'=>"حداقل امتیاز","value"=>""])
                        </div>

                        <a href="{{route('hr.definition.selection.selection.index')}}" class="btn btn-outline-dark">بازگشت</a>

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
                "caption": "required",
                'field_type_id': "required",
                'weight': "required",
                'min_score': "required",
            }
        });
    </script>
@endsection
