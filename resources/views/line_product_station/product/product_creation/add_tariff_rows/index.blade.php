@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><b> پیش بینی بهای تمام شده </b></h5>
                </div>
{{--                آیا همه قیمت گذاری ها محاسبه شده اند، اگر بله، اجازه رفتن به مرحله بعد را می دهد.--}}
                @php global $all_pricing_claculated; $all_pricing_claculated=true; @endphp
                <div class="card-block" style="overflow: auto">
                    @include("line_product_station.product.actual_cost._predict_info",["show_btn_list"=>false,"only_current_price"=>1])

               <div class="row">

                   <div class="col-md-4">
                       <form id="form1" action="{{route("line_product_station.product.product_creation.add_tariff_rows.set_parent_product",[$product_creation_process])}}"
                             method="post"
                             autocomplete="off"
                             novalidate="novalidate">
                           @csrf
                       @include("component.input._select",[
                           "id"=>"parent_product_id",
                           "label"=>" این کالا همراه با کالای زیر قیمت گذاری می شود ",
                           "option"=>$accompanying_product_select["items"],
                           "val"=>$accompanying_product_select["value"],
                           "text"=>$accompanying_product_select["text"],
                           "class_col"=>""
                           ])
                       <br/>
                       <button type="submit" class="btn btn-primary"> ثبت کالای همراه</button>
                       </form>
                   </div>


               </div>
                </div>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>افزودن کالا به لیست تعرفه</h5>
                </div>
                <div class="card-block">
                    <div class="row">
                        @include("line_product_station.product.pricing._info",["allow_delete_item"=>1])
                    </div>
                </div>
            </div>
        </div>

    </div>


@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <link rel="stylesheet" href="{{asset("assets/plugins/jstreeview/style.css")}}"/>
@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
            }
        });

    </script>
@endsection

