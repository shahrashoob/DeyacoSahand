@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])

@section('page_header_title',"داشبورد  انبار  ")

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>مشخصات انبار</h5>
                </div>
                <div class="card-block">


                    <div class="row">


                        @include("component.input._lable",[
                            "label"=>"نوع تراکنش",
                            "value"=>$trans_kind->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"trans_kind_id",
                            "value"=>$trans_kind->id??""
                            ])

                        @include("component.input._lable",[
                            "label"=>"طرف حساب",
                            "value"=>$opp_kind->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"opp_kind_id",
                            "value"=>$opp_kind->id??""
                            ])

                        @include("component.input._lable",[
                            "label"=>"مرکز هزینه",
                            "value"=>$cost_center->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"cost_center_id",
                            "value"=>$cost_center->id??""
                            ])

                        @include("component.input._lable",[
                            "label"=>"انبار",
                            "value"=>$warehouse->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"warehouse_id",
                            "value"=>$warehouse->id??""
                            ])


                        @include("component.input._lable",[
                            "label"=>"شرح تراکنش انبار",
                            "value"=>$description
                            ])
                        @include("component.input._hidden",[
                            "id"=>"description",
                            "value"=>$description
                            ])


                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
               <div class="col-md-12">
                   <div class="card">
                       <div class="card-header">
                           <h5>لیست کالا ها</h5>
                       </div>
                       <div class="card-block">

                           <form id="form1" autocomplete="off" action="{{route("wh.out.exit_form_implementation2.submit_read_product_info_type4")}}"
                                 method="post"
                                 novalidate="novalidate">
                               @csrf

                           @for($k=0;$k< $max_row_for_exit_type4;$k++)
                               <div class="row">
                                   @include("component.input._select",[
                                          "id"=>"product_id_$k",
                                          "label"=>"نام کالای ".($k+1),
                                          "option"=>$product_option["items"],
                                          "val"=>$product_option["value"],
                                          "text"=>$product_option["text"],
                                          "class_col"=>""
                                          ])
                                   @include("component.input._number",["label"=>"مقدار کالای ".($k+1),"id"=>"final_amount_$k","value"=>isset($product_amount[$k])?$product_amount[$k]:"","class_col"=>"col-md-2"])
                                   @include("component.input._number",["label"=>"لات(همبافت) کالای  ".($k+1),"id"=>"lot_number_$k","value"=>isset($lot_number[$k])?$lot_number[$k]:"","class_col"=>"col-md-2"])
                                   @include("component.input._text",["label"=>"درجه کالای  ".($k+1),"id"=>"degree_$k","value"=>isset($degree_id[$k])?$degree_id[$k]:"درجه اصلی کالا در رسته کالایی","class_col"=>"col-md-2","readonly"=>1])
                               </div>
                           @endfor

                               <div class="col-md-12 center" id="btn_submit">
                                   <a class="btn btn-outline-dark" href="{{route("wh.out.exit_form_implementation2.index")}}">بارگشت</a>
                                   <button type="submit" class="btn btn-primary submit_form"> ثبت و ادامه</button>
                               </div>
                           </form>


                       </div>
                   </div>
               </div>

            </div>
        </div>


    </div>

@endsection
@section("styles")

@endsection


@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                "caption": "required",
                "product_id_0": "required",
                "final_amount_0": "required",
                "lot_number_0": "required",
            }
        });


    </script>
@endsection

