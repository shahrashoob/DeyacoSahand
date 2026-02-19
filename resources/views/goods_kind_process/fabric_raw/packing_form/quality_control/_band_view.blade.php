<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title" style="font-size: 25px;">
                باند {{$band_code}}


            </h5>

            @if(!$qc_data["is_end_of_quality_control"][$band_code])
                <h5 style="display: inline; float: left;background-color:#0000">
                    {{$qc_data["last_amount_control"][1] -$qc_data["contour_of_band"][$band_code] }}
                </h5>
            @endif

        </div>
        <div class="card card-body">
            @if($qc_data["is_end_of_quality_control"][$band_code])
                @include("goods_kind_process.fabric_raw.packing_form.quality_control._end_of_quality_control")
            @else
                <div class="collapse show">
                    <div class="col-md-12 center">

                        در حال کنترل: {{$qc_data["current_packing_form_item"][$band_code]["product"]["caption"]}}
                        <br/>
                        @if(isset($qc_data["items"][$band_code][$qc_data["current_packing_form_item"][$band_code]["id"]]["parent_production_channel_type_id"]))
                            کانال تولید سطح بالا:
                            {!! $qc_data["items"][$band_code][$qc_data["current_packing_form_item"][$band_code]["id"]]["parent_production_channel_type_id"] !!}

                            <br/>
                        @endif
                        کد کالا: {{ $qc_data["current_packing_form_item"][$band_code]["product"]["code"]}}
                        <br/>
                        کد آیتم {{$qc_data["current_packing_form_item"][$band_code]["code"]}}
                        <br/>


                        @if(isset($product_list[$qc_data["current_packing_form_item"][$band_code]["id"]]))
                            @include("component.input._lable_product",["id"=>"product_".$band_code,"lable"=>"","image"=>1,"basic_info"=>1,"product_consumed"=>1,
                                     "product_property"=>$product_list[$qc_data["current_packing_form_item"][$band_code]["id"]],
                                     "value"=>"مشخصات کالا"])
                        @else
                            <div class="form-group">

                                <a  href="" >
                                    <b>مشخصات کالا</b>
                                </a>
                                &nbsp;&nbsp;&nbsp;
                                <a href=""  data-title="مشخصات کالا">
                                    <b><i class="fa  fa-image"></i> تصویر کالا</b>
                                </a>

                                <a  href="" >
                                    <b>کالای مصرفی</b>
                                </a>


                                <a href="">
                                    <b>اطلاعات تکمیلی</b>
                                </a>

                            </div>
                        @endif

                    </div>
                    @if(isset($qc_data["degree_id_current"][$band_code]))
                        {{--                دریافت درجه کالا--}}
                        @include("goods_kind_process.fabric_raw.packing_form.quality_control._degree")
                    @elseif(!isset($qc_data["item_faults_current_properties"][$band_code]))
                        {{--                مشخصات نقص ها و پایان بسته بندی--}}
                        <div class="card-body center">

                            @foreach($qc_data["faults"] as $fault)
                                {{--                    عیب نقطه ای--}}
                                @if($fault["product_fault_type_id"] ==2)
                                    <button type="button"

                                            data-id="{{$fault["id"]}}"
                                            data-product_fault_type_id="{{$fault["product_fault_type_id"]}}"
                                            data-band_code="{{$band_code}}"

                                            class="btn_fault btn_fault_point btn-lg btn btn-outline-dark">
                                        {{$fault["caption"]}}
                                        @if(isset($qc_data["item_faults"][$band_code][$fault["id"]]))
                                            <span class="ml-1 badge badge-danger">
                                    {{count($qc_data["item_faults"][$band_code][$fault["id"]])}}
                                    </span>
                                        @endif
                                    </button>

                                    {{--                        عیب پیوست--}}
                                @else

                                    {{--                            تشخیص شروع و پایان عیبت--}}
                                    @if(
                                        !isset($qc_data["item_faults"][$band_code][$fault["id"]])
                                        ||
                                        isset($qc_data["item_faults"][$band_code][$fault["id"]]
                                            [count($qc_data["item_faults"][$band_code][$fault["id"]])-1]["end_point"]
                                            )
                                        )
                                        <button
                                                type="button"
                                                style="font-size: 15px"
                                                class="btn_fault btn_fault_start_point btn btn-lg btn-outline-dark"

                                                data-id="{{$fault["id"]}}"
                                                data-product_fault_type_id="{{$fault["product_fault_type_id"]}}"
                                                data-band_code="{{$band_code}}"

                                        > {{$fault["caption"]}}

                                            @if(isset($qc_data["item_faults"][$band_code][$fault["id"]]))
                                                <span class="ml-1 badge badge-danger">
                                    {{count($qc_data["item_faults"][$band_code][$fault["id"]])}}
                                    </span>
                                            @endif

                                        </button>
                                    @else
                                        <button
                                                type="button"
                                                style="font-size: 15px"
                                                class="btn_fault btn_fault_end_point btn btn-lg btn-secondary"

                                                data-id="{{$fault["id"]}}"
                                                data-product_fault_type_id="{{$fault["product_fault_type_id"]}}"
                                                data-band_code="{{$band_code}}"

                                        > {{$fault["caption"]}}

                                            (پایان)

                                        </button>
                                    @endif
                                @endif

                            @endforeach

                            <div class="center">
                                <br/>
                                <br/>

                                <button style="width: 260px" type="button" class="btn btn-primary btn_end_item"
                                        data-band_code="{{$band_code}}">پایان
                                    آیتم ورودی
                                </button>
                                @if(!$qc_data["is_last_packing_form_item"][$band_code])
                                    <button style="width: 260px" type="button"
                                            class="btn btn-primary btn_end_item_new_packing"
                                            data-band_code="{{$band_code}}">پایان آیتم ورودی و بسته بندی جدید
                                    </button>
                                @endif
                                <button style="width: 260px" type="button" class="btn btn-info btn_end_new_packing"
                                        data-band_code="{{$band_code}}">پایان آیتم خروجی و بسته بندی جدید
                                </button>
                                <button style="width: 260px" type="button" class="btn btn-info btn_end"
                                        data-band_code="{{$band_code}}">
                                    پایان آیتم خروجی
                                </button>
                            </div>
                        </div>
                    @else
                        @include("goods_kind_process.fabric_raw.packing_form.quality_control._properties_form")
                    @endif

                </div>
            @endif
        </div>
    </div>

</div>