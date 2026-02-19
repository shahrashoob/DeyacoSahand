@php $case_id=$product->supply_type_id;
if($product->goods_kind->production_algorithm_type_id==3){
	$case_id=-1;
}
@endphp

@switch($case_id)

    @case(-1)
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    با توجه به نوع روش برنامه ریزی تولید در رسته کالایی، امکان تعریف کالاهای مصرفی برای این کالا وجود
                    ندارد.
                </div>
            </div>
            <div class="col-md-12">

                @include($view_path."_btn_list_1")

            </div>
        </div>
        @break

    @default
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><b>لیست محصولات جایگزین </b></h5>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>کد کالا</th>
                                    <th>نام کالا</th>
                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1; @endphp
                                @foreach($product->replace_product as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>{{$item->replace_product->code??""}}</td>
                                        <td>{{$item->replace_product->caption??""}}</td>
                                        <td>
                                            <a class="text-danger"
                                               href="{{route($route_path."delete",[$product,$item->replace_product,$product_creation_process])}}"><i
                                                    class="fa fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>

                </div>
            </div>

        </div>
        <div class="row">
            @if(!isset($desable_insert_panel))
                @include("line_product_station.product.replace_product._insert")
            @else
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>افزودن کالای جایگزین مصرف </h5>
                        </div>
                        <div class="card-block">
                            @include("line_product_station.product.replace_product._insert")
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @break

@endswitch


