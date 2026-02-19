<div class="card-body ">
    <form id="form_faults_property_{{$band_code}}"

    >
        @csrf

        <div class="alert alert-info  col-md-12">
            لطفا اطلاعات تکمیلی نقص اعلام شده را وارد نمایید.
        </div>

        {{--        پاسخ به این سوال که آیا عیب را برطرف کرده اند یا خیر--}}
        @php
            $product_fault=$qc_data["faults"][$qc_data["item_faults_current_properties"][$band_code]["product_fault_id"]];
        @endphp

        @switch($product_fault["product_fault_fixed_type_id"])

            @case(2)
                {{--               <div class="alert alert-warning">--}}
                {{--                   با توجه به اینکه {{$product_fault["caption"]}} قابل رفع شدن است، حتما عیب را از کالا برطرف کنید.--}}
                {{--               </div>--}}
                {{--            @break--}}
                @include("component.input._radio_box01",[
                                                       "id"=>"product_fault_fixed_type_id".$product_fault["id"],
                                                   'label'=>"آیا عیب را رفع کرده اید؟",
                                                   "value"=>"0"])
                @break
            @case(1)
            @case(3)
                @include("component.input._radio_box01",[
                                                       "id"=>"product_fault_fixed_type_id".$product_fault["id"],
                                                   'label'=>"آیا عیب را رفع کرده اید؟",
                                                   "value"=>""])
                @break

        @endswitch

        {{--                دریافت مشخصات نقص ها--}}
        @php $fault_property_list=[];@endphp
        @foreach($qc_data["item_faults_current_properties"][$band_code]["properties"] as $fault_properties)
            @php $fault_property_list[$fault_properties["id"]]=$fault_properties["field_type_id"]; @endphp
            @switch($fault_properties["field_type_id"])
                {{--                        int--}}
                @case("1")

                    @include("component.input._number",[
                                "id"=>"fault_property_".$fault_properties["id"],
                                'label'=>$fault_properties["caption"],
                                "value"=>""])

                    @break
                    {{--                            char--}}
                @case("2")

                    @include("component.input._text",[
                                "id"=>"fault_property_".$fault_properties["id"],
                                'label'=>$fault_properties["caption"],
                                "value"=>""])
                    @break


                    {{--                            select--}}
                @case("3")
                    @include("component.input._select",[
                            "id"=>"fault_property_".$fault_properties["id"],
                            "label"=>$fault_properties["caption"],
                            "option"=>$qc_data["fault_property_option"][$fault_properties["id"]]["items"],
                            "class_col"=>"col-md-6"
                            ])
                    <br/>
                    @break


                @case("5")
                    {{--                            bool--}}
                    @include("component.input._radio_box01",[
                                        "id"=>"fault_property_".$fault_properties["id"],
                                    'label'=>$fault_properties["caption"],
                                    "value"=>""])
                    @break

            @endswitch
        @endforeach

        <div class="col-md-12">
            <button id="btn_fault_properties" data-band_code='{{$band_code}}' type="submit" class="btn btn-success ">
                تایید و ادامه
            </button>
        </div>
    </form>
    <script>
        $('#form_faults_property_{{$band_code}}').validate({
            rules: {
                "product_fault_fixed_type_id{{$product_fault["id"]}}": "required",
                @foreach($qc_data["item_faults_current_properties"][$band_code]["properties"] as $fault_properties)
                        @if($fault_properties["required"]== 1)
                "fault_property_{{$fault_properties["id"]}}": "required",
                @endif
                @endforeach
            }
        });
        $("#btn_fault_properties").click(function () {
            var property_values = [];
            @foreach($fault_property_list as $fault_property_id=>$field_type_id)

                    @switch($field_type_id)
                    {{--                        int--}}
                    @case("1")
                    {{--                            char--}}
                    @case("2")
                    {{--                            select--}}
                    @case("3")
                property_values[{{$fault_property_id}}] = $("#fault_property_{{$fault_property_id}}").val()
            @break
                    @case("5")
                    {{--                            bool--}}
                property_values[{{$fault_property_id}}] = $("#fault_property_{{$fault_property_id}}_1").val()

            @break

            @endswitch

            @endforeach

            var validate = $("#form_faults_property_{{$band_code}}").validate().form();
            if (validate) {
                add_fault(
                    "form_faults_property",
                        {{$qc_data["item_faults_current_properties"][$band_code]["product_fault_id"]}},
                        {{$band_code}},
                    $(this).data('band_code'),
                    property_values,
                    $('input[name="product_fault_fixed_type_id{{$product_fault["id"]}}"]:checked').val()
              )
          }
            return false;

        })
    </script>
</div>