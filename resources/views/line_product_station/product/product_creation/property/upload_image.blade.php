@extends('layouts.admin._master')

@section('page_header_title',"آپلود تصویر")

@section("content")
    <div class="row">


        <div class="col-sm-12">

            <div class="card">


                <div class="card-header">
                    <h5>{{"آپلود تصویر ".$goods_kind_property["caption"]." برای    ".$product->fullCaption()}}  </h5>
                    <div class="card-header-right">

                    </div>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.product.property.submit_upload_image",[$product,$goods_kind_property,$product_creation_process->id??null])}}"
                          method="post"
                          enctype="multipart/form-data">
                        @csrf

                        <br/>
                        @include("component.input._file_upload",["id"=>"file_uploaded","label"=>"تصویر مشخصه ","value"=>""])

                        <div class="text-center m-t-20">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i>
                                آپلود تصویر
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
