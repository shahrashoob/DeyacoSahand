<form id="form1"
      action="{{route("fabric_raw.jacquard.machine.material_return_to_warehouse.submit_remaining_packing_form",[$machine,$warehouse->id])}}"
      method="post" autocomplete="off" novalidate="novalidate">
    @csrf
    <input type="hidden" id="change_of_grade" name="change_of_grade" value="0">
    <input type="hidden" id="machine_allocation_modification_type_id" name="machine_allocation_modification_type_id"
           value="{{$machine_allocation_modification_type_id}}">
    <div class="row">
        <div class="col-sm-12">
            @if(count($master_packing_form_list) > 0)
                <div class="card">
                    <div class="card-header"><h5>
                            @if($machine_allocation_modification_type_id==1)
                                برگشت مواد اولیه به انبار
                            @else
                                انبارگردانی {{$warehouse->caption}}
                            @endif
                        </h5></div>
                    <div class="card-block" style="overflow: auto">

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
                                                        {{$item->packing_type->fullCaption()}}
                                                    </div>
                                                    <div class="col-md-12 center">

                                                        <input type="radio" value="6021101"
                                                               @if(isset($consumed_list[$item->id]) && $consumed_list[$item->id]==6021101))
                                                               checked
                                                               @endif                                                               data-packing_form_id="{{$item->id}}"
                                                               class="consumed_radio" name="consumed[{{$item->id}}]"
                                                               id="{{$item->id}}_1"
                                                               required>
                                                        <label for="{{$item->id}}_1"> مصرف نشده</label>

                                                        <input type="radio" value="6021102"
                                                               @if(isset($consumed_list[$item->id]) && $consumed_list[$item->id]==6021102))
                                                               checked
                                                               @endif
                                                               data-packing_form_id="{{$item->id}}"
                                                               class="consumed_radio" name="consumed[{{$item->id}}]"
                                                               id="{{$item->id}}_2"
                                                               required>
                                                        <label for="{{$item->id}}_2"> مصرف شده</label>

                                                        <input type="radio" value="6021103"
                                                               @if(isset($consumed_list[$item->id]) && $consumed_list[$item->id]==6021103))
                                                               checked
                                                               @endif                                                               data-packing_form_id="{{$item->id}}"
                                                               class="consumed_radio" name="consumed[{{$item->id}}]"
                                                               id="{{$item->id}}_3"
                                                               required>
                                                        <label for="{{$item->id}}_3"> کاملا مصرف شده</label>

                                                    </div>
                                                    <div class="col-md-12" id="input_packing_{{$item->id}}"
                                                         @if(!isset($consumed_list[$item->id]) ||(isset($consumed_list[$item->id]) && $consumed_list[$item->id]!=6021102)))
                                                         style="display: none"
                                                        @endif
                                                    >
                                                        <div class="row">

                                                            @include("component.input._number",["id"=>"gross_weight_".$item->id,"name"=>"gross_weight[".$item->id."]","is_smart_object"=>$smart_object??null, "label"=>"وزن ناخالص","required"=>1,"value"=>isset($gross_weight_list[$item->id])?$gross_weight_list[$item->id]:"","class_col"=>"col-md-3 col-sm-12 gross_weight_input"])

                                                            @include("component.input._number",["id"=>"sub_packing_form_number[".$item->id."]", "label"=>"تعداد بسته بندی فرعی","required"=>1,"value"=>isset($sub_packing_form_number_list[$item->id])?$sub_packing_form_number_list[$item->id]:"","class_col"=>"col-md-3 col-sm-12"])

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


                    </div>
                </div>
            @endif
        </div>
        @if($smart_object)
        <div class="col-md-12">
            @include("component.input._lable",["lable"=>"باسکول ","value"=>$smart_object->caption??"","url"=>$url_scale??"","class_col"=>"col-md-4"])

        </div>
        @endif
        <div class="col-md-12 center">


            <br/>
            <br/>
            <a href="{{route($dashboard_route."view",$machine)}}"
               class="btn btn-outline-dark">بازگشت</a>

            <button type="submit" class="btn btn-primary" id="btn_submit"
            >
                تایید و ادامه

            </button>

            <a href="{{route($route_log_path."index",$machine)}}" class="btn btn-primary"
            >
                مشاهده سابقه برگشت مواد اولیه

            </a>

        </div>
    </div>

</form>
