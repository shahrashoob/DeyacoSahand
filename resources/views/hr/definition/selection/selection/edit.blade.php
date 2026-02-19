@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش گزینش {{$selection->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.definition.selection.selection.update",$selection)}}" method="post" enctype="multipart/form-data"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان آموزش","value"=>$selection->caption])
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[

                                      "id"=>"selection_type_id",
                                      "label"=>" نوع گزینش  ",
                                      "option"=>$selection_option["items"],
                                       "val"=>$selection->selection_type->id??"",
                                        "text"=>$selection->selection_type->caption??"",
                                        "class_col"=>""
                                      ])

                            </div>

                        </div>

                        <a href="{{route("hr.definition.selection.selection.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
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
