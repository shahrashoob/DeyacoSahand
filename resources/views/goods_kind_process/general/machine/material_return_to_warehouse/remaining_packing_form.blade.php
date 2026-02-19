@extends('layouts.admin._master') @section('page_header_title',"داشبورد  تولید")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5> برگشت مواد اولیه به انبار</h5></div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.machine.material_return_to_warehouse.submit_remaining_packing_form",$machine)}}"
                          method="post" autocomplete="off" novalidate="novalidate"> @csrf
                        <div class="row">
                            <div class="col-md-12 alert alert-info">
                                <div class="">لطفا وضعیت بسته بندی های جهت برگشت به انبار را مشخص نمایید:</div>
                            </div>

                            <div class="col-md-12 ">
                                <table class="table  table-hover">

                                    <tbody>
                                    @foreach($master_packing_form_list as $item)
                                        <tr>
                                            <td>
                                                <div class="row ">
                                                    <div class="col-md-12 center ">
                                                        <h5>بسته بندی {{$item->code}}
                                                            ({{$material_list[$item->items()->first()->product_id]->caption??""}}
                                                            )</h5>
                                                    </div>
                                                    <div class="col-md-12 center">

                                                        <input type="radio" value="6021101"
                                                               data-packing_form_id="{{$item->id}}"
                                                               class="consumed_radio" name="consumed[{{$item->id}}]" id="{{$item->id}}_1"
                                                               required>
                                                        <label for="{{$item->id}}_1"> مصرف نشده</label>

                                                        <input type="radio" value="6021102"
                                                               data-packing_form_id="{{$item->id}}"
                                                               class="consumed_radio" name="consumed[{{$item->id}}]" id="{{$item->id}}_2"
                                                               required>
                                                        <label for="{{$item->id}}_2"> مصرف شده</label>

                                                        <input type="radio" value="6021103"
                                                               data-packing_form_id="{{$item->id}}"
                                                               class="consumed_radio" name="consumed[{{$item->id}}]" id="{{$item->id}}_3"
                                                               required>
                                                        <label for="{{$item->id}}_3"> کاملا مصرف شده</label>

                                                    </div>
                                                    <div class="col-md-12" id="input_packing_{{$item->id}}" style="display: none">
                                                        <div class="row">

                                                            @include("component.input._number",["id"=>"gross_weight[".$item->id."]", "label"=>"وزن ناخالص","required"=>1,"value"=>"","class_col"=>"col-md-3 col-sm-12"])

                                                            @include("component.input._number",["id"=>"sub_packing_form_number[".$item->id."]", "label"=>"تعداد بسته بندی فرعی","required"=>1,"value"=>"","class_col"=>"col-md-3 col-sm-12"])

                                                        </div>
                                                    </div>


                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                            <div class="col-md-6 center">
                                <br/>
                                <br/>
                                <a href="{{route($dashboard_route."view",$machine)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"
                                      >
                                    تایید و ادامه

                                </button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section("styles")
@endsection

@section("scripts")
    <script>
        $(".consumed_radio").change(function () {

            if ($(this).val() == 6021101 || $(this).val() == 6021103) {
                $("#input_packing_" + $(this).data("packing_form_id")
                ).css("display", "none");
            } else {
                $("#input_packing_" + $(this).data("packing_form_id")
                ).css("display", "");
            }
        })
        $('#form1').validate({
            rules: {
                "description": "required",
            }
        }); </script>
@endsection
