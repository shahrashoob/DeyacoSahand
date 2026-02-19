@extends('layouts.admin._master')

@section('page_header_title',"آپلود تقویم کاری   ".$shift->caption)

@section("content")
    <div class="row">

        {{--        @include("component.alert._primary",["content"=>__("page.header.event.add_new")])--}}

        <div class="col-sm-12">

            <div class="card">


                <div class="card-header">
                    <h5>آپلود فایل اکسل {{$model["caption"]}} </h5>
                    <div class="card-header-right">
                        <a href="{{asset("import_sample/".$model["name"].".xlsx"."?x=".rand(1000,5000))}}" class="btn btn-dark"><i
                                class="fa fa-download"></i>
                            دانلود فایل اکسل نمونه
                        </a>
                    </div>
                </div>
                <div class="card-block">
                    <div class="col-sm-12">

                    </div>
                    <form id="form1"
                          action="{{route($model["route"],$model["id"]??"")}}" method="post"
                          enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" value="{{$model["name"]}}" name="model"/>

                        @include("component.input._select",["id"=>"year","lable"=>"انتخاب سال","option"=>$option,"class_col"=>"col-md-3"])
                        <div class="col-md-3">
                            <br/>
                            @include("component.input._checkbox_simple",["id"=>"check_avg","checked"=>1,"label"=>"آیا میانگین ساعت کار قانونی در روز (دقیقه) بررسی شود؟","class_col"=>"col-md-3"])

                        </div>
                        <br/>
                       <div class="col-md-3">
                           <input type="file"  name="file_uploaded" id="input_file_now"
                                  class="file-upload form-label" required/>
                       </div>



                        <div class="text-center m-t-20">
                            <a href="{{route("hr.shift.index")}}" class="btn btn-outline-dark">بازگشت</a>
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
