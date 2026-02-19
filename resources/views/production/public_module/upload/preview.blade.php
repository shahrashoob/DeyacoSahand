@extends('layouts.admin._master')
@section("page_header_title","داشبورد ".$machine_allocation->getTextOfThing("dashboard_caption")."-  ".
$machine_allocation->getTextOfThing("fullCaption")
)

@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">
                    <h5>بررسی و تایید فایل اکسل </h5>

                </div>
                <div class="card-block">
                    <div class="col-sm-12">

                    </div>
                    <form id="form1"
                          action="{{route("production.public_module.upload.confirm",$machine_allocation)}}"
                          method="post"
                          enctype="multipart/form-data">
                        @csrf
                        @php $has_error=false; @endphp

                        <br/>

                        <table class="table table-styling center">
                            <tr>
                                <th>ردیف</th>
                                <th>کد کالا</th>
                                <th>نام کالا</th>
                                <th> درجه</th>
                                <th>لات</th>
                                <th>واحد اصلی</th>
                                <th>واحد فرعی</th>
                                <th>مقدار فرعی 2</th>
                                <th>تعداد بسته بندی فرعی</th>
                                <th>Pin1</th>

                            </tr>

                            @php $i=1; @endphp
                            @foreach($data["packing_form_list"] as $item)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$item["product_code"]}}</td>
                                    <td>{{isset($products[$item["product_id"]])?$products[$item["product_id"]]->caption:""}}</td>
                                    <td>{{$item["degree_code"]}}</td>
                                    <td>{{$item["lot_number_code"]}}</td>
                                    <td>{{$item["amount"]}}</td>
                                    <td>{{$item["sub_amount"]}}</td>
                                    <td>{{$item["sub_amount2"]}}</td>
                                    <td>{{$item["sub_packing_number"]}}</td>
                                    <td>{{$item["pin1"]}}</td>

                                </tr>
                                @if($item["error"]!="")
                                    <tr>
                                        <td colspan="10" class="alert alert-danger">
                                            {!! $item["error"] !!}
                                        </td>
                                    </tr>
                                    @php $has_error=true; @endphp
                                @endif
                            @endforeach
                        </table>

                        <div class="text-center m-t-20">
                            <input type="checkbox" checked name="print_packing_forms"/> چاپ بسته بندی های جدید
                            <br/>
                            <br/>
                            <a href="{{route("production.public_module.register_production.index",$machine_allocation)}}"
                               class="btn btn-outline-dark">بازگشت</a>
                            @if($has_error)
                                <a href="{{route("production.public_module.upload.index",$machine_allocation)}}"
                                   class="btn btn-primary">بارگذاری مجدد</a>
                            @else
                                <button type="submit" class="btn btn-success"> تایید و ادامه
                                </button>
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
