@extends('layouts.admin._master')
@section('page_header_title'," داشبورد جاری تولید - ".$production->product->goods_kind->caption)
@section("content")
    <div class="row">
        <div class="col-sm-12 ">

            <div class="card">
                <div class="card-header">
                    <h5>خروجی های ماشین {{$machine->code." ".$machine->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center ">
                            <thead>
                            <tr>

                                <th>  باند خروجی</th>
                                <th> کارت تولید</th>
                                <th>کالا</th>
                                <th>تعداد داف</th>
                                <th>مشخصات هر داف</th>
                                <th> متراژ باقی مانده چله (در زمان شروع بافت)</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp

                                <tr>

                                    <td>
                                        @foreach($machine_allocation as $item)
                                            @php $band_code_warps=$item->band_code>$warps_count?$warps_count:$item->band_code;@endphp
                                        باند {{$item->band_code}} <br/>

                                        @endforeach
                                    </td>
                                    <td>
                                        @php $allocation_show=[]; @endphp
                                        @foreach($machine_allocation as $item)

                                           @if(!isset($allocation_show[$item->production->id]))
                                                @php $allocation_show[$item->production->id]=1; @endphp
                                                {{$item->production->serial()}} <br/>
                                            @endif

                                        @endforeach

                                    </td>
                                    <td>
                                        @php $allocation_product=[]; @endphp
                                        @foreach($machine_allocation as $item)

                                            @if(!isset($allocation_product[$item->production->product_id]))
                                                @php $allocation_product[$item->production->product_id]=1; @endphp
                                                {{$item->product->fullCaption()}} <br/>
                                            @endif
                                        @endforeach


                                    </td>
                                    <td>{{$item->max_number_of_doffs}}</td>
                                    <td>

                                        @include("goods_kind_process.general.machine.allocation_card._allocation_doff_brand",["product"=>$item->product,"machine_allocation"=>$item])


                                    </td>
                                    <td>
                                        @if(isset($amount_list[$band_code_warps]))
                                            {{$amount_list[$band_code_warps]["warps"]["amount_begin_of_weaving"]}}
                                        @endif
                                    </td>


                                </tr>
                                @if(isset($amount_list[$band_code_warps]) &&!$amount_list[$band_code_warps]["warps"]["result"])
                                    <tr>
                                        <td colspan="6" class="alert alert-danger">
                                            <i class="fas fa-minus-circle"></i>
                                            {{$amount_list[$band_code_warps]["warps"]["message"]}}
                                        </td>
                                    </tr>
                                @endif

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ورودی های ماشین {{$machine->code." ".$machine->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>

                                <th style="text-align: right">ماده اولیه</th>
                                <th> ورودی</th>
                                <th>لاین ورودی</th>
                                <th> باند خروجی</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($machine_input_output_band_list as $item)
                                <tr>

                                    <td style="text-align: right">
                                        {{$item->material->fullCaption()}}
                                    </td>
                                    <td>
                                        {{$item->material->goods_kind->caption}}
                                    </td>
                                    <td>{{$item->input_line_code}}</td>
                                    <td>{{$item->getBandCodeList()}}</td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-sm-12" style="text-align: center">
            <br/>
            <br/>
            <a href="{{route("fabric_raw.production_card.view_card",$machine_allocation[0]->production)}}"
               class="btn btn-outline-dark">بازگشت
                به کارتابل تولید</a>
            <a href="{{route("fabric_raw.jacquard.machine_allocation.get_allocation_different",[$machine_allocation[0]->allocation_id,$machine_allocation[0]->production_id])}}"
               class="btn btn-primary">
                ادامه تخصیص</a>

        </div>

    </div>
@endsection
