@extends('layouts.admin._master')

@section('page_header_title',"داشبورد ماشین آلات")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> پایان برگردان {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("warps.matthys.machine.end_of_beaming.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="w-100"></div>
                        @include("component.input._lable",["id"=>"amount","label"=>"نام کالا ","value"=>$product->fullCaption()])

                        @if($machine_type_output_band_goods_kind->machine_type_calculation_method_for_unit_id == 1)
                            @include("component.input._number",["id"=>"amount","label"=>$product->unit->measurement,"value"=>""])
                        @else
                            @include("component.input._lable",["id"=>"amount","label"=>$product->unit->measurement,"value"=>$amount])
                            @include("component.input._hidden",["id"=>"amount","value"=>$amount])
                        @endif

                        @if($machine_type_output_band_goods_kind->machine_type_calculation_method_for_sub_unit_id == 1)
                            @include("component.input._number",["id"=>"gross_weight","label"=>"وزن ناخالص  ","value"=>""])
                        @else
                            @include("component.input._lable",["id"=>"gross_weight","label"=>"وزن ناخالص ","value"=>$sub_amount])
                            @include("component.input._hidden",["id"=>"gross_weight","value"=>$sub_amount])

                        @endif


                        @include("component.input._lable",["id"=>"packing_type_label","label"=>"نوع بسته بندی ","value"=>$production_form->packing_type->caption])

                        @include("component.input._lable",["id"=>"","lable"=>" شماره حامل","value"=>$production_form->carrier->code])

                        @include("component.input._lable",["id"=>"","lable"=>"درجه","value"=>$main_degree->caption??""])

                        <div class="col-md-12 offset-md-12">
                            <div class="form-group">
                                <label>لات (ها) :</label>
                                @foreach($production_form->lot_numbers as $item)

                                    @include("line_product_station.product.lot_number._label",["lot_number"=>$item->lot_number,"amount"=>null,"id"=>$item->id])
                                    ,
                                @endforeach
                                @foreach($production_form->lot_numbers as $item)
                                    @include("line_product_station.product.lot_number._collapse",["lot_number"=>$item->lot_number,"id"=>$item->id])

                                @endforeach
                            </div>
                        </div>


                        <div class="col-md-12">

                            @if($sub_amount === 0 )
                                <div class="alert alert-warning">مقدار وزن ناخالص به درستی خوانش نشد، لطفا یکبار دیگر تلاش کنید.</div>
                                <a href="{{route("warps.matthys.machine.end_of_beaming.index",$machine)}}"
                                   class="btn btn-warning"><i class="fa fa-refresh"></i> تلاس مجدد </a>

                                <a href="{{route("warps.matthys.machine.dashboard.view",$machine)}}"
                                   class="btn btn-outline-dark">بازگشت</a>
                            @else
                                <button type="submit" class="btn btn-success">ثبت پایان برگردان</button>
                                <a href="{{route("warps.matthys.machine.dashboard.view",$machine)}}"
                                   class="btn btn-outline-dark">بازگشت</a>
                            @endif
                        </div>


                    </form>
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
        $('#form1').validate({
            rules: {
                "carrier_code": "required",
                "packing_type_id": "required"
            }
        });
    </script>
@endsection
