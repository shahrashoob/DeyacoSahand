@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تولید بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> درجه بندی فرم {{$production_form->getCode()}} - باند {{$production_form_item->band_code}} </h5>
                </div>
                <div class="card-block">


                    <div class="w-100"></div>

                    <div class="table-responsive">
                        @include("component.input._lable",["id"=>"","lable"=>"کارت تولید  ","value"=>$production_form_item->production->serial,"class_col"=>"col-md-12"])

                        @include("component.input._lable_product",["id"=>"product".$production_form_item->id,"lable"=>"   پارچه خام",
                    "product_property"=>$production_form_item->production->product,
                    "value"=>$production_form_item->production->product->code."-".$production_form_item->production->product->caption,"col"=>4])

                        @include("component.input._lable",["id"=>"","lable"=>" کد غلطک پارچه","value"=>$production_form->carrier->code])

                        @include("component.input._lable",["id"=>"","lable"=>"متراژ  ","value"=>$production_form_item->amount." ".$production_form_item->product->unit->caption,"class_col"=>"col-md-12"])
                        @include("component.input._lable",["id"=>"amount_after_control","lable"=>"متراژ (پس از کنترل کیفیت)  ","value"=>$amount_after_control." ".$production_form_item->product->unit->caption,"class_col"=>"col-md-3"])
                        <div class="col-md-12">
                            @foreach($production_form_item->lot_numbers as $item)

                                همبافت  {{$item->lot_number->code??"***"}}:
                                <b>
                                    {{isset($amount_after_control_list[$item->lot_number->id])?$amount_after_control_list[$item->lot_number->id]:"***"}}

                                    {{$production_form_item->product->unit->caption}}
                                </b>  |
                            @endforeach
                        </div>


                        <form id="form1"
                              action="{{route("fabric_raw.production_form.grading.submit_section",$production_form_item)}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                           <div class="col-md-12" style="overflow: auto">
                               <table class="table table-styling" style="max-width:600px;margin: auto; text-align: center">
                                   <thead>
                                   <tr>
                                       <th>#</th>
                                       <td>همبافت (لات)</td>
                                       <td>از متراژ</td>
                                       <td>تا متراژ</td>
                                       <td>درجه</td>
                                       <td></td>
                                   </tr>

                                   </thead>

                                   <tbody>
                                   @foreach($list_grading as $item)
                                       @if($item->end_point)
                                           <tr>
                                               <td>{{$item->section_degree_number}}</td>
                                               <td>
                                                   {{$item->lot_number->code??"***"}}
                                               </td>
                                               <td>{{$item->start_point}}</td>
                                               <td> {{$item->end_point}} </td>
                                               <td> {{$item->degree->caption}} </td>
                                               <td>
                                                   <a href="{{route("fabric_raw.production_form.grading.delete_section",[$production_form_item,$item])}}"
                                                      class="text-danger"
                                                      onclick="return confirm('در صورت حذف این رکورد، همه رکوردهای بعدی نیز حذف می گردد \n آیا از حذف اطمینان دارید؟')">
                                                       <i class="fa fa-trash"></i><i class="fa fa-arrow-down"></i>
                                                   </a>

                                               </td>
                                           </tr>
                                       @endif
                                   @endforeach
                                   </tbody>
                               </table>
                           </div>
                            @if(!$item->end_point)
                                <div class="row alert alert-info"
                                     style="text-align: center; max-width: 500px;margin:auto; margin-top: 15px">

                                    @include("component.input._lable",["id"=>"end_point","class_col"=>"col-md-12","value"=>$item->lot_number->code??"","label"=>"همبافت (لات)"])


                                    @include("component.input._number",["id"=>"end_point","class_col"=>"col-md-6 col-xs-12","autofocus"=>1,"label"=>" از متراژ ".$item->start_point." تا متراژ "])

                                    @include("component.input._aotocomplet2",[
                                             "id"=>"degree_id",
                                             "option"=>$degree_option["items"],
                                             "val"=>"",
                                             "text"=>"",
                                            "label"=>"درجه",
                                             "class_col"=>"col-md-6 col-xs-12"
                                             ])
                                    <div class="col-xs-12 col-md-12">
                                        <ul class="text-danger ">
                                            @foreach ($errors->all() as $error)
                                                <li> {{$error}}</li>
                                            @endforeach
                                        </ul>
                                        <button type="submit" class="btn btn-success"><i
                                                class="fa fa-plus"></i>
                                            افزودن جدید
                                        </button>
                                    </div>
                                </div>

                            @endif
                        </form>
                        @if($item->end_point)
                            <br/>
                            <div class="col-md-12" style="text-align: center">
                                <form id="form1"
                                      action="{{route("fabric_raw.production_form.grading.end_of_section",$production_form_item)}}"
                                      method="post"
                                      autocomplete="off"
                                      novalidate="novalidate">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('آیا از پایان درجه بندی اطمینان دارید؟')"
                                            class="btn btn-primary"><i class="fa fa-stop-circle"></i>
                                        پایان درجه بندی
                                    </button>
                                </form>
                            </div>

                        @endif
                        <div class="col-md-12 center" >
                            <br/>
                            <br/>
                            <a href="{{route("fabric_raw.production_form.grading.index",$production_form_item)}}"
                               class="btn btn-outline-dark">بازگشت</a>

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
                    }
                });
                @if(!$item->end_point)
                $('#form1').submit(function () {
                    if ($("#degree_id").val() == "") {
                        alert("لطفا فیلد درجه بندی را تکمیل نمایید.");
                        return false;
                    }
                })
                @endif
            </script>
@endsection
