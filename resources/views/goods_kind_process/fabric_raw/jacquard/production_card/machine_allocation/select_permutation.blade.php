@extends('layouts.admin._master')
@section('page_header_title'," داشبورد جاری تولید - ".$production->product->goods_kind->caption)
@section("content")
    <form id="form1"
          action="{{route("fabric_raw.jacquard.machine_allocation.submit_select_permutation",[$machine,$production])}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-12 ">

                <div class="card">
                    <div class="card-header">
                        <h5>انتخاب حالت های تخصیص برای ماشین {{$machine->code." ".$machine->caption}}

                        </h5>
                    </div>
                    <div class="card-block">

                        <div class="alert alert-primary">

                            با توجه به اینکه موجودی کالا جهت تخصیص کافی نمی باشد، شما می توانید یکی از حالت های زیر را
                            جهت
                            تخصیص انتخاب نمایید.
                            <br/>
                            <a href="{{route("utility.json_view.allocation_data_type_200",[$production->id,$machine->id,0])}}">مشاهده اطلاعات تخصیص</a>

                        </div>

                        @php $row=1;@endphp
                        @foreach($permutation_list["permutation_list"] as $p_number=>$permutation_item)
                            <div class="row ">
                                <label class="col-lg-2 col-sm-12 text-lg-end">
                                    <input type="radio" name="permutation_select_number" value="{{$p_number}}" required>
                                    <b>حالت {{$p_number++}}</b>
                                </label>
                                <div class="col-sm-12">
                                    <p class="user-select-all">

                                    <ul>
                                        @foreach($permutation_item as $key=>$item)


                                            <li>
@php $key=$key+0;@endphp

                                                @php $product_caption="";@endphp
                                                @foreach($permutation_list["end_result"][$key]["material"] as $material)
                                                    @if($material["priority_number"] >1 )
                                                        @php $product_caption.=$products[$material["material_id"]]->caption." ".$products[$material["material_id"]]->code." ,";@endphp

                                                    @endif

                                                @endforeach
                                                @if(isset($permutation_list["end_result"][$key]["bom_permutation"]))
                                                    {{$permutation_list["end_result"][$key]["bom_permutation"]->getSystemCode()}} -
                                                @endif
                                                @if($product_caption=="")
                                                    تولید بدون جایگزینی
                                                @else

                                                    تولید با
                                                    {{$product_caption}}


                                                @endif
                                                <b>
                                                    ( {{$item}} {{$production->product->unit->caption}})
                                                </b>
                                            </li>

                                        @endforeach
                                    </ul>
                                    </p>
                                </div>
                            </div>
                        @endforeach


                    </div>
                </div>
            </div>

            <div class="col-sm-12" style="text-align: center">

                <a href="{{route("fabric_raw.production_card.view_card",$production)}}"
                   class="btn btn-outline-dark">بازگشت
                    به کارتابل تولید</a>
                <button type="submit"
                        class="btn btn-primary">
                    انتخاب و ادامه
                </button>

            </div>

        </div>
    </form>
@endsection
@section("scripts")
    <script>
        $("#form1').validate({
            rules: {
                "packing_type_id_auto": "required",
            }
        });
    </script>
@endsection
