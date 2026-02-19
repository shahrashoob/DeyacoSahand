<div class="row">
    <div class="col-sm-12">
        <form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}"
              method="post"
              autocomplete="off"
              novalidate="novalidate"
              enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <input type="checkbox" id="possibility_of_sale" name="possibility_of_sale"
                        {{$product->possibility_of_sale?"checked":""}}>
                    آیا محصول امکان فروش دارد؟
                    <br/>
                    <br/>
                </div>
                <div class="w-100"></div>
                <div class="col-md-6">
                    <table id="table_type_of_sale">
                        <tr>
                            <td>لطفا نوع فروش را انتخاب نمایید:</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach($type_of_sale_product_list as $item)
                            <tr>
                                <td style="min-width:300px;"></td>
                                <td style="min-width: 150px;">
                                    <input type="checkbox" id="type_of_sale_{{$item->id}}"
                                           name="type_of_sale_{{$item->id}}"
                                           value="{{$item->id}}" {{in_array($item->id,$type_of_sale_product_list_for_product)?"checked":""}}>
                                    {{$item->id}} - {{$item->caption}}
                                </td>

                                @if($item->id == 2)
                                    <td style="height: 100px">
                                        @include("component.input._select",[
                                            "id"=>"service_id_2",
                                            "label"=>"نام و کد خدمت متناظر در سامانه",
                                            "option"=>$service_option[2]["items"],
                                            "val"=>$service_option[2]["value"],
                                            "text"=>$service_option[2]["text"],
                                            "class_col"=>"",
                                            "required"=>1
                                            ])

                                    </td>
                                @endif

                            </tr>

                        @endforeach
                    </table>
                </div>


                <div class="w-100"></div>


            </div>
            @include($view_path."_btn_list")
        </form>
    </div>
</div>

<script>

    $("#possibility_of_sale").change(function () {
        table_type_of_sale();
    })
    table_type_of_sale();

    function table_type_of_sale() {
        if ($("#possibility_of_sale").is(":checked")) {

            $("#table_type_of_sale").css("display", "")
        } else {
            $("#table_type_of_sale").css("display", "none")
        }
    }



    $("#type_of_sale_2").change(function () {
        type_of_sale_2();
    })
    type_of_sale_2();
    function type_of_sale_2() {
        if ($("#type_of_sale_2").is(":checked")) {
            $("#service_id_2").parent().css("display", "")
        } else {
            $("#service_id_2").parent().css("display", "none")
        }
    }
</script>
