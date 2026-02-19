@extends('layouts.admin._master')

@section('page_header_title'," داشبورد جاری تولید - ".$production->product->goods_kind->caption)

@section('content')
    <form id="form1"
          action="{{route("fabric.finishing_machine.machine_allocation.confirm_submit",[$machine,$production,$allocation])}}"
          method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">

            <div class="w-25"></div>
            <div class="col-sm-12 col-md-12 col-md-offset-3">

                <div class="card">
                    <div class="card-header">
                        <h5>تایید تخصیص کالا به {{$machine->caption}}  </h5>

                    </div>

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>باند ورودی</th>
                                <th>کارت تولید</th>
                                <th>نام کالا</th>
                                <th>مقدار تخصیص
                                    ({{$production->product->unit->caption}})
                                </th>
                                <th>اولین عملیات</th>
                                <th>اولین عملیات فرعی</th>
                                <th>نوع بسته بندی مجاز</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp


                            @foreach($allocation->items  as $machine_allocation)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>باند {{$machine_allocation->band_code}}</td>
                                    <td>{{$machine_allocation->production->serial()}}</td>
                                    <td>{{$machine_allocation->product->caption}}</td>
                                    <td>
                                        {{$machine_allocation->allocation_amount}}
                                    </td>
                                    <td>
                                        {{$machine_allocation->line_product_station->station_operation->caption}}
                                    </td>
                                    <td>
                                        {{$machine_allocation->line_product_station->station_sub_operation->caption}}
                                    </td>
                                    <td>
                                        @foreach($machine_allocation->production->packing_types as $production_packing_type)
                                            {{$production_packing_type->packing_type->caption}}<br/>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        </div>

        @if(count($list_product_station_list)>0)
            @foreach($allocation->items  as $machine_allocation)
                <div class="row">

                    <div class="w-25"></div>
                    <div class="col-sm-12 col-md-12 col-md-offset-3">

                        <div class="card">
                            <div class="card-header">
                                <h5> تنظیمات ستاب و عملیات کارت {{$machine_allocation->production->serial}} </h5>

                            </div>

                            <div class="table-responsive">
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td colspan="6">
                                            تنظیمات عملیات طبق مسیر محصول کالا ثبت شده است،

                                            در صورت عدم نیاز به هر کدام از عملیات ها، آن را از حالت انتخاب خارج نمایید.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>ردیف</th>
                                        <th>
                                            نام کالا
                                            /
                                            کارت تولید

                                        </th>
                                        <th>اولین عملیات
                                            /
                                            <br/>
                                            اولین عملیات فرعی
                                        </th>
                                        <th> شروع ستاپ (setup)
                                        </th>
                                        <th> شروع عملیات</th>
                                        <th> پایان عملیات</th>
                                        <th> تنظیمات نهایی</th>
                                        <th>
                                            تایید کنترل کیفیت
                                        </th>
                                        <th>
                                            آیا نیاز به تخصیص مجزا دارد؟
                                        </th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp



                                    @foreach($list_product_station_list[$machine_allocation->id] as $line_product_station)

                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>{{$machine_allocation->product->caption}}
                                                <br/>
                                                {{$machine_allocation->production->serial()}}</td>
                                            <td>
                                                {{$line_product_station->station_operation->caption}}
                                                /
                                                <br/>
                                                {{$line_product_station->station_sub_operation->caption}}
                                            </td>
                                            <td>
                                                @if($line_product_station->is_need_start_setup)
                                                    @include("component.input._checkbox_simple",[
                                                            "id"=>"machine_allocation[".$machine_allocation->id."][".$line_product_station->id."][is_need_start_setup]","label"=>"",
                                                        "checked"=>$machine_allocation->line_product_station->is_need_start_setup
                                                        ])
                                                @endif

                                            </td>
                                            <td>

                                                @if($machine_allocation->line_product_station->is_need_start_of_operation)
                                                    @include("component.input._checkbox_simple",[
                                                            "id"=>"machine_allocation[".$machine_allocation->id."][".$line_product_station->id."][is_need_start_of_operation]","label"=>"",
                                                        "checked"=>$machine_allocation->line_product_station->is_need_start_of_operation
                                                        ])
                                                @endif
                                            </td>
                                            <td>

                                                @if($machine_allocation->line_product_station->is_need_end_of_operation)

                                                    @include("component.input._checkbox_simple",[
                                                            "id"=>"machine_allocation[".$machine_allocation->id."][".$line_product_station->id."][is_need_end_of_operation]","label"=>"",
                                                        "checked"=>$machine_allocation->line_product_station->is_need_end_of_operation
                                                        ])
                                                @endif
                                            </td>
                                            <td>
                                                @if($machine_allocation->line_product_station->is_need_final_setting)

                                                    @include("component.input._checkbox_simple",[
                                                           "id"=>"machine_allocation[".$machine_allocation->id."][".$line_product_station->id."][is_need_final_setting]","label"=>"",
                                                       "checked"=>$machine_allocation->line_product_station->is_need_final_setting
                                                       ])
                                                @endif
                                            </td>
                                            <td>
                                                @if($machine_allocation->line_product_station->is_need_for_quality_control)

                                                    @include("component.input._checkbox_simple",[
                                                           "id"=>"machine_allocation[".$machine_allocation->id."][".$line_product_station->id."][is_need_for_quality_control]","label"=>"",
                                                       "checked"=>$machine_allocation->line_product_station->is_need_for_quality_control
                                                       ])
                                                @endif
                                            </td>
                                            <td>
                                                @if($machine_allocation->line_product_station->is_need_allocation_at_first)

                                                    @include("component.input._checkbox_simple",[
                                                           "id"=>"machine_allocation[".$machine_allocation->id."][".$line_product_station->id."][is_need_allocation_at_first]","label"=>"",
                                                       "checked"=>$machine_allocation->line_product_station->is_need_allocation_at_first
                                                       ])
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach


                                    </tbody>

                                </table>
                            </div>


                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="row">
            <div class="col-sm-12" style="text-align: center">

                <button type="submit" class="btn btn-success" id="btn_replace">تایید تخصیص</button>
                <a href="{{route("fabric.production_card.view_card",$production)}}"
                   class="btn btn-outline-dark">بازگشت
                    به کارتابل تولید</a>
            </div>
        </div>
    </form>
@endsection

@section("style")
    <style>
        .amount_number {
            width: 130px !important;
        }
    </style>
@endsection
@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                "product_id": "required",
            }
        });
        $("#btn_replace").click(function () {

            return confirm("آیا از تایید تخصیص اطیمنان دارید؟")
        })
    </script>
@endsection


