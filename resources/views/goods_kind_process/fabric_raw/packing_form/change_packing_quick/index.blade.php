@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بسته بندی")

@section('content')
    @php $product_unit=$packing_form->items[0]->product->unit; @endphp
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تغییر فرم بسته بندی {{$packing_form->getCode()}}   </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.packing_form.change_packing_quick.submit",[$packing_form])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        <div class="w-100 alert alert-info">

                            لطفا نوع بسته بندی جدید را انتخاب نمایید و در مرحله بعد دیگر بسته بندی هایی که باید نوع بسته
                            بندی آنها تغییر کند را انتخاب کرده، در مرحله پایانی تغییر بسته بندی را نهایی کنید.
                            <br/>
                            بعد از تایید نهایی نوع بسته بندی همه بسته بندی ها تغییر کرده و همه لیبل های جدید چاپ می گردد.
                        </div>

                        <div class="table-responsive">
                            @include("component.input._lable",["id"=>"","lable"=>"شماره فرم بسته بندی   ","value"=>$packing_form->code,"class_col"=>"col-md-12"])

                            <div class="col-md-4">
                                @include("component.input._select",[
                                    "id"=>"packing_type_id",
                                    "label"=>" نوع بسته بندی جدید ",
                                    "option"=>$packing_type_option["items"],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>
<br/>
                            @include("component.input._number",["id"=>"product_request_form_code","lable"=>"شماره درخواست مشتری از انبار     (***/DCRP)  ","value"=>"","class_col"=>"col-md-4"])


                            <div class="w-100"><br/></div>
                            <div class="col-md-12">
                                <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-success">تایید و ادامه</button>
                            </div>


                        </div>

                    </form>
                </div>

            </div>

        </div>
    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "packing_type_id": "required",
                "product_request_form_code": "required",

            }
        });
    </script>
@endsection
