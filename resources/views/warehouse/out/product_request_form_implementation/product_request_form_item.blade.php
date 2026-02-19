@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ثبت درخواست خروج متفرقه </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("wh.out.product_request_form_implementation.submit_product_request_form")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


{{--                        <div class="col-md-3">--}}
{{--                            @include("component.input._lable",[--}}
{{--                               "id"=>"warehouse_id",--}}
{{--                               "label"=>" انبار   ",--}}
{{--                               "value"=>$warehouse->caption,--}}
{{--                               "class_col"=>""--}}
{{--                           ])--}}
{{--                        </div>--}}


                        <table>

                            @foreach($products as $product)
                                <tr>
                                    <td colspan="2"><h5>{{$product->caption}}</h5></td>
                                </tr>
                                <tr>
                                    <td style="width: 200px">مقدار کالا</td>
                                    <td>
                                        @include("component.input._number_sample",[
                              "id"=>"product_amount_".$product->id,
                              "label"=>"",
                              "value"=>"",
                              "class_col"=>""
                          ])
                                    </td>

                                </tr>
                                <tr>
                                    <td>درجه های مجاز کالا</td>
                                    <td>بسته بندی های مجاز کالا</td>
                                </tr>
                            <tr>
                                    <td style="text-align: right; direction: rtl">
                                        @foreach($degree_list[$product->goods_kind_id] as $degree)
                                            <span>
                                                <input type="checkbox" name="degree[{{$product->id}}][{{$degree->id}}]"
                                                       @if($degree->degree_type_id ==1) checked="checked"  @endif  >
                                            {{$degree->caption}}
                                               <br/>
                                            </span>
                                        @endforeach
                                    </td>


                                    <td>
                                        @foreach($packing_type_list[$product->id] as $packing_type_id=>$packing_type_caption)
                                           <span>
                                                <input type="checkbox" name="packing_type[{{$product->id}}][{{$packing_type_id}}]"  checked="checked">
                                            {{$packing_type_caption}}
                                              <br/>
                                           </span>
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
