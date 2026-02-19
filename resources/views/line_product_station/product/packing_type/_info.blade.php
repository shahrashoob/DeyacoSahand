
<form id="form1" action="{{route($route_path.($custom_route??"submit"),[$product,$product_creation_process])}}" method="post"
      autocomplete="off"
      novalidate="novalidate">
    @csrf
    <div class="row">
        <div class="col-md-12" style="overflow: auto">

            <h5>
                لطفا بسته بندی های مجاز را برای کالای
                <b> {{$product->fullCaption()}}</b>
                انتخاب نمایید. </h5>
            <br/>
            @php $row=1;@endphp
            <table class="table col-md-6 center">
                <tr>
                    <th></th>
                    <th>کد  نوع بسته بندی</th>
                    <th>عنوان نوع بسته بندی</th>
                    <th>بسته بندی های مجاز کالا</th>
                    @if($product->possibility_of_sale)
                        <th>بسته بندی های مجاز فروش</th>
                    @endif

                </tr>
                @foreach($product->goods_kind->packing_type as $item)
                    <tr>
                        <td>{{$row++}}</td>
                        <td>
                            <b> {{$item->code}} </b>
                        </td>
                        <td>
                            <b> {{$item->caption}} </b>
                        </td>
                        <td >
                            <input type="checkbox" id="switch-data[{{$item->id}}]"
                                   name="data[packing_type][{{$item->id}}][permission]" {{$product->has_product_type_permission($item->id)?"checked='checked'":""}}
                            ">

                        </td>
                        @if($product->possibility_of_sale)
                            <td>
                                <input type="checkbox" id="switch-data[{{$item->id}}]"
                                       name="data[packing_type][{{$item->id}}][is_it_salable]" {{$product->is_it_salable($item->id)?"checked='checked'":""}}
                                ">
                            </td>
                        @endif

                    </tr>
                @endforeach
            </table>

          @include($view_path."_btn_list".($custom_route??""))

        </div>
    </div>
</form>


