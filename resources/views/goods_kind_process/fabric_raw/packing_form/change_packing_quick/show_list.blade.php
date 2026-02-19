@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بسته بندی")

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تغییر فرم بسته بندی {{$packing_form->getCode()}}   </h5>
                </div>
                <div class="card-block">
                    @include("component.input._lable",["id"=>"","lable"=>"نام و کد کالای مشتری با توجه به درخواست ","value"=>isset($customer_code)?$customer_code:"---","class_col"=>"col-md-12"])
                    @include("component.input._lable",["id"=>"","lable"=>"شماره درخواست کالا از انبار","value"=>$product_request_form->code,"class_col"=>"col-md-12"])

                    <form id="form1"
                          action="{{route("fabric_raw.packing_form.change_packing_quick.confirm",[$packing_form])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-styling" style="text-align: center!important;">
                                <thead>
                                <tr>

                                    <th>ردیف</th>

                                    <th> شماره بسته بندی</th>
                                    <th> نوع بسته بندی قبلی</th>
                                    <th>نوع بسته بندی جدید</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($packing_forms as $item)
                                    <tr>
                                        <td>{{$row++}}</td>

                                        <td>
                                            {{$item->getCode()}}

                                        </td>
                                        <td>
                                            {{$item->packing_type->caption??"---"}}
                                        </td>
                                        <td>
                                           {{$new_packing_type->caption}}
                                        </td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <div class="col-md-12">
                            @include("component.input._checkbox_simple",["id"=>"allow_print","label"=>"چاپ لیبل های جدید"])
                           <br/>
                            @include("component.input._checkbox_simple",["id"=>"allow_add_to_request","label"=>"بسته بندی ها به درخواست ".$product_request_form->code." اضافه شود."])
                        <br/>
                            <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">تایید نهایی</button>
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

            }
        });
    </script>
@endsection
