@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن گزینش جدید </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route('hr.definition.selection.selection.store')}}" method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان گزینش","value"=>""])
                            @include("component.input._aotocomplet2",[

                                "id"=>"selection_type_id",
                                "label"=>" نوع گزینش  ",
                                "option"=>$selection_type_option["items"],
                                ])
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
                "selection_type_id_auto": "required",
            }
        });
    </script>
@endsection
