@extends('layouts.admin._master')

@section('page_header_title',"بارگذاری مانده حساب های مشتری ")

@section("content")
<div class="row">

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h4>  1401 - مانده حساب های دریافتنی  </h4>
                <div class="card-header-right">
                    <a href="{{asset("import_sample/1401.xlsx")}}" class="btn btn-dark"><i
                        class="fa fa-download"></i>
                    دانلود فایل اکسل نمونه
                    </a>
                </div>
            </div>
            <div class="card-block">
                <div class="col-sm-12">

                    <div class="alert alert-primary" role="alert">
                        <p> لطفا اطلاعات  را مطابق با فایل اکسل تکمیل نمایید و سپس آپلود نمایید.
                            <br/>
                            <a
                                href="{{asset("import_sample/1401.xlsx")}}" target="_blank"
                            class="link"> دانلود فایل اکسل نمونه</a></p>

                    </div>

                </div>
                <form id="form1"
                      action="{{route("import.account_balance.upload",1401)}}" method="post" enctype="multipart/form-data">
                @csrf

                <input type="hidden" value="1401" name="model"/>

                <br/>
                <input type="file" name="file_uploaded" id="input_file_now"
                       class="file-upload" required/>


                <div class="text-center m-t-20">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> آپلود

                    </button>
                </div>

                </form>
            </div>
        </div>

    </div>


    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h4>  1301 - اسناد نزد صندوق   </h4>
                <div class="card-header-right">
                    <a href="{{asset("import_sample/1301.xlsx")}}" class="btn btn-dark"><i
                            class="fa fa-download"></i>
                        دانلود فایل اکسل نمونه
                    </a>
                </div>
            </div>
            <div class="card-block">
                <div class="col-sm-12">

                    <div class="alert alert-primary" role="alert">
                        <p> لطفا اطلاعات  را مطابق با فایل اکسل تکمیل نمایید و سپس آپلود نمایید.
                            <br/>
                            <a
                                href="{{asset("import_sample/1301.xlsx")}}" target="_blank"
                                class="link"> دانلود فایل اکسل نمونه</a></p>

                    </div>

                </div>
                <form id="form1"
                      action="{{route("import.account_balance.upload",1301)}}" method="post" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" value="1401" name="model"/>

                    <br/>
                    <input type="file" name="file_uploaded" id="input_file_now"
                           class="file-upload" required/>


                    <div class="text-center m-t-20">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> آپلود

                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h4>  1302 - اسناد در جریان وصول   </h4>
                <div class="card-header-right">
                    <a href="{{asset("import_sample/1302.xlsx")}}" class="btn btn-dark"><i
                            class="fa fa-download"></i>
                        دانلود فایل اکسل نمونه
                    </a>
                </div>
            </div>
            <div class="card-block">
                <div class="col-sm-12">

                    <div class="alert alert-primary" role="alert">
                        <p> لطفا اطلاعات  را مطابق با فایل اکسل تکمیل نمایید و سپس آپلود نمایید.
                            <br/>
                            <a
                                href="{{asset("import_sample/1302.xlsx")}}" target="_blank"
                                class="link"> دانلود فایل اکسل نمونه</a></p>

                    </div>

                </div>
                <form id="form1"
                      action="{{route("import.account_balance.upload",1302)}}" method="post" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" value="1401" name="model"/>

                    <br/>
                    <input type="file" name="file_uploaded" id="input_file_now"
                           class="file-upload" required/>


                    <div class="text-center m-t-20">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> آپلود

                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h4>  1304 - اسناد در جریان وصول نزد صندوق   </h4>
                <div class="card-header-right">
                    <a href="{{asset("import_sample/1304.xlsx")}}" class="btn btn-dark"><i
                            class="fa fa-download"></i>
                        دانلود فایل اکسل نمونه
                    </a>
                </div>
            </div>
            <div class="card-block">
                <div class="col-sm-12">

                    <div class="alert alert-primary" role="alert">
                        <p> لطفا اطلاعات  را مطابق با فایل اکسل تکمیل نمایید و سپس آپلود نمایید.
                            <br/>
                            <a
                                href="{{asset("import_sample/1304.xlsx")}}" target="_blank"
                                class="link"> دانلود فایل اکسل نمونه</a></p>

                    </div>

                </div>
                <form id="form1"
                      action="{{route("import.account_balance.upload",1304)}}" method="post" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" value="1401" name="model"/>

                    <br/>
                    <input type="file" name="file_uploaded" id="input_file_now"
                           class="file-upload" required/>


                    <div class="text-center m-t-20">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> آپلود

                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h4>  1303 - اسناد برگشتنی   </h4>
                <div class="card-header-right">
                    <a href="{{asset("import_sample/1303.xlsx")}}" class="btn btn-dark"><i
                            class="fa fa-download"></i>
                        دانلود فایل اکسل نمونه
                    </a>
                </div>
            </div>
            <div class="card-block">
                <div class="col-sm-12">

                    <div class="alert alert-primary" role="alert">
                        <p> لطفا اطلاعات  را مطابق با فایل اکسل تکمیل نمایید و سپس آپلود نمایید.
                            <br/>
                            <a
                                href="{{asset("import_sample/1303.xlsx")}}" target="_blank"
                                class="link"> دانلود فایل اکسل نمونه</a></p>

                    </div>

                </div>
                <form id="form1"
                      action="{{route("import.account_balance.upload",1303)}}" method="post" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" value="1401" name="model"/>

                    <br/>
                    <input type="file" name="file_uploaded" id="input_file_now"
                           class="file-upload" required/>


                    <div class="text-center m-t-20">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> آپلود

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
