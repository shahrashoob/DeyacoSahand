@extends('layouts.admin._master') @section('page_header_title',"داشبورد  تولید")
@section('content')

    <form id="form1"
          action="{{route($route_path."submit",$machine)}}"
          method="post" autocomplete="off" novalidate="novalidate">
        @csrf

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header"><h5> جمع آوری ضایعات</h5></div>
                    <div class="card-block">
                        <div class="row">
                            @foreach($machine_type_list as $machine_type)

                                <div class="col-md-12" style="margin-top: 10px">
                                    <input type="checkbox" name="all_machine_type[{{$machine_type->id}}]">

                                    <a class="" data-toggle="collapse" href="#collapse{{$machine_type->id}}"
                                       role="button" aria-expanded="true"
                                       aria-controls="collapseExample">{{$machine_type->caption}}</a>

                                    <div class="collapse " id="collapse{{$machine_type->id}}" style="">

                                        @include("component.input._select_simple",[
                                            "id"=>"machine_type[".$machine_type->id."][]",
                                            "class"=>"js-example-rtl col-sm-12","multiple"=>"multiple",
                                            "option"=>$machine_option_list[$machine_type->id]["items"]])

                                    </div>

                                </div>

                            @endforeach
                            <br/>
                            <div class="col-md-4">
                                @include("component.input._select",[
                                    "id"=>"product_id",
                                    "label"=>"ضایعات ",
                                    "option"=>$waste_option["items"],
                                    "val"=>$waste_option["value"],
                                    "text"=>$waste_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                                <duv class="w-100"></duv>
                                <div class="col-md-4">
                                    @include("component.input._select",[
                                    "id"=>"packing_type_id",
                                    "label"=>"نوع بسته بندی",
                                    "option"=>[],
                                    "val"=>[],
                                    "text"=>[],
                                    "class_col"=>""
                                    ])
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md-4">
                                    @include("component.input._select",[
                                    "id"=>"degree_id",
                                    "label"=>"درجه ضایعات",
                                    "option"=>[],
                                    "val"=>[],
                                    "text"=>[],
                                    "class_col"=>""
                                    ])
                                </div>
                            <div class="w-100"></div>
                            @include("component.input._number",[
                                "id"=>"gross_weight",
                                "label"=>"وزن ناخالص ",
                                 "class_col"=>"col-md-4"
                                ])
                            <div class="w-100"></div>
                            @include("component.input._number",[
                                "id"=>"sub_packing_form_number",
                                "label"=>"تعداد بسته بندی فرعی ",
                                 "class_col"=>"col-md-4"
                                ])
                            <div class="col-md-12">
                                <a href="{{route($dashboard_route."view",$machine)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"
                                        onclick="return confirm('آیا تایید فرم اطمیان دارید؟')">
                                    تایید و ثبت

                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
@endsection

@section("styles")
    <link href="{{asset("assets/plugins/select2/css/select2.min.css")}}" rel="stylesheet">

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/> @endsection

@section("scripts")
    <script src="{{asset("assets/plugins/select2/js/select2.full.min.js")}}"></script>
    @include("component.script_function.get_new_option")
    <script>
        var goods_kind_product =@php echo json_encode($list_goods_kind_product); @endphp;
        var has_sub_packing_type =@php echo json_encode($has_sub_packing_type); @endphp;
        $("#product_id").change(function () {

            get_new_option(
                $("#packing_type_id").val(),
                $("#product_id").val(),
                "نوع بسته بندی",
                "packing_type_id",
                "packing_type_product",
                []
            )
        });
        $("#packing_type_id").change(function () {

            if(has_sub_packing_type[$("#packing_type_id").val()] ==1){
                $("#sub_packing_form_number").parent().css("display","")
            }else{
                $("#sub_packing_form_number").parent().css("display","none")
            }
            get_new_option(
                $("#degree_id").val(),
                goods_kind_product[$("#product_id").val()],
                "درجه کالا",
                "degree_id",
                "degree",
                []
            )



        })
        $(".js-example-rtl").select2({
            dir: "rtl"
        });


        $('#form1').validate({
            rules: {
                "product_id": "required",
                "degree_id": "required",
                "sub_packing_form_number": "required",
                "packing_type_id": "required",
                "gross_weight": "required",
            }
        });

    </script>

@endsection

