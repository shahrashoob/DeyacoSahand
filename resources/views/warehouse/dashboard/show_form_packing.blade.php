@extends('layouts.admin._master')

@section('page_header_title',"داشبورد ورود به انبار  ")
@php $permission_confirm_packing=$post_user->checkButtonPermission("wh.dashboard.input.confirm_packing");@endphp
@section('content')
    <div class="row">

        @if($permission_confirm_packing)
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> فرم ورود به انبار {{$form->code}}</h5>
                    </div>
                    <div class="card-block  ">
                        @include("warehouse.dashboard._input_form_info")
                        <div class="row center">
                            @if($form->status_id ==500000410 || $form->status_id==500000420 )
                                <div class="col-md-12">

                                    @if($checking_carrier_at_delivery_of_product)
                                        <div class="alert alert-info">
                                            لطفا شماره بسته بندی یا کد حامل هر بسته بندی را در کادر(های) زیر
                                            وارد
                                            نمایید،
                                            ترتیب ورود اهمیتی ندارد
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            لطفا شماره بسته بندی یا کد حامل هر بسته بندی زیر را کنترل نمایید و
                                            در صورت عدم مغایرت، فرم را تایید نمایید
                                        </div>
                                    @endif
                                    @endif
                                </div>

                                <div class="col-md-12 center">
                                    <form id="form1" style="display: inline"
                                          action="{{route("wh.dashboard.confirm_packing_list",[$form,$page])}}"
                                          method="post"
                                          autocomplete="off"
                                          novalidate="novalidate">
                                        @csrf


                                        <div class="row">
                                            @if($form->status_id ==500000410 || $form->status_id==500000420 )

                                                <div class="col-md-12" style="overflow: auto">
                                                    <table class="table table-styling center"
                                                           style="max-width: 480px;  margin: auto">

                                                        <tr>
                                                            <th>ردیف</th>
                                                            <th> شماره حامل</th>
                                                            <th>
                                                                @if($form->warehouse->allow_entry_with_pin)
                                                                    پین بسته بندی
                                                                @else
                                                                    کد بسته بندی
                                                                @endif
                                                            </th>
                                                        </tr>
                                                        @php $row=0;$default_focus_set=false;@endphp
                                                        @foreach($packing_form_list as $item)
                                                            @if(isset($packing_form_id_where_put_in_warehouse[$item->id]))
                                                                <tr>
                                                                    <td>بسته {{++$row}}</td>
                                                                    <td>
                                                                        <input class="carrier"
                                                                               data-next_id="{{$row+1}}"
                                                                               data-maxlength="4"
                                                                               type="text"
                                                                               style="width: 140px"

                                                                               value="{{$item->carrier->code??""}}"
                                                                               disabled>


                                                                    </td>
                                                                    <td>
                                                                        <input class="packing"
                                                                               data-next_id="{{$row+1}}"
                                                                               data-maxlength="4" type="text"
                                                                               style="width: 140px"

                                                                               value="{{$item->getCodeNumber()}}"
                                                                               disabled>
                                                                        /DCPK

                                                                    </td>
                                                                </tr>
                                                            @else
                                                                <tr>
                                                                    <td>بسته {{++$row}}</td>
                                                                    <td>

                                                                        @if(($item->warehouse_status_id== 4201 && $item->status_id==7007003  ) || !$checking_carrier_at_delivery_of_product)
                                                                            <input class="carrier" id="carrier_{{$row}}"
                                                                                   data-next_id="{{$row+1}}"
                                                                                   data-maxlength="4"
                                                                                   type="text"
                                                                                   style="width: 140px"

                                                                                   value="{{$item->carrier->code??""}}"
                                                                                   disabled>
                                                                            <input type="hidden"
                                                                                   name="data[carrier_code][{{$row}}]"
                                                                                   value="{{$item->carrier->code??""}}">
                                                                        @else

                                                                            <input class="carrier" id="carrier_{{$row}}"
                                                                                   tabindex="{{$row}}"
                                                                                   data-next_id="{{$row+1}}"
                                                                                   data-maxlength="4"
                                                                                   type="text"
                                                                                   style="width: 140px"
                                                                                   name="data[carrier_code][{{$row}}]"
                                                                                   value="">

                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if(($item->warehouse_status_id== 4201 && $item->status_id==7007003) || !$checking_carrier_at_delivery_of_product)
                                                                            <input class="packing" id="packing_{{$row}}"
                                                                                   data-next_id="{{$row+1}}"
                                                                                   data-maxlength="4" type="text"
                                                                                   style="width: 140px"

                                                                                   value="{{$item->getCodeNumber()}}"
                                                                                   disabled>
                                                                            /DCPK

                                                                            <input type="hidden"
                                                                                   name="data[packing_form_code][{{$row}}]"
                                                                                   value="{{$item->getCodeNumber()}}">
                                                                        @else

                                                                            <input class="packing" id="packing_{{$row}}"
                                                                                   data-next_id="{{$row+1}}"
                                                                                   tabindex="{{$row+1000}}"
                                                                                   data-maxlength="4" type="text"
                                                                                   style="width: 140px"
                                                                                   {{$default_focus_set?"":"autofocus"}}
                                                                                   name="data[packing_form_code][{{$row}}]">

                                                                            @if($form->warehouse->allow_entry_with_pin)

                                                                            @else
                                                                                /DCPK
                                                                            @endif

                                                                            @php $default_focus_set=true;@endphp
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                        @endforeach

                                                    </table>
                                                </div>

                                                <br/>
                                                <br/>
                                            @endif

                                            <div class="col-md-12">
                                                <br/>
                                                <br/>
                                                @if($form->status_id ==500000410 || $form->status_id==500000420 )
                                                    <button type="submit" class="btn btn-primary" style="width: 130px"
                                                            onclick='return confirm("آیا از تایید تحویل کالا  اطمینان دارید؟")'>
                                                        تایید انبار
                                                    </button>
                                                @endif
                                                @if($form->status_id ==500000410 || $form->status_id==500000420 )
                                                    <a class="btn btn-danger" style="width: 130px"
                                                       href="{{route("wh.dashboard.reject_packing_list",[$form])}}"
                                                       onclick='return confirm("آیا از عدم تایید فرم اطمینان دارید؟")'>عدم
                                                        تایید
                                                        انبار
                                                    </a>

                                                @endif

                                                <a href="{{route("wh.dashboard.index")}}?page={{$page}}"
                                                   class="btn btn-outline-dark"
                                                   style="width: 130px">بازگشت</a>

                                                @if($form->status_id ==500000200)
                                                    <a href="{{route("wh.dashboard.print_packing_form",[$form,$page])}}"
                                                       class="btn btn-outline-dark"
                                                       style="width: 130px">چاپ لیبل ها </a>

                                                    <button class="btn btn-success dropdown-toggle" type="button"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true" style="width: 140px"
                                                            aria-expanded="false">دریافت فرم
                                                    </button>
                                                    <div class="dropdown-menu" style="text-align: center">
                                                        <a class="dropdown-item"
                                                           href="{{route("wh.dashboard.download_entry_form",[$form,4])}}"
                                                           id="btn_confirm_print">دانلود با فرمت A4</a>
                                                        <br/>
                                                        <a class="dropdown-item"
                                                           href="{{route("wh.dashboard.download_entry_form",[$form,3])}}"
                                                           id="btn_confirm_print">دانلود با فرمت A5 </a>
                                                        <br/>
                                                        <a class="dropdown-item"
                                                           href="{{route("wh.dashboard.print_entry_form",[$form,4])}}"
                                                           id="btn_confirm_back">چاپ با فرمت A4 </a>
                                                        <br/>
                                                        <a class="dropdown-item"
                                                           href="{{route("wh.dashboard.print_entry_form",[$form,3])}}"
                                                           id="btn_confirm_back">چاپ با فرمت A4 </a>


                                                    </div>

                                                    @if(in_array($form->trans_kind,[100,1]))
                                                        {{--    خرید داخلی و خرید خارجی--}}
                                                        <br/>
                                                        <br/>
                                                        <a href="{{route("utility.special_license.panel.new_special_license.index",[9,$form->id,$form->allocation->supplier_id??-1])}}"
                                                           class=""
                                                        >
                                                            <i class="fa fa-unlock-alt"></i>
                                                            درخواست مجوز برگشت کالا به تامین کننده
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-12">

                                </div>
                        </div>
                    </div>
                </div>

            </div>
        @else
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> فرم ورود به انبار {{$form->code}}</h5>
                    </div>
                    <div class="card-block  ">
                        @include("warehouse.dashboard._input_form_info")
                        <div class="row">
                            <div class="col-md-12 center">
                                <a href="{{route("wh.dashboard.index")}}?page={{$page}}"
                                   class="btn btn-outline-dark"
                                   style="width: 130px">بازگشت</a>
                            </div>
                            @if($form->status_id ==500000200)
                                <a href="{{route("wh.dashboard.print_packing_form",[$form,$page])}}"
                                   class="btn btn-outline-dark"
                                   style="width: 130px">چاپ لیبل ها </a>

                                <button class="btn btn-success dropdown-toggle" type="button"
                                        data-toggle="dropdown"
                                        aria-haspopup="true" style="width: 140px"
                                        aria-expanded="false">دریافت فرم
                                </button>
                                <div class="dropdown-menu" style="text-align: center">
                                    <a class="dropdown-item"
                                       href="{{route("wh.dashboard.download_entry_form",[$form,4])}}"
                                       id="btn_confirm_print">دانلود با فرمت A4</a>
                                    <br/>
                                    <a class="dropdown-item"
                                       href="{{route("wh.dashboard.download_entry_form",[$form,3])}}"
                                       id="btn_confirm_print">دانلود با فرمت A5 </a>
                                    <br/>
                                    <a class="dropdown-item"
                                       href="{{route("wh.dashboard.print_entry_form",[$form,4])}}"
                                       id="btn_confirm_back">چاپ با فرمت A4 </a>
                                    <br/>
                                    <a class="dropdown-item"
                                       href="{{route("wh.dashboard.print_entry_form",[$form,3])}}"
                                       id="btn_confirm_back">چاپ با فرمت A4 </a>


                                </div>

                                @if(in_array($form->trans_kind,[100,1]))
                                    {{--    خرید داخلی و خرید خارجی--}}
                                    <br/>
                                    <br/>
                                    <a href="{{route("utility.special_license.panel.new_special_license.index",[9,$form->id,$form->allocation->supplier_id??-1])}}"
                                       class=""
                                    >
                                        <i class="fa fa-unlock-alt"></i>
                                        درخواست مجوز برگشت کالا به تامین کننده
                                    </a>
                                @endif
                            @endif
                        </div>


                    </div>
                </div>
            </div>

        @endif

        @php
            if(count($form->item)>0){
                        $sum_amount     =round($form->item()->sum( "amount"),2 );
                           $sum_sub_amount =round( $form->item()->sum( "sub_amount") ,2);
                           $unit=$form->item->first()->product->unit;
                           $sub_unit=$form->item->first()->product->sub_unit;
                           }
        @endphp

        @include("warehouse.dashboard._general_item_list")
        @include("warehouse.out.exit_form.qr._list_group_by_products",["caption"=>"فرم ورود"])
        @include("warehouse.dashboard._item_list",["show_packing_code"=>!(in_array($form->status_id ,[500000410,500000420,500000710,500000720])  )])
        @include("warehouse.dashboard._log")
    </div>

@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "product_id": "required",
            }
        });
    </script>
@endsection


