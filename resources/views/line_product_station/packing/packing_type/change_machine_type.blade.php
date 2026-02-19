@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <form id="form1"
          action="{{route("line_product_station.packing.packing_type.submit_change_machine_type",$packing_type)}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <div class="row">
            @if(count($machine_type_input_band_goods_kind) > 0)
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> ویرایش نوع بسته بندی <b>{{$packing_type->caption}}</b> در <b>ورودی های</b> گروه ماشین ها </h5>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive center">
                            <table class="table table-styling">
                                <thead>
                                <tr>

                                    <th>ردیف</th>
                                    <th>گروه ماشین</th>
                                    <th> نام ورودی (رسته کالایی)</th>
                                    <th></th>
                                    <th> </th>

                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($machine_type_input_band_goods_kind as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            {{$item->machine_type_input_band->machine_type->caption}}
                                        </td> <td>
                                            {{$item->machine_type_input_band->caption}} ({{$item->goods_kind->caption}})
                                        </td>
                                        <td>
                                            @php $key=$item->machine_type_input_band_id."_".$item->goods_kind_id;@endphp
                                            <input type="checkbox"
                                                   name="data[machine_type_input_band_goods_kind][{{$key}}]" {{isset($machine_type_input_band_packing_type[$key])?"checked":""}}>
                                        </td>


                                        <td>

                                        </td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>


                    </div>
                </div>

            </div>
            @endif

            @if(count($machine_type_output_band_goods_kind) > 0)
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> ویرایش نوع بسته بندی <b>{{$packing_type->caption}}</b> در <b>خروجی های</b> گروه ماشین ها </h5>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive center">
                            <table class="table table-styling">
                                <thead>
                                <tr>

                                    <th>ردیف</th>
                                    <th>گروه ماشین</th>
                                    <th> نام خورجی (رسته کالایی)</th>
                                    <th></th>
                                    <th> </th>

                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($machine_type_output_band_goods_kind as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            {{$item->machine_type_output_band->machine_type->caption}}
                                        </td> <td>
                                            {{$item->machine_type_output_band->caption}} ({{$item->goods_kind->caption}})
                                        </td>
                                        <td>
                                            @php $key=$item->machine_type_output_band_id."_".$item->goods_kind_id;@endphp
                                            <input type="checkbox"
                                                   name="data[machine_type_output_band_goods_kind][{{$key}}]" {{isset($machine_type_output_band_packing_type[$key])?"checked":""}}>
                                        </td>


                                        <td>

                                        </td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>


                    </div>
                </div>

            </div>
            @endif
            <div class="col-md-12">
                <a href="{{route("line_product_station.packing.packing_type.index")}}"
                   class="btn btn-outline-dark">بازگشت</a>

                <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>
            </div>
        </div>

    </form>

@endsection