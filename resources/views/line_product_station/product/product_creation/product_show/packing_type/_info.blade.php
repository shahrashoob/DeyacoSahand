@csrf
<div class="row">
    <div class="col-md-12">

        <h5>
             بسته بندی های مجاز را برای کالای
            <b> {{$product->fullCaption()}}</b>

        <br/>    <br/>
        @php $row=1;@endphp
        <table class="table col-md-6 center">
            <tr>
                <th></th>
                <th>کد نوع بسته بندی</th>
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
                    <td>
                        {!! $product->has_product_type_permission($item->id) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}




                    </td>
                    @if($product->possibility_of_sale)
                        <td>
                            {!!$product->is_it_salable($item->id)?'<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}

                        </td>
                    @endif

                </tr>
            @endforeach
        </table>

                  @include($view_path."_btn_list")

    </div>
</div>



