@extends('layouts.admin._master')
@section("page_header_title","داشبورد پیمانکاران -  ".$contractor->fullCaption())

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دستور پیمان {{$contractor_allocation->production->serial()}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1" autocomplete="off"
                          action="{{route("contractor.panel.register_production.submit_add_new_packing",[$contractor_allocation,$layer,$packing_form->id??0])}}"
                          method="post"
                          novalidate="novalidate">
                        @csrf

                        @if(count($packing_type_product_option["items"])>2)
                            <div class="w-100"></div>
                            <div class="col-md-4">
                                @include("component.input._select",[
                                    "id"=>"packing_type_id",
                                    "label"=>"نوع بسته بندی ",
                                    "option"=>$packing_type_product_option["items"],
                                    "val"=>$packing_type_product_option["value"],
                                    "text"=>$packing_type_product_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                        @else

                            @include("component.input._lable",["id"=>"packing_type_id","value"=>$packing_type_product_option["items"][1]["text"],"label"=>"نوع بسته بندی"])
                            @include("component.input._hidden",["id"=>"packing_type_id","value"=>$packing_type_product_option["items"][1]["value"]??""])

                        @endif

                        @include("component.input._text",["id"=>"carrier_code","lable"=>"شماره حامل","value"=>$carrier_code??"","class_col"=>"col-md-4"])

                        @include("component.input._hidden",["id"=>"packing_form_id","value"=>$packing_form->id??""])

                        <div class="col-md-12">
                            @if($packing_form->parent_packing_form_sub_packing())
                                <a class="btn btn-outline-dark"
                                   href="{{route("contractor.panel.register_production.view_packing",[$contractor_allocation,$packing_form->parent_packing_form_sub_packing()->parent_packing_form_id])}}">

                                    بازگشت

                                </a>
                            @else
                                <a class="btn btn-outline-dark"
                                   href="{{route("contractor.panel.register_production.index",[$contractor_allocation])}}">

                                    بازگشت

                                </a>
                            @endif

                            <button type="submit" class="btn btn-primary"> افزودن بسته بندی</button>
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
        var has_number_ability =@php echo json_encode($has_number_ability);@endphp;

        $('#form1').validate({
            rules: {
                "packing_type_id": "required",
                "carrier_code": "required",
                "degree_id_auto": "required",
            }
        });
        $("#carrier_code").parent().css("display", "none");
        @if(count($packing_type_product_option["items"])==2 && $has_number_ability[$packing_type_product_option["items"][1]["value"].""]==1)
        $("#carrier_code").parent().css("display", "block");
        @endif
        $("#packing_type_id").change(function () {

            if (has_number_ability[$(this).val()]) {
                $("#carrier_code").parent().css("display", "block");
            } else {
                $("#carrier_code").parent().css("display", "none");
            }
        });
    </script>
@endsection
