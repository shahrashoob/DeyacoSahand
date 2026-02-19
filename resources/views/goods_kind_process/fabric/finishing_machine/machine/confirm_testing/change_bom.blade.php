@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    @php $unit_caption=$machine_allocation->product->unit->caption;@endphp
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  {{$machine->fullCaption()}} - بررسی تست کالا
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric.finishing_machine.machine.confirm_testing.submit_change_bom",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="w-100">BOM مصرف شده برای تولید مقدار تست</div>

                        <div class="table-responsive center" style="font-size: 12px">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ماده اولیه</th>

                                    <th>مقدار</th>
                                    <th>تعداد</th>
                                    <th>درصد استفاده</th>
                                    <th>مقدار مورد نیاز برای یک واحد کالا</th>

                                </tr>

                                </thead>
                                <tbody>

                                @php $row=1; @endphp
                                @foreach($current_machine_inputs as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>

                                            {{($item->material->code??"")." - ".($item->material->caption??"")}}

                                        </td>

                                        <td>{{round($item->amount??"",7)}} {{$item->material->unit->caption}}</td>
                                        <td>{{round($item->number,7)}}</td>
                                        <td>{{round($item->percent_of_use,7)}}</td>
                                        <td>
                                            <input  name="item[{{$item->input_line_code."_".$item->material_id}}]" type="number" style="width: 60px" value="{{round($item->amount_for_one_unit(),7)}}" >
                                            {{$unit_caption}}</td>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>


                        <br/>

                        <div class="col-md-12">
                            <a href="{{route("fabric.finishing_machine.machine.confirm_testing.index",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary" onclick="return confirm('در صورت تایید BOM کالا به مقدار های جدید بروز می شود.\nآیا از این اقدام اطمینان دارید؟')">ثبت تغیرات و ادامه</button>


                        </div>


                    </form>
                </div>
            </div>

        </div>
        <div class="col-md-12 center">


        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
