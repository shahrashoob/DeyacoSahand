@extends('layouts.admin._master')

@section('page_header_title',"داشبورد بسته بندی")
@php $product_unit=$packing_form->items()->first()->product->unit; @endphp
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> تغییر فرم بسته بندی {{$packing_form->getCode()}}   </h5>
                </div>
                <div class="card-block">


                    <div class="w-100"></div>

                    <div class="table-responsive">
                        @include("component.input._lable",["id"=>"","lable"=>"شماره بسته بندی  ","value"=>$packing_form->code,"class_col"=>"col-md-12"])

                        @include("component.input._lable",["id"=>"","lable"=>" حامل","value"=>isset($packing_form->carrier)?$packing_form->carrier->code:"فاقد حامل"])

                        @include("component.input._lable",["id"=>"","lable"=>$product_unit->measurement."  نهایی  ","value"=>$sum_final_amount_bands."  ".$product_unit->caption,"class_col"=>"col-md-12"])


                        <form id="form1"
                              action="{{route("fabric_raw.packing_form.change_in_warehouse.submit_section",[$packing_form,$packing_item->band_code])}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                            <div class="col-md-12" style="overflow: auto">
                                <table class="table table-styling"
                                       style="max-width:750px;margin: auto; text-align: center">
                                    <thead>
                                    <tr>

                                        <td>کد کالا</td>
                                        <td>همبافت (لات)</td>
                                        <td>باند مبدا</td>
                                        <td>{{$product_unit->measurement}} نهایی</td>
                                        <td>درجه</td>
                                        <td>نوع بسته بندی</td>
                                        <td>حامل</td>
                                        <td>باند مقصد</td>
                                    </tr>

                                    </thead>

                                    <tbody>
                                    @foreach($packing_item_temp as $item)
                                        @if($item->final_amount!=0 )
                                            <tr>

                                                <td> {{$item->product->code}} </td>
                                                <td>
                                                    {{$item->lot_number->code??"***"}}
                                                </td>
                                                <td> {{$item->band_code}} </td>
                                                <td>{{$item->final_amount}}</td>
                                                <td> {{$item->degree_caption}} </td>
                                                <td> {{$item->packing_type_caption}} </td>
                                                <td> {{$item->carrier_codes?$item->carrier_codes:($item->new_carrier_id==1?"حامل جدید":"حامل قبلی")}} </td>
                                                <td> {{$item->target_band_code??1}} </td>
                                                {{--                                                <td>--}}
                                                {{--                                                    --}}{{--                                                   <a href="{{route("fabric_raw.production_form.grading.delete_section",[$production_form_item,$item])}}"--}}
                                                {{--                                                    --}}{{--                                                      class="text-danger"--}}
                                                {{--                                                    --}}{{--                                                      onclick="return confirm('در صورت حذف این رکورد، همه رکوردهای بعدی نیز حذف می گردد \n آیا از حذف اطمینان دارید؟')">--}}
                                                {{--                                                    --}}{{--                                                       <i class="fa fa-trash"></i><i class="fa fa-arrow-down"></i>--}}
                                                {{--                                                    --}}{{--                                                   </a>--}}

                                                {{--                                                </td>--}}
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if( $latest_packing_item_temp->final_amount+ $latest_packing_sum_before < $sum_final_amount )
                                <div class="row alert alert-info"
                                     style="text-align: center; max-width: 800px;margin:auto; margin-top: 15px">

                                    <div class="w-100 center ">
                                        <h5>
                                            کد کالا : <b>{{  $latest_packing_item_temp->product->code }}</b>
                                            &nbsp;
                                            &nbsp;
                                            &nbsp;
                                            &nbsp;
                                            نام کالا : <b>{{  $latest_packing_item_temp->product->caption }}</b>
                                            <br/>
                                            <br/>

                                            همبافت (لات) : <b>{{  $latest_packing_item_temp->lot_number->code }}</b>
                                            &nbsp;
                                            &nbsp;
                                            &nbsp;
                                            &nbsp;
                                            باند مبدا : <b>{{  $packing_item->band_code }}</b>
                                        </h5>
                                    </div>

                                    @include("component.input._number",["id"=>"end_point","class_col"=>"col-md-4 col-xs-12","autofocus"=>1,"label"=>" از  ".$product_unit->measurement." ".$latest_packing_sum_before." تا  ".$product_unit->measurement,"value"=>""])


                                    @include("component.input._select",[
                                             "id"=>"packing_type_id",
                                             "option"=>$packing_type_option["items"],
                                             "val"=>"",
                                             "text"=>"",
                                            "label"=>"نوع بسته بندی",
                                             "class_col"=>"col-md-4 col-xs-12"
                                             ])

                                    @if($has_number_ability)
                                        @for($k=0;$k<$max_carrier;$k++)
                                            @include("component.input._number",["id"=>"carrier_id_$k","class_col"=>"carrier_div_$k carrier col-md-4 col-xs-12","autofocus"=>1,"label"=>"حامل","value"=>""])
                                            @include("component.input._number",["id"=>"band_carrier_id_$k","class_col"=>"band_carrier_div_$k carrier col-md-4 col-xs-12","label"=>"باند","value"=>"1"])
                                        @endfor
                                    @endif

                                    @include("component.input._select",["id"=>"new_carrier_id","class_col"=>"new_carrier_div carrier col-md-4 col-xs-12","autofocus"=>1,"label"=>"آیا بسته بندی جدید است؟","option"=>$option])

                                    <div class="col-xs-12 col-md-12">
                                        <ul class="text-danger ">
                                            @foreach ($errors->all() as $error)
                                                <li> {{$error}}</li>
                                            @endforeach
                                        </ul>
                                        <button type="submit" class="btn btn-success"><i
                                                class="fa fa-plus"></i>
                                            افزودن جدید
                                        </button>
                                    </div>
                                </div>

                            @endif
                        </form>


                        @if( round( $latest_packing_sum_before,6) ==round( $sum_final_amount,6) )
                            @if(isset($final_amount_list[$packing_item->band_code+1])  )
                                <br/>
                                <div class="col-md-12" style="text-align: center">

                                    <a href="{{route("fabric_raw.packing_form.change_in_warehouse.section",[$packing_form,$band_code+1])}}"
                                       class="btn btn-success"><i class="fa fa-stop-circle"></i>
                                        ادامه بسته بندی باند {{$packing_item->band_code+1}}
                                    </a>

                                </div>
                            @else

                                <br/>
                                <div class="col-md-12" style="text-align: center">
                                    <form id="form1"
                                          action="{{route("fabric_raw.packing_form.change_in_warehouse.end_of_section",$packing_form)}}"
                                          method="post"
                                          autocomplete="off"
                                          novalidate="novalidate">
                                        @csrf
                                        @include("component.input._checkbox_simple",["id"=>"print_new_label","checked"=>1,"label"=>" پرینت لیبل بسته بندی های جدید"])

                                       <br/>
                                        <button type="submit"
                                                onclick="return confirm('آیا از پایان بسته بندی اطمینان دارید؟')"
                                                class="btn btn-primary"><i class="fa fa-stop-circle"></i>
                                            پایان تغییر بسته بندی
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endif
                        <div class="col-md-12 center">
                            <br/>
                            <br/>
                            <a href="{{route("fabric_raw.packing_form.view",$packing_form)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                        </div>


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
                $(".carrier").css("display", "none");
                $('#form1').validate({
                    rules: {
                        "degree_id_auto": "required",
                        "end_point": {
                            "required": true,
                            "min": {{$latest_packing_sum_before}},
                            "max": {{$latest_packing_sum_before_band+ $final_amount_list[$band_code][$latest_packing_item_temp->lot_number_id]}},
                        },
                        "packing_type_id_auto": {"required": true},
                        @if($has_number_ability)
                            @for($k=0;$k<$max_carrier;$k++)
                        "carrier_id_{{$k}}": {"required": true}
                        @endfor
                        @endif
                    }
                });
                var carrier_count_for_each_packing =@php  echo $carrier_count_for_each_packing;@endphp;
                var carrier_band_for_each_packing =@php  echo $carrier_band_for_each_packing;@endphp;
                var has_number_ability =@php  echo $has_number_ability;@endphp;

                $("#packing_type_id").change(function () {
                    $(".carrier").css("display", "none");
                    for (k = 0; k < carrier_count_for_each_packing[$(this).val()]["layer_count"]; k++) {
                        $(".carrier_div_" + k).css("display", has_number_ability[$(this).val()]?"block":"none")
                        if (carrier_band_for_each_packing[$(this).val()]["band_number"] > 1) {
                            $(".band_carrier_div_" + k).css("display", "block")
                        }
                    }
                    //if (carrier_count_for_each_packing[$(this).val()]["layer_count"] == 0) {

                        $(".new_carrier_div").css("display", "block");

                   // }

                })

            </script>
@endsection
