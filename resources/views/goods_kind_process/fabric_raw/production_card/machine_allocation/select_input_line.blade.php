@extends('layouts.admin._master')
@section('page_header_title'," داشبورد جاری تولید - ".$reserve_production->product->goods_kind->caption)
@section("content")
        <div class="row">
            <div class="col-sm-12 ">

                <div class="card">
                    <div class="card-header">
                        <h5>خروجی های ماشین  {{$machine->code." ".$machine->caption}}
                        </h5>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling center ">
                                <thead>
                                <tr>

                                    <th> باند خروجی</th>
                                    <th> کارت تولید</th>
                                    <th >کالا</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($machine_allocation as $item)
                                    <tr>

                                        <td >
                                            باند {{$item->band_code}}
                                        </td>
                                        <td>
                                            {{$item->reserve_production->serial()}}
                                        </td>
                                        <td> {{$item->product->fullCaption()}}</td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ورودی های ماشین  {{$machine->code." ".$machine->caption}}
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
                <a href="{{route("fabric_raw.dashboard.view_card",$machine_allocation[0]->reserve_production)}}" class="btn btn-outline-dark">بازگشت
                    به کارتابل تولید</a>
                <a href="{{route("fabric_raw.machine_allocation.get_allocation_different",$machine_allocation[0]->allocation_id)}}" class="btn btn-primary">
                    ادامه تخصیص</a>

            </div>

        </div>
@endsection
