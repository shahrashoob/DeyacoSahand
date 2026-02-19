@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تایید درخواست خروج متفرقه </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("wh.out.product_request_form_implementation.confirm_product_request_form")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        @include("component.input._lable",["lable"=>"مرکز هزینه","value"=>$cost_center->fullCaption()])
                        @include("component.input._lable",["lable"=>"نوع تراکنش","value"=>$trans_kind->caption])

                        <table>

                            @foreach($products as $product)
                                <tr>
                                    <td colspan="2"><h5>{{$product->caption}}</h5></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px">مقدار کالا</td>
                                    <td style="font-weight: bold">
                                        {{$product_amount_list[$product->id]}} {{$product->unit->caption}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>درجه های مجاز کالا</td>
                                    <td>
                                        @foreach($degree_list[$product->goods_kind_id] as $degree)
                                            @if(isset($degree_list_select[$product->id][$degree->id]))
                                            <input type="checkbox"
                                                  checked="checked"  disabled="disabled"  >
                                            {{$degree->caption}}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>بسته بندی های مجاز کالا</td>
                                    <td>
                                        @foreach($packing_type_list[$product->id] as $packing_type_id=>$packing_type_caption)

                                            @if(isset($packing_type_list_select[$product->id][$packing_type_id]))
                                                <input type="checkbox"
                                                       checked="checked"  disabled="disabled"  >
                                                {{$packing_type_caption}}
                                            @endif

                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><br/><br/></td>
                                </tr>
                            @endforeach
                        </table>

                        <div class="col-md-12">
                            <a href="{{route("wh.out.product_request_form_implementation.index")}}" class="btn btn-outline-dark">بازگشت</a>
                            <button type="submit" class="btn btn-primary"> ثبت و ادامه </button>
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

    <style>
        li {
            direction: rtl !important;
        }
    </style>
    @include("component.input.select2._script")
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "warehouse_id_auto": "required",
                "product_ids": "required",
            }
        });
    </script>
@endsection
