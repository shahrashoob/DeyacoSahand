@extends('layouts.admin._master')
@section("page_header_title","داشبورد ".$machine_allocation->getTextOfThing("dashboard_caption")."-  ".
$machine_allocation->getTextOfThing("fullCaption")
)



@section("content")
    <div class="row">



        <div class="col-sm-12">

            <div class="card">


                <div class="card-header">
                    <h5>آپلود فایل اکسل  </h5>
                    <div class="card-header-right">
                        <a href="{{asset("import_sample/production_module1_register.xlsx"."?x=".rand(1000,5000))}}" class="btn btn-dark"><i
                                    class="fa fa-download"></i>
                            دانلود فایل اکسل نمونه
                        </a>
                    </div>
                </div>
                <div class="card-block">
                    <div class="col-sm-12">

                    </div>
                    <form id="form1"
                          action="{{route("production.public_module.upload.submit",$machine_allocation)}}" method="post"
                          enctype="multipart/form-data">
                        @csrf



                        <br/>
                        <div class="col-md-3">
                            <input type="file"  name="file_uploaded" id="input_file_now"
                                   class="file-upload form-label" required/>
                        </div>



                        <div class="text-center m-t-20">
                            <a href="{{route("production.public_module.register_production.index",$machine_allocation)}}" class="btn btn-outline-dark">بازگشت</a>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> آپلود و ادامه
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
