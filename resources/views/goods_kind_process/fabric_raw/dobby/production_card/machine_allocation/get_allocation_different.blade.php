@extends('layouts.admin._master')
@section('page_header_title'," داشبورد جاری تولید  ")
@section("content")
    <div class="row">
        <div class="col-sm-12 ">

            <div class="card">
                <div class="card-header">
                    <h5> آیا محصول در تخصیص جدید تغییر  خواهد کرد؟
                    </h5>
                    <h3 style="color: #EE5757;display: inline"> {{$value["has_product_change"]?"بله":"خیر"}}</h3>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center ">


                            <tr>
                                <td>
                                    کالای قبلی:
                                </td>
                                <td>
                                    {{isset($value["before_product"])?$value["before_product"]->fullCaption():""}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    کالای جدید:
                                </td>
                                <td>
                                    {{isset($value["current_product"])?$value["current_product"]->fullCaption():""}}

                                </td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-sm-12 ">

            <div class="card">
                <div class="card-header">
                    <h5> آیا طراحی ماشین در تخصیص جدید تغییر خواهد کرد؟

                    </h5>
                    <h3 style="color: #EE5757 ; display: inline"> {{$value["has_design_change"]?"بله":"خیر"}}</h3>
                </div>
                @if(isset($value["property_caption"]))
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-styling center ">

                                <tr>
                                    <td>
                                        مشخصه
                                    </td>
                                    <td>
                                        کالای قبلی
                                    </td>
                                    <td>
                                        کالای جدید
                                    </td>
                                </tr>
                                @foreach($value["property_caption"] as $key=>$caption)
                                    <tr
                                       class=" {{ (isset($value["before_property_value_product"][$key])?$value["before_property_value_product"][$key]:"-") !=
                                      (isset($value["current_property_value_product"][$key])?$value["current_property_value_product"][$key]:"-") ?"alert-warning":"-"}}"

                                    >
                                        <td>

                                            {{$caption}}
                                        </td>
                                        <td>
                                            {{isset($value["before_property_value_product"][$key])?$value["before_property_value_product"][$key]:"---"}}
                                        </td>
                                        <td>
                                            {{isset($value["current_property_value_product"][$key])?$value["current_property_value_product"][$key]:"---"}}
                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                        </div>

                    </div>
                @endif
            </div>
        </div>


        <div class="col-sm-12 ">

            <div class="card">
                <div class="card-header">
                    <h5> آیا تراکم در تخصیص جدید تغییر خواهد کرد؟
                    </h5>
                    <h3 style="color: #EE5757 ; display: inline"> {{$value["has_weft_density_change"]?"بله":"خیر"}}</h3>
                </div>
                @if(isset($value["weft_density_caption"]))
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-styling center ">

                                <tr>
                                    <td>
                                        مشخصه
                                    </td>
                                    <td>
                                        کالای قبلی
                                    </td>
                                    <td>
                                        کالای جدید
                                    </td>
                                </tr>
                                @foreach($value["weft_density_caption"] as $key=>$caption)
                                    <tr
                                        class=" {{ (isset($value["before_weft_density_value_product"][$key])?$value["before_weft_density_value_product"][$key]:"-") !=
                                      (isset($value["current_weft_density_value_product"][$key])?$value["current_weft_density_value_product"][$key]:"-") ?"alert-warning":"-"}}"

                                    >
                                        <td>
                                            {{$caption}}
                                        </td>
                                        <td>
                                            {{isset($value["before_weft_density_value_product"][$key])?$value["before_weft_density_value_product"][$key]:"---"}}
                                        </td>
                                        <td>
                                            {{isset($value["current_weft_density_value_product"][$key])?$value["current_weft_density_value_product"][$key]:"---"}}
                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                        </div>

                    </div>
                @endif
            </div>
        </div>


        <div class="col-sm-12 ">

            <div class="card">
                <div class="card-header">
                    <h5> آیا چله در تخصیص جدید تغییر خواهد کرد؟
                    </h5>
                    <h3 style="color: #EE5757 ; display: inline"> {{$value["has_warps_change"]?"بله":"خیر"}}</h3>

                </div>

                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-styling center ">

                                <tr>

                                    <td>
                                        چله قبلی
                                    </td>
                                    <td>
                                        چله جدید
                                    </td>
                                </tr>
                               <tr>
                                   <td>
                                       @foreach($before_warps_bom as $item)
                                           {{$item->material->fullCaption()}}<br/>
                                           @endforeach
                                   </td>
                                   <td>
                                       @foreach($current_warps_bom as $item)
                                           {{$item->material->fullCaption()}}<br/>
                                       @endforeach
                                   </td>
                               </tr>

                            </table>
                        </div>

                    </div>

            </div>
        </div>


        <div class="col-sm-12 ">

            <div class="card">
                <div class="card-header">
                    <h5> آیا نخ پود در تخصیص جدید تغییر خواهد کرد؟
                    </h5>
                    <h3 style="color: #EE5757; display: inline"> {{$value["has_yarn_weft_change"]?"بله":"خیر"}}</h3>

                </div>

                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-styling center ">

                                <tr>

                                    <td>
                                        نخ پود قبلی
                                    </td>
                                    <td>
                                        نخ پود جدید
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @foreach($before_yarn_weft_bom as $item)
                                            {{$item->material->fullCaption()}}
                                            <br/>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($current_yarn_weft_bom as $item)
                                            {{$item->material->fullCaption()}}<br/>
                                        @endforeach
                                    </td>
                                </tr>

                            </table>
                        </div>

                    </div>

            </div>
        </div>


    </div>
        <form id="form1" action="{{route("fabric_raw.machine_allocation.confirm_submit",[$allocation->machine])}}" method="post"
              novalidate="novalidate">
            @csrf
            <div class="col-sm-12" style="text-align: center">
                <br/>
                <br/>
                <h6>
                    در صورتی که از تخصیص کارت به ماشین  {{$allocation->machine->code." ".$allocation->machine->caption}} اطمینان دارید، بر روی دکمه "تایید و ثبت تخصیص" کلید بفرمایید.
                </h6>
                <br/>
                <button type="submit" onclick="return confirm('آیا از تخصیص کارت تولید به ماشین به صورت زیر اطمینان دارید؟')" class="btn btn-success" id="btn_replace">تایید و ثبت تخصیص</button>
                <a href="{{route("fabric_raw.dashboard.view_card",$allocation->items[0]->production)}}" class="btn btn-outline-dark">بازگشت
                    به کارتابل تولید</a>
            </div>
        </form>
@endsection
