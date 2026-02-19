@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تولید بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> بسته بندی فرم {{$production_form->getCode()}} - باند {{$production_form_item->band_code}} </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        @include("component.input._lable",["id"=>"","lable"=>"کارت تولید  ","value"=>$production_form_item->production->serial,"class_col"=>"col-md-12"])

                        @include("component.input._lable_product",["id"=>"product".$production_form_item->id,"lable"=>"   پارچه خام",
                    "product_property"=>$production_form_item->production->product,
                    "value"=>$production_form_item->production->product->code."-".$production_form_item->production->product->caption,"col"=>4])

                        @include("component.input._lable",["id"=>"","lable"=>" کد غلطک پارچه","value"=>$production_form->carrier->code])

                        @include("component.input._lable",["id"=>"","lable"=>"متراژ  ","value"=>$production_form_item->amount." ".$production_form_item->product->unit->caption,"class_col"=>"col-md-12"])
                        @include("component.input._lable",["id"=>"amount_after_control","lable"=>"متراژ (پس از کنترل کیفیت)  ","value"=>$production_form_item->amount_after_control." ".$production_form_item->product->unit->caption,"class_col"=>"col-md-3"])


                        <form id="form1"
                              action="{{route("fabric_raw.production_form.register_packing_carrier.submit",$production_form_item)}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                            <table class="table table-styling" style="width: 60%; margin: auto; text-align: center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <td>از متراژ</td>
                                    <td>تا متراژ</td>
                                    <td>درجه</td>
                                    <td style="width: 25%">شماره پالت</td>
                                </tr>

                                </thead>

                                <tbody>
                                @foreach($list_grading as $item)
                                    <tr>
                                        <td>{{$item->section_degree_number}}</td>
                                        <td>{{$item->start_point}}</td>
                                        <td>
                                            {{$item->end_point}}
                                        </td>
                                        <td>

                                            {{$item->degree->caption}}
                                        </td>
                                        <td>
                                            <input type="text" name="data[carrier][{{$item->id}}]"
                                                   id="carrier_{{$item->id}}"
                                                   required
                                                   style="width: 130px"
                                                   value="{{isset($carrier_data["carrier"][$item->id])?$carrier_data["carrier"][$item->id]:""}}"
                                            />
                                        </td>
                                    </tr>
                                @endforeach


                                </tbody>
                            </table>
                            <div class="col-md-12" style="text-align: center">
                                <button type="submit"
                                        onclick="return confirm('آیا از ثبت پالت های بسته بندی اطمینان دارید؟')"
                                        class="btn btn-primary">
                                    ثبت پالت های بسته بندی
                                </button>
                            </div>
                        </form>


                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.production_form.dashboard.view",$production_form)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                        </div>


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
        $('#form1').validate({
            rules: {
                "degree_id_auto": "required",
                "end_point": "required",
                @foreach($list_grading as $item)
                "carrier_{{$item->id}}": "required",
                @endforeach
            }
        });
    </script>
@endsection
