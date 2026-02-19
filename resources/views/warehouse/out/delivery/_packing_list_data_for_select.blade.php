<div class="card-block">
    <form id="form1"
          action="{{route("wh.out.delivery.submit",[$product_request_form,$page,$product_id??0,$dashboard_type??""])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf

        <div class="row">
            <div class="table-responsive">
                <table class="table table-styling center">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>
                            <input type="checkbox" id="select_all">
                        </th>
                        <th> کد بسته بندی</th>
                        <th>جایگاه</th>
                        <th>نوع بسته بندی</th>
                        <th>حامل</th>
                        <th>عنوان اولین آیتم بسته بندی</th>
                        <th>مقدار کل بسته بندی</th>
                        <th> تعداد بسته بندی فرعی</th>
                        <th>تعداد آیتم</th>
                        <th>تنوع آیتم</th>
                        <th>انتخاب کل (بخشی از) <br/> بسته بندی</th>
                        @if($product_request_form->warehouse->warehouse_type_id!=1)
                            <th> مقدار خروج</th>
                        @endif
                        <th>شماره بسته بندی<br/> حمل و نقل</th>
                        <th>سریال تولید</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=$packing_list->firstItem();@endphp
                    @foreach($packing_list as $item)
                        <tr>
                            <td>{{$row++}}</td>
                            <td>
                                @php $tcode=isset($packing_list_data[$item->id]["transport_item_id"])?$packing_list_data[$item->id]["transport_item_id"]:null;@endphp

                                <input class="myCheckBox P{{$item->getCodeNumber()}}  T{{$tcode}}"
                                       name="data[packing][{{$item->id}}]"
                                       id="packing_{{$item->id}}"
                                       data-id="{{$item->id}}"
                                       data-transport_id="{{$tcode}}"
                                       type="checkbox" {{in_array($item->id,$selected_packing_ids)?"checked":""}}>

                            </td>
                            <td>{{$item->getCode()}}
                            @if(isset($packing_list_data[$item->id]["applicant_caption"]))<br/>
                                    <b class="text-info">{{$packing_list_data[$item->id]["applicant_caption"]}}</b>
                            @endif
                            </td>
                            <td>{{isset($warehouse_shelving_list[$item->warehouse_shelving_id])?$warehouse_shelving_list[$item->warehouse_shelving_id]:""}}</td>
                            <td>{{$item->packing_type->caption??""}}</td>
                            <td>{{$item->carrier->code??""}}</td>
                            <td>{{$packing_list_data[$item->id]["product_caption"]}}</td>
                            <td>


                                @if($allow_select_partial_of_packing_in_output)
                                    <input
                                            class="amount_select_packing_form"
                                            id="amount_select_packing_form_{{$item->id}}"
                                            name="data[amount_select][{{$item->id}}]"
                                            type="number"
                                            max="{{$packing_list_data[$item->id]["sum_final_amount"]}}"
                                            required
                                            style="width: 45px; text-align: center"
                                            value="{{isset($amount_select[$item->id])?$amount_select[$item->id]:$packing_list_data[$item->id]["sum_final_amount"]}}"
                                            {{in_array($item->id,$selected_packing_ids)?"":"disabled"}}
                                    >
                                @else
                                    {{$packing_list_data[$item->id]["sum_final_amount"]}}
                                @endif

                                {{$packing_list_data[$item->id]["unit_caption"]}}
                            </td>

                            <td>{{$packing_list_data[$item->id]["count_content"]}}</td>
                            <td>{{$packing_list_data[$item->id]["count_item"]}}</td>
                            <td>{{$packing_list_data[$item->id]["count_product_id"]}}</td>
                            @if($product_request_form->warehouse->warehouse_type_id!=1)
                           <td>
                               <input
                                   class="count_select_packing_form"
                                   id="count_select_packing_form_{{$item->id}}"
                                   name="data[count_select][{{$item->id}}]"
                                   type="number"
                                   max="{{$item->sub_packing_form_number}}"
                                   required
                                   max="{{$packing_list_data[$item->id]["count_content"]}}"
                                   style="width: 45px; text-align: center"
                                   value="{{isset($count_select[$item->id])?$count_select[$item->id]:""}}"
                                   {{in_array($item->id,$selected_packing_ids)?"":"disabled"}}
                               >
                           </td>
                                <td>
                                    <input
                                        class="count_select_packing_form"
                                        id="exit_amount_of_packing_form_{{$item->id}}"
                                        name="data[exit_amount_of_packing_form][{{$item->id}}]"
                                        type="number"
                                        required
                                        min="{{0}}"
                                        max="{{$packing_list_data[$item->id]["sum_final_amount"]}}"
                                        style="width: 120px; text-align: center"
                                        value="{{
	                                        isset($exit_amount_of_packing_form[$item->id])?
	                                        $exit_amount_of_packing_form[$item->id]:""}}"
                                        {{in_array($item->id,$selected_packing_ids)?"":"disabled"}}
                                    >
                                </td>
                            @else
                                <td>
                                   @if(
                                    $packing_list_data[$item->id]["count_content"] <=1 || isset($packing_list_data[$item->id]["transport_item_code"]))

                                    کل بسته

                                    @else
                                        <input
                                            class="count_select_packing_form"
                                            id="count_select_packing_form_{{$item->id}}"
                                            name="data[count_select][{{$item->id}}]"
                                            type="number"
                                            min="1"
                                            required
                                            max="{{$packing_list_data[$item->id]["count_content"]}}"
                                            style="width: 45px; text-align: center"
                                            value="{{isset($count_select[$item->id])?$count_select[$item->id]:$packing_list_data[$item->id]["count_content"]}}"
                                            {{in_array($item->id,$selected_packing_ids)?"":"disabled"}}
                                        >
                                    @endif
                                </td>
                            @endif
                            <td>
                                {{isset($packing_list_data[$item->id]["transport_item_code"])?$packing_list_data[$item->id]["transport_item_code"]:""}}

                            </td>
                            <td>
                                {{$packing_list_data[$item->id]["production_serial"]}}
                                @if($packing_list_data[$item->id]["production_parent_serial"])
                                    <br/>
                                    {{$packing_list_data[$item->id]["production_parent_serial"]}}
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td><br/>
                            <hr/>
                        </td>
                        <td colspan="3" style="text-align: right">
                            <input type="checkbox" id="select_all_page">
                            انتخاب همه {{$packing_list->total()}} بسته بندی
                            <hr/>
                        </td>
                        <td colspan="9">
                            <br/>
                            <hr/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="float-left">
                نمايش رکوردهای
                <b>{{$packing_list->firstItem()}}</b>
                تا
                <b>{{$packing_list->lastItem()}}</b>
                از
                <b>{{$packing_list->total()}}</b>
                رکورد موجود


            </div>
        </div>


        <div style="text-align: center">
            <div class="text-center">
                {{$packing_list->links('pagination::bootstrap-4')}}
            </div>


        </div>
        <div style=" height: 60px">
            <div style="text-align: center; height: 150px" id="btn_list">




                @if(isset($dashboard_type) && $dashboard_type=="customer")
                    <a href="{{route("wh.out.customer.view",[$product_request_form->order->customer_id])}}?page={{$page}}"
                       class="btn btn-outline-dark">بازگشت</a>


                @else
                    <a href="{{route("wh.out.dashboard.view",[$product_request_form->id,$page])}}"
                       class="btn btn-outline-dark">بازگشت</a>


                @endif

                <button type="submit" class="btn btn-primary">انتخاب و ادامه</button>

            </div>
        </div>
    </form>
</div>

<script>
    $("#select_all").change(function () {

        $('.myCheckBox').each(function () {
            $(this).prop('checked', $("#select_all").is(':checked'));
        });
        $('.count_select_packing_form').each(function () {

            $(this).prop("disabled", $("#select_all").is(':checked') ? 0 : 1);
        });

        $("#btn_list").css("display", "none");
        request = $.ajax({
            url: "{{url("api/other/warehouse_delivery_changeSelectedPacking")}}",
            type: "post",
            data: {
                "product_request_form_id": {{$product_request_form->id}},
                "all_checked": $("#select_all").is(':checked') ? 1 : -1,
                "dashboard_type":'{{$dashboard_type??""}}',
                "packing_show_in_page_list": [
                    @foreach($packing_list as $item)
                        "{{$item->id}}",
                    @endforeach
                ]
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#btn_list").css("display", "");
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
        $("#btn_list").css("display", "none");
        request = $.ajax({
            url: "{{url("api/other/warehouse_delivery_changeSelectedPacking")}}",
            type: "post",
            data: {
                "product_request_form_id": {{$product_request_form->id}},
                "all_page_checked": $("#select_all_page").is(':checked') ? 1 : -1,
                "dashboard_type":'{{$dashboard_type??""}}',
                "product_id": '{{$product_id}}'
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#btn_list").css("display", "");
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });

    })


    $(".myCheckBox").change(function () {

        if ($(this).is(':checked')) {
            $("#count_select_packing_form_" + $(this).data("id")).prop("disabled", 0);
            $("#amount_select_packing_form_" + $(this).data("id")).prop("disabled", 0);
            $("#exit_amount_of_packing_form_" + $(this).data("id")).prop("disabled", 0);
        } else {

            $("#count_select_packing_form_" + $(this).data("id")).prop("disabled", 1);
            $("#amount_select_packing_form_" + $(this).data("id")).prop("disabled", 1);
            $("#exit_amount_of_packing_form_" + $(this).data("id")).prop("disabled", 1);
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
                "dashboard_type":'{{$dashboard_type??""}}',
                "checked": $(this).is(':checked'),
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
