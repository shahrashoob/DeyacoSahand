<div class="col-sm-12">

    <div class="card">
        <div class="card-header">
            <h5>لیست آیتم های موجود در حامل</h5>
        </div>
        <div class="card-block">

            <div class="table-responsive">
                <table class="table table-styling center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <td>ردیف</td>
                        <th>مقدار نهایی({{$packing_form->product->unit->caption??""}})</th>
                        <th>مقدار فرعی ({{$packing_form->product->sub_unit->caption??""}})</th>
                        <th></th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=1;@endphp
                    @foreach($packing_form->items as $item)
                        <tr>
                            <td>{{$row++}}</td>
                            <td>{{$item->getCode()}}</td>
                            <td>{{$item->final_amount}}</td>
                            <td>{{$item->sub_amount}}</td>
                            <td>
                                @if( $post_user->checkButtonPermission($cancel_item_route) && $packing_form->status_id== 7007001)
                                    <a href="{{route($cancel_item_route,[$packing_form,$item])}}" class="text-danger" onclick="return myFunction('{{$item->getCode()}}')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                @endif
                            </td>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>
