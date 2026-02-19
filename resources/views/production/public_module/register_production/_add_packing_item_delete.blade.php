@if($machine_allocation->production->packing_types()->count() ==1)
    @include("component.input._lable",["id"=>"packing_type_id","value"=>$machine_allocation->production->packing_types()->first()->packing_type->caption??"","label"=>"نوع بسته بندی"])
    @include("component.input._hidden",["id"=>"packing_type_id","value"=>$machine_allocation->production->packing_types()->first()->packing_type->id??"",])
@endif
@include("component.input._hidden",["id"=>"packing_type_id","value"=>$machine_allocation->production->packing_type->id??""])

@include("component.input._text",["id"=>"carrier_code","label"=>"شماره  حامل","value"=>$carrier_code??"","class_col"=>"col-md-4"])

@include("component.input._hidden",["id"=>"packing_form_id","value"=>$packing_form->id??""])

@include("component.input._lable",["id"=>"product_id","value"=>$machine_allocation->product->fullCaption(),"label"=>"کالا"])

@include("component.input._hidden",["id"=>"action_type","value"=>"add_new_product_item"])


<div class="row">

    <table class="table table-styling center"
           style="width: 600px;margin: auto; border: 3px solid #efefef; margin-bottom: 20px">

        @if(isset($message_add_packing))
            <tr>
                <td colspan="5" >
                   <div class="alert alert-danger">
                       {{$message_add_packing}}
                   </div>
                </td>
            </tr>
        @endif
        {{--        دانلود فایل بسته بندی--}}
        @if( isset($action_type) && ($action_type == "register_continue" || $action_type == "register_print_continue" ))
            <tr>
                <td colspan="4">
                    یک بسته بندی جدید با شماره
                    <b style="font-size: 16px">{{$packing_form_new_added->getCode()}}</b>
                    ثبت گردید.
                    <br/>
                    <a href="{{route("fabric_raw.packing_form.print_qr.download",$packing_form_new_added)}}"><i
                            class="fa fa-download"></i> دانلود فرم {{$packing_form_new_added->getCode()}}</a>
                </td>
            </tr>
        @endif

        {{--        بازگشت به صفحه اصلی--}}

        <tr>
            <th style="">
                @if($machine_allocation->production->packing_types()->count() !=1)
                    بسته بندی
                @endif
            </th>
            <th style="width: 150px">لات</th>
            <th style="width: 150px">درجه</th>
            <th style="width: 150px">{{$machine_allocation->production->product->unit->measurement}}</th>
            <th>تعداد بسته بندی فرعی</th>
        </tr>
        @if(isset($packing_form))
            @foreach($packing_form->items as $item)
                <tr>
                    <td></td>
                    <td>{{$item->lot_number->code}}</td>
                    <td>{{$item->degree->code}}</td>
                    <td>{{$item->final_amount}}</td>
                    <td></td>
                </tr>
            @endforeach
        @endif
        <tr>
            <td>
                @if($machine_allocation->production->packing_types()->count() !=1)
                    <select id="packing_type_id" name="packing_type_id">
                        @foreach($packing_type_option["items"] as $item)
                            <option
                                value="{{($item["value"]==0?"":$item["value"])}}" {{isset($item["selected"])?"selected":""}}>{{$item['caption']??$item['text']??"***"}}</option>
                        @endforeach
                    </select>
                @endif
            </td>
            <td>
                @if(isset($packing_form) && $packing_form->items()->count()>0)
                    <input type="hidden" id="lot_number_code" name="lot_number_code" autofocus
                           value="{{$packing_form->items()->first()->lot_number->code??""}}">
                    {{$packing_form->items()->first()->lot_number->code??""}}
                @else
                    <input type="text" id="lot_number_code" name="lot_number_code" value="{{$lot_number_code??""}}">
                @endif

            </td>
            <td>

                @if(isset($packing_form) && $packing_form->items()->count()>0 && $packing_form->packing_type->many_degrees_can_fit_into_one == 0)
                    <input type="hidden" id="degree_id" name="lot_number_code"
                           value="{{$packing_form->items()->first()->degree->id}}">
                    {{$packing_form->items()->first()->degree->caption}}

                @else
                    <select id="degree_id" name="degree_id">
                        @foreach($degree_option["items"] as $item)
                            <option
                                value="{{($item["value"]==0?"":$item["value"])}}" {{isset($item["selected"])?"selected":""}}>{{$item['caption']??$item['text']??"***"}}</option>
                        @endforeach
                    </select>
                @endif
            </td>
            <td>
                <input type="number" id="final_amount" name="final_amount" autofocus
                       value="{{isset($add_new_lot)?($final_amount??""):""}}">
            </td>
            <td>
                <input type="number" id="sub_packing_form_number" name="sub_packing_form_number" autofocus
                       value="{{isset($add_new_lot)?($sub_packing_form_number??""):""}}">
            </td>
        </tr>
        @if(isset($add_new_lot))
            <tr>
                <td colspan="5">
                    @include("goods_kind_process.fabric.special_production.machine.register_production._add_new_lot_number_n")
                </td>
            </tr>
        @endif

    </table>
</div>

<div class="col-md-12" style="text-align: center">


    <div class="btn-group mb-2 mr-2">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true"
                style="width: 140px"
                aria-expanded="false">ثبت
        </button>
        <div class="dropdown-menu" style="text-align: center">
            <a class="dropdown-item" href="#!" id="register_continue">ثبت و ادامه</a>
            <a class="dropdown-item" href="#!" id="register_back">ثبت و بازگشت</a>
            <a class="dropdown-item" href="#!" id="register_print_continue">ثبت ، چاپ و
                ادامه</a>
            <a class="dropdown-item" href="#!" id="register_print_back">ثبت ، چاپ و
                بازگشت</a>
        </div>
        <a class="btn btn-outline-dark" style="width: 140px"
           href="{{route("fabric.special_production.machine.register_production.index",[$machine_allocation])}}">

            بازگشت

        </a>
    </div>


</div>


<script>
    @if(isset($action_type) && ($action_type == "register_print_back" || $action_type == "register_back" ))

    console.log("{{$action_type}}")
    window.location.href = ("{{route("fabric.special_production.machine.register_production.index",[$machine_allocation])}}");

    @endif

    $("#final_amount").focus();
    $("#carrier_code").parent().css("display", "none");

    $("#add_new_product_item").click(function () {

        add_new_product();
    });

    $("#register_back").click(function () {
        $("#action_type").val("register_back");
        add_new_product(1);
    });
    $("#register_continue").click(function () {
        $("#action_type").val("register_continue");
        add_new_product(1);
    });
    $("#register_print_continue").click(function () {
        $("#action_type").val("register_print_continue");
        add_new_product(1);
    });
    $("#register_print_back").click(function () {
        $("#action_type").val("register_print_back");
        add_new_product(1);
    });

    function add_new_product(check_sub_amount) {

        var carrier_code = $("#carrier_code").val();
        var lot_number_code = $("#lot_number_code").val();
        var degree_id = $("#degree_id").val();
        var final_amount = $("#final_amount").val();
        var packing_type_id = $("#packing_type_id").val();
        var add_new_lot = $("#add_new_lot").val();
        var action_type = $("#action_type").val();
        var sub_packing_form_number = $("#sub_packing_form_number").val();

        if (sub_packing_form_number && isNaN(parseFloat(sub_packing_form_number))) {
            alert("لطفا تعداد بسته بندی فرعی را وارد نمایید.")
            return 0;
        }



        if (isNaN(parseInt(packing_type_id))) {
            alert("لطفا نوع بسته بندی را انتخاب نمایید.")
            return 0;
        }
        if (lot_number_code == "") {
            alert("لطفا لات را وارد کنید.")
            return 0;
        }
        if (isNaN(parseInt(degree_id))) {
            alert("لطفا درجه را انتخاب نمایید.")
            return 0;
        }

        if (isNaN(parseFloat(final_amount))) {
            alert("مقدار متراژ به درستی وارد نشده است.");
            return 0;
        }

        request = $.ajax({
            url: "{{url("api/fabric/special_production/add_new_packing/add_new_product_to_packing_api")}}",
            type: "post",
            data: {
                "machine_allocation_id": {{$machine_allocation->id}},
                "user_id": {{$user_id}},
                "packing_form_id": {{$packing_form->id??0}},
                "packing_type_id": packing_type_id,
                "lot_number_code": lot_number_code,
                "degree_id": degree_id,
                "final_amount": final_amount,
                "carrier_code": carrier_code,
                "add_new_lot": add_new_lot,
                "action_type": action_type,
                "sub_packing_form_number": sub_packing_form_number
            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#panel_packing_item").html(response);
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            alert("اطلاعات ثبت نشد، لطفا دوباره تلاش کنید.")
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
    }
</script>
