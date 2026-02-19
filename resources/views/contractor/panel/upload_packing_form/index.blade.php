@extends('layouts.admin._master')

@section('page_header_title',"آپلود فایل اکسل   ".$model["caption"])

@section("content")
    <div class="row">


        <div class="col-sm-12">

            <div class="card">
                @if(isset($stepInfo))
                    @include("component.smartwizard.step",["stepInfo"=>$stepInfo,"active"=>$step])

                @endif

                <div class="card-header">
                    <h5>آپلود فایل اکسل {{$model["caption"]}} </h5>
                    <div class="card-header-right">
                        <a href="{{asset("import_sample/".$model["name"].".xlsx")}}" class="btn btn-dark"><i
                                class="fa fa-download"></i>
                            دانلود فایل اکسل نمونه
                        </a>
                    </div>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route($model["route"],$model["id"]??"")}}" method="post"
                          enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" value="{{$model["name"]}}" name="model"/>

                        <div class="w-100"></div>
                        <div class="col-md-6">
                            @include("component.input._aotocomplet2",[
                                "id"=>"packing_type_id",
                                "label"=>"نوع بسته بندی",
                                "option"=>$packing_type_option["items"],
                                "val"=>"",
                                "text"=>"",
                                "class_col"=>""
                                ])
                        </div>
                        <input type="file" name="file_uploaded" id="input_file_now"
                               class="file-upload btn btn-default" required/>



                        <div class="text-center m-t-20">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> آپلود و درج در
                                دیتابیس
                            </button>

                        </div>

                    </form>
                </div>


            </div>

        </div>

    </div>

@endsection
@section("scripts")
    <script src="{{asset("assets/plugins/fileupload/js/dropzone-amd-module.min.js")}}"></script>
    <script>
        $('#form1').validate({
            rules: {
                factory_id_auto: "required",
                input_file_now: "required",
            }
        });
    </script>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>


    @include("component.smartwizard.script")
@endsection
