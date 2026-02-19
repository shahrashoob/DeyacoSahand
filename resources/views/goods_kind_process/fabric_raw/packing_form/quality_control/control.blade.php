<!DOCTYPE html>
<html lang="en">
<head>
@include("layouts._head")
    @yield("styles")
    <style>
        body {
            font-family: IRANSans !important;
        }
    .btn_end_item{
        width: 250px;
    }
    .btn_fault{
        width: 220px;
    }
    .table_report td, .table_report th{
        padding: 2px;
    }
</style>


</head>

<body>
<script src="{{asset('assets/plugins/jquery-validation/js/jquery.validate.min.js?random=9')}}"></script>
<script src="{{asset('assets/plugins/jquery-validation-1.11.1/localization/messages_'.__("local").'.js?random=10')}}"></script>

@php $product_unit=$packing_form->items()->first()->product->unit; @endphp

<div class="row" id="qc_content" style="padding: 30px;margin: 0px">
<div class="col-md-12">
    @include("layouts._messages")
</div>
    @include("goods_kind_process.fabric_raw.packing_form.quality_control._section_new")

</div>
@include("layouts._footer")
@include("component.script_function.get_new_option")

@include("component.script_function.view_image")


</body>
</html>