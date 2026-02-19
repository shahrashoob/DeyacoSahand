@extends('layouts.admin._master')

@section('page_header_title',"آپلود فایل اکسل   ".$model["caption"])

@section("content")
    <div class="row">

        {{--        @include("component.alert._primary",["content"=>__("page.header.event.add_new")])--}}

        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">
                    <h5>دانلود فایل اکسل نمونه </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("import.product.property.download_excel")}}" method="post"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="w-100"></div>
                        <div class="col-md-6">
                            @include("component.input._aotocomplet2",[
                                "id"=>"goods_kind_id",
                                "label"=>"رسته کالا ",
                                "option"=>$goods_kind_option["items"],
                                "val"=>"",
                                "text"=>"",
                                "class_col"=>""
                                ])
                        </div>


                        <div class="text-center m-t-20">
                            <button type="submit" class="btn btn-dark"><i class="fa fa-download"></i> دانلود فایل نمونه
                            </button>
                            <a class="btn btn-primary" href="{{route("import.product.property.index")}}">بازگشت</a>
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
                goods_kind_id_auto: "required",
            }
        });
    </script>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>


    @include("component.smartwizard.script")
@endsection
