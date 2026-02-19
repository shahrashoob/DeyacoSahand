@extends('layouts.admin._master')
@section("page_header_title","کارتابل  مدیریت ")
@section("content")



    @include("line_product_station.product._tabs",["tab"=>"edit"])

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        dialog#lightbox {
            width: min(92vw, 980px); border: none; padding: 0; border-radius: 18px; overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,.25);
        }
        dialog::backdrop { background: rgba(0,0,0,.45); }
        .lb-wrap { position: relative; background: #000; }
        .lb-img { display: block; width: 100%; height: auto; max-height: 85vh; object-fit: contain; background: #000; }
        .lb-bar {
            display: flex; justify-content: space-between; align-items: center; gap: 8px;
            padding: 10px 12px; background: #0b1220; color: #fff; font-size: 14px;
        }
        .lb-close { appearance: none; border: 1px solid #1f2937; background: #111827; color: #fff; padding: 6px 10px; border-radius: 8px; cursor: pointer; }
    </style>
@endsection

@section("scripts")
    @include("component.script_function.get_new_option")
    @include("component.script_function.view_image")
    <script>
        $('#form1').validate({
            rules: {
                "product_service_type_id":"required",
                @for($k=1;$k<=2;$k++)
                "product_service_type_id{{$k}}": "required",
                "caption{{$k}}": "required",
                "code{{$k}}": "required",
                "status_id{{$k}}": "required",
                "unit_id{{$k}}": "required",
                "goods_kind_id{{$k}}": "required",
                "goods_type_id{{$k}}": "required",
                "number_in_carton{{$k}}": {required: true, min: 1},
                "weight{{$k}}": "required",
                "ic{{$k}}": "required",
                "service_id_in_employer_system{{$k}}": "required",
                "supply_type_id{{$k}}": "required",
                @endfor
{{--                @if(!$product->image)--}}
{{--                "image_file": "required",--}}
{{--                @endif--}}
            }
        });
    </script>

    @include("line_product_station.product._init_info_script")
@endsection
