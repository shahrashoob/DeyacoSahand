@extends('layouts.admin._master')

@section('page_header_title',"داشبورد ماشین آلات")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> درخواست مواد اولیه برای {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route($route_path."submit_select_material",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <input type="hidden" name="allocation_ids"
                               value="{{json_encode($allocations,true)}}">

                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-styling" style="text-align: center!important;">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th></th>
                                        <th>کد کالا</th>
                                        <th>نام کالا</th>
                                        @switch($allocation_diff_unit_type_id)
                                            @case(1)
                                                <th>مقدار درخواست</th>
                                            @break
                                        @endswitch

                                        <th>تعداد بسته بندی</th>
                                        <th>انبار درخواست کالا</th>
                                        <th>انبار تحویل کالا</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=1;@endphp
                                    @foreach($current_machine_input_list_material as $item)
                                        <tr>
                                            <td>{{$row++}}</td>
                                            <td>
                                                <input type="checkbox" name="material[{{$item->material_id}}]"
                                                       value="{{$item->material_id}}">

                                                <input type="hidden" name="product[{{$item->material_id}}]"
                                                       value="{{$item->product_id}}">
                                                <input type="hidden" name="goods_kind[{{$item->material_id}}]"
                                                       value="{{$item->goods_kind_id}}">
                                            </td>
                                            <td>
                                                {{$item->material->code}}
                                            </td>
                                            <td>
                                                {{$item->material->caption}}
                                            </td>
                                            @switch($allocation_diff_unit_type_id)
                                                @case(1)
                                                    <td>
                                                        <input type="number" min="0"
                                                               name="amount_required[{{$item->material_id}}]"
                                                               value="{{$item->amount_required}}">
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0"
                                                               name="number_of_packing[{{$item->material_id}}]"
                                                               value="{{$item->input_line_code_count}}">
                                                    </td>
                                                    @break

                                                @case(4)

                                                    <td>
                                                        {{$item->allocation->getAllocationAmount()}}
                                                        <input type="hidden" min="0"
                                                               name="number_of_packing[{{$item->material_id}}]"
                                                               value="{{$item->allocation->getAllocationAmount()}}">
                                                    </td>
                                                @break
                                            @endswitch


                                            <td>
                                                @if(count($warehouse_option[$item->material_id]["items"]) >0 )
                                                    <select name="warehouse_from_ids[{{$item->material_id}}]">
                                                        @foreach($warehouse_option[$item->material_id]["items"] as $item_option)
                                                            <option
                                                                value="{{$item_option["value"]}}">{{$item_option["text"]}}</option>
                                                        @endforeach

                                                    </select>
                                                @endif
                                            </td>

                                            <td>
                                                @if(count($warehouse_to_option[$item->material_id]["items"]) >1 )
                                                    <select name="warehouse_to_ids[{{$item->material_id}}]">
                                                        @foreach($warehouse_to_option[$item->material_id]["items"] as $item_option)
                                                            <option
                                                                value="{{$item_option["value"]}}">{{$item_option["text"]}}</option>
                                                        @endforeach

                                                    </select>
                                                @endif
                                            </td>


                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <a href="{{route($route_path."index",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary">ثبت و ادامه</button>
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
                "allocation_id": "required",
            }
        });
    </script>
@endsection
