@extends('layouts.admin._master')

@section('page_header_title',"آپلود فایل اکسل   ".$model["caption"])

@section("content")
    <div class="row">

        {{--        @include("component.alert._primary",["content"=>__("page.header.event.add_new")])--}}

        <div class="col-sm-12">

            <div class="card">
                @if(isset($stepInfo))
                    @include("component.smartwizard.step",["stepInfo"=>$stepInfo,"active"=>$step])

                @endif

                <div class="card-header">
                    <h5>آپلود فایل اکسل {{$model["caption"]}} </h5>
                    <div class="card-header-right">
                        <a href="{{route("import.product.property.download_page")}}" class="btn btn-dark"><i
                                class="fa fa-download"></i>
                            دانلود فایل اکسل نمونه
                        </a>
                    </div>
                </div>
                <div class="card-block">
                    <div class="col-sm-12">
                        @switch($model["name"])
                            @case("product_info")
                            <div class="alert alert-primary" role="alert">
                                <p> لطفا اطلاعات محصولات را مطابق با فایل اکسل تکمیل نمایید و سپس آپلود نمایید.
                                    <br/>
                                    <a
                                        href="{{asset("import_sample/".$model["name"].".xlsx")}}" target="_blank"
                                        class="link"> دانلود فایل اکسل نمونه</a></p>

                            </div>
                            @break
                        @endswitch
                    </div>
                    <form id="form1"
                          action="{{route($model["route"],$model["id"]??"")}}" method="post"
                          enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" value="{{$model["name"]}}" name="model"/>

                        <br/>
                        <input type="file" name="file_uploaded" id="input_file_now"
                               class="file-upload btn btn-default" required/>


                        <div class="text-center m-t-20">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> آپلود و درج در
                                دیتابیس
                            </button>
                            @if($model["name"] == "nosa")
                                <a class="btn btn-warning" onclick="return confirm('آیا از فراخوانی بدون سفارش اطمینان دارید؟')" href="{{route("import.nosa.with_out_orderlist")}}">
                                    فراخوانی بدون آپلود لیست سفارش ها
                                </a>
                            @endif
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
