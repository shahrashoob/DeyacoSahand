@php $case_id=$product->supply_type_id;
if($product->goods_kind->production_algorithm_type_id==3){
	$case_id=-1;
}
@endphp
@switch($case_id)
    @case(2)
    @case(3)
    @case(4)

    <div class="col-sm-12">
        <div class="alert alert-warning">
            با توجه به نوع تامین کالا، امکان تعریف جریان همبافتی (مواد) برای این کالا وجود ندارد.
        </div>
       @include($view_path."_btn_list")

    </div>
    @break
    @case(1)
    <div class="col-sm-12 mb-3">
        <h5 class="mb-3">لیست جریان های مواد {{$product->fullCaption()}}
        </h5>


        <div class="table-responsive">
            <table class="table table-styling" style="text-align: center!important;">
                <thead>
                <tr>
                    <td>ردیف</td>
                    <th>مسیر - محصول</th>
                    <th>BOM</th>
                    <th>گروه ماشین</th>
                    <th></th>
                    <th></th>
                </tr>

                </thead>
                <tbody>
                @php $row=1;@endphp
                @foreach($product->route()->where("active_status_id",1200)->get() as $route)

                    @foreach($route->bom as $bom)
                        @foreach($bom->product_route->line_product_station()->groupBy("machine_type_id")->get() as $line_product_station)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>{{$route->caption}}</td>
                                <td>{{$bom->caption}}</td>
                                <td>
                                    {{$line_product_station->machine_type->caption}}

                                </td>
                                <td>
                                    <a href="{{route($route_path."graph",[$product,$bom,$line_product_station,$product_creation_process])}}">
                                        <i class="fa  fas fa-share-alt"></i> طراحی مسیر جریان تولید
                                    </a>
                                </td>
                                <td>
                                    <a href="{{route($route_path."draw_graph_one_to_one",[$product,$bom,$line_product_station,$product_creation_process])}}"
                                    onclick="return confirm('در صورتی که گراف یک به یک را رسم نمایید، تمامی ارتباطات قبلی حذف می گردد. \n آیا از این اقدام اطمینان دارید؟')"
                                    >
                                        <i class="fa  fa-expand-arrows-alt"></i> طراحی گراف یک به یک
                                    </a>
                                </td>
                            </tr>
                        @endforeach


                    @endforeach


                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include($view_path."_btn_list")

    @break
    @case(-1)
    <div class="col-sm-12">
        <div class="alert alert-warning">
            با توجه به نوع روش برنامه ریزی تولید در رسته کالایی، امکان تعریف کالاهای مصرفی برای این کالا وجود ندارد.
        </div>
    </div>


    @include($view_path."_btn_list")

    @break

@endswitch



