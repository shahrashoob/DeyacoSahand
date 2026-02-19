@extends('layouts.admin._master',["keypress_enable"=>1])

@section('page_header_title',"داشبورد  تحویل انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12" id="card">
            <div class="card">
                <div class="card-header">
                    <h5>
                        لیست بسته بندی های مجاز انتخاب برای درخواست
                        {{$product_request_form->getCode()}}
                    </h5>
                </div>

                <div class="card-block">
                    <form id="form1" action="{{route("wh.out.delivery.submit",[$product_request_form,$page,$product_id??0])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            <input type="checkbox" id="select_all">
                                        </th>
                                        <th> کد بسته بندی</th>
                                        <th>نوع بسته بندی</th>
                                        <th>حامل</th>
                                        <th>عنوان اولین آیتم بسته بندی</th>
                                        <th>مقدار کل بسته بندی</th>
                                        <th> تعداد بسته بندی فرعی</th>
                                        <th>تعداد آیتم</th>
                                        <th>تنوع آیتم</th>
                                        <th>انتخاب کل (بخشی از) <br/> بسته بندی</th>
                                        <th>شماره بسته بندی<br/> حمل و نقل</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=$product_request_form_delivery_list->firstItem();@endphp
                                    @foreach($product_request_form_delivery_list as $item)
                                        <tr>
                                            <td>{{$row++}}</td>
                                            <td>
                                                @php $tcode=$item->transport_item_id??"";@endphp

                                                <input class="myCheckBox P{{$item->packing_form->getCodeNumber()}}  T{{$tcode}}"
                                                       name="data[packing][{{$item->packing_form_id}}]"
                                                       id="packing_{{$item->packing_form_id}}"
                                                       data-id="{{$item->packing_form_id}}"
                                                       data-transport_id="{{$tcode}}"
                                                       type="checkbox" {{$item->packing_form_selected?"checked":""}}>

                                            </td>
                                            <td>{{$item->packing_form->getCode()}}</td>
                                            <td>{{$item->packing_type->caption??""}}</td>
                                            <td>{{$item->carrier->code??""}}</td>
                                            <td>{{$item->product->caption}}</td>
                                            <td>
                                                {{$item->final_amount}}
                                                {{$item->unit->caption}}
                                            </td>

                                            <td>{{$item->sub_packing_form_number}}</td>
                                            <td>{{$item->count_item}}</td>
                                            <td>{{$item->count_product_id}}</td>
                                            <td>
                                                @if($item->sub_packing_form_number <=1 || $item->transport_item_id )
                                                    کل بسته

                                                @else
                                                    <input
                                                        class="count_select_packing_form"
                                                        id="count_select_packing_form_{{$item->id}}"
                                                        name="data[count_select][{{$item->id}}]"
                                                        type="number"
                                                        min="1"
                                                        max="{{$item->count_content}}"
                                                        style="width: 45px; text-align: center"
                                                        value="{{$item->sub_packing_form_number_selected}}"
                                                        {{$item->packing_form_selected?"":"disabled"}}
                                                    >
                                                @endif
                                            </td>
                                            <td>
                                                {{$item->transport_item->code??""}}

                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td><br/>
                                            <hr/>
                                        </td>
                                        <td colspan="3" style="text-align: right">
                                            <input type="checkbox" id="select_all_page">
                                            انتخاب همه {{$product_request_form_delivery_list->total()}} بسته بندی
                                            <hr/>
                                        </td>
                                        <td colspan="8">
                                            محل ثبت بارکد:
                                            <input type="number" value="" id="packing_number">
                                            /DCPK
                                            <hr/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="float-left">
                                نمايش رکوردهای
                                <b>{{$product_request_form_delivery_list->firstItem()}}</b>
                                تا
                                <b>{{$product_request_form_delivery_list->lastItem()}}</b>
                                از
                                <b>{{$product_request_form_delivery_list->total()}}</b>
                                رکورد موجود


                            </div>
                        </div>


                        <div style="text-align: center">
                            <div class="text-center">
                                {{$product_request_form_delivery_list->links('pagination::bootstrap-4')}}
                            </div>
                            <a href="{{route("wh.out.dashboard.view",[$product_request_form->id,$page])}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary">انتخاب و ادامه</button>

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
        $('form').on('keyup keypress', function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();

                if( $("#packing_number").val()== ""){
                    return false;
                }

                request = $.ajax({
                    url: "{{url("api/other/warehouse_delivery_changeSelectedPacking")}}",
                    type: "post",
                    data: {
                        "product_request_form_id": {{$product_request_form->id}},
                        "packing_form_code": $("#packing_number").val(),
                        "checked": 1,
                        "only_add": 1,
                    }
                });
                request.done(function (response, textStatus, jqXHR) {
                   if(response == "checked=1"){

                       $(".P" + $("#packing_number").val()).prop('checked', 1);
                   }else{
                        alert(response);
                   }
                    $("#packing_number").val("");
                });
                request.fail(function (jqXHR, textStatus, errorThrown) {
                    // Log the error to the console
                    console.error(
                        "The following error occurred: " +
                        textStatus, errorThrown
                    );
                    $("#packing_number").val("");
                });



                return false;
            }
        });

        $(".myCheckBox").change(function () {

            if ($(this).is(':checked')) {
                $("#count_select_packing_form_" + $(this).data("id")).prop("disabled", 0);
            } else {

                $("#count_select_packing_form_" + $(this).data("id")).prop("disabled", 1);
            }

            if ($(this).data("transport_id") != "") {
                $(".T" + $(this).data("transport_id")).prop('checked', $(this).is(':checked'));
            }

            request = $.ajax({
                url: "{{url("api/other/warehouse_delivery_changeSelectedPacking")}}",
                type: "post",
                data: {
                    "product_request_form_id": {{$product_request_form->id}},
                    "packing_form_id": $(this).data("id"),
                    "checked": $(this).is(':checked'),
                    "count_select_packing_form": $("#count_select_packing_form_" + $(this).data("id")).val()
                }
            });
            request.done(function (response, textStatus, jqXHR) {


            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });
        })
        $('#form1').validate({
            rules: {
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",
            }
        });

        $("#select_all").change(function () {

            $('.myCheckBox').each(function () {
                $(this).prop('checked', $("#select_all").is(':checked'));
            });
            $('.count_select_packing_form').each(function () {

                $(this).prop("disabled", $("#select_all").is(':checked') ? 0 : 1);
            });

            request = $.ajax({
                url: "{{url("api/other/warehouse_delivery_changeSelectedPacking")}}",
                type: "post",
                data: {
                    "product_request_form_id": {{$product_request_form->id}},
                    "all_checked": $("#select_all").is(':checked') ? 1 : -1,
                    "packing_show_in_page_list": [
                        @foreach($product_request_form_delivery_list as $item)
                            "{{$item->packing_form_id}}",
                        @endforeach
                    ]
                }
            });
            request.done(function (response, textStatus, jqXHR) {


            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });

        })

        $("#select_all_page").change(function () {

            $('.myCheckBox').each(function () {
                $(this).prop('checked', $("#select_all_page").is(':checked'));
            });
            $('.count_select_packing_form').each(function () {

                $(this).prop("disabled", $("#select_all_page").is(':checked') ? 0 : 1);
            });

            request = $.ajax({
                url: "{{url("api/other/warehouse_delivery_changeSelectedPacking")}}",
                type: "post",
                data: {
                    "product_request_form_id": {{$product_request_form->id}},
                    "all_page_checked": $("#select_all_page").is(':checked') ? 1 : -1,
                    "product_id": '{{$product_id}}'
                }
            });
            request.done(function (response, textStatus, jqXHR) {


            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });

        })


    </script>
@endsection


