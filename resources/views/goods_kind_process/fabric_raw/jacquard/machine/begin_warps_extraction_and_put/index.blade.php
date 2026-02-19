@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> شروع استخراج چله و چله گذاری {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.machine.begin_warps_extraction_and_put.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        @include("goods_kind_process.fabric_raw.public._shift_and_counter")

                        @if(count($packing_form_list)>0)
                            <div class="row">
                                <div class="col-md-12 alert alert-info">
                                    <div class="">لطفا وضعیت بسته بندی های جهت برگشت به انبار را مشخص نمایید:</div>
                                </div>


                                <div class="col-md-12 ">
                                    <table class="table  table-hover">

                                        <tbody>
                                        @foreach($packing_form_list as $item)
                                            <tr>
                                                <td>
                                                    <div class="row ">
                                                        <div class="col-md-12 center ">
                                                            <h5>بسته بندی {{$item->code}}
                                                                ({{$item->items()->first()->product->caption??""}}
                                                                )</h5>
                                                        </div>
                                                        <div class="col-md-12 center">
                                                            {{$item->packing_type->fullCaption()}}
                                                        </div>
                                                        <div class="col-md-12 center">


                                                            <input type="radio" value="6021102"
                                                                   data-packing_form_id="{{$item->id}}"
                                                                   class="consumed_radio" name="consumed[{{$item->id}}]"
                                                                   id="{{$item->id}}_2"
                                                                   required>
                                                            <label for="{{$item->id}}_2"> مصرف شده</label>

                                                            <input type="radio" value="6021103"
                                                                   data-packing_form_id="{{$item->id}}"
                                                                   class="consumed_radio" name="consumed[{{$item->id}}]"
                                                                   id="{{$item->id}}_1"
                                                                   required>
                                                            <label for="{{$item->id}}_1">کاملا مصرف شده</label>
                                                        </div>
                                                        <div class="col-md-12" id="input_packing_{{$item->id}}"
                                                             style="display: none"
                                                        >
                                                            <div class="row">

                                                                @include("component.input._number",["id"=>"amount[".$item->id."]", "label"=>"مقدار نهایی باقی مانده","required"=>1,"value"=>"","class_col"=>"col-md-3 col-sm-12"])


                                                            </div>
                                                        </div>


                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>

                            </div>
                        @endif


                        <div class="col-md-12">
                            <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">تایید شروع استخراج چله</button>
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
                "shift_work_id_auto": "required",
                "contour_1_value": "required",
                "carrier_id": "required",
                {{--                @if($warps_request)--}}
                {{--                    @foreach($warps_request->items as $item)--}}
                {{--                "item_{{$item->id}}_auto": "required",--}}
                {{--                @endforeach--}}
                {{--                @endif--}}
            }
        });
        $(".consumed_radio").change(function () {

            if ($(this).val() == 6021101 || $(this).val() == 6021103) {
                $("#input_packing_" + $(this).data("packing_form_id")
                ).css("display", "none");
            } else {
                $("#input_packing_" + $(this).data("packing_form_id")
                ).css("display", "");
            }
        })
    </script>
@endsection
