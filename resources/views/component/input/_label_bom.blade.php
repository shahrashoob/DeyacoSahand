<div class="{{ isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">
    <div class="form-group">


        <label>{{$lable??$label??""}}:</label>
        <a href="#!" data-toggle="collapse" data-target="#{{$id??"collapseExample"}}"
           aria-expanded="false" aria-controls="{{$id??"collapseExample"}}">
            <b>{{isset($value)?$value:""}}</b>
        </a>

        <div class="collapse col-md-12" id="{{$id??"collapseExample"}}">
            <div class="alert " style="border: 1px solid #0b0b0b; border-radius: 10px">
                <div class="row">


                    @switch($product->supply_type_id )
                        @case(1)
                        <div class="table-responsive center" style="font-size: 12px">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ماده اولیه</th>

                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1; @endphp
                                @foreach($bom->items as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>
                                            {{($item->material->code??"")." - ".($item->material->caption??"")}}
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                        @break

                        @case(3)
                        <div class="table-responsive center" style="font-size: 12px">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ماده اولیه</th>
                                    <th>پیمانکار</th>
                                    <th>عملیات پیمانکار</th>
                                    <th>انبار تحویل کالا</th>
                                    <th>واحد تحویل کالا</th>
                                    <th>مقدار</th>
                                    <th>تعداد</th>
                                    <th>درصد استفاده</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1; @endphp
                                @foreach($bom->items as $item)
                                    <tr>
                                        <td>{{$row++}}</td>

                                        <td>  {{($item->material->code??"")." - ".($item->material->caption??"")}} </td>
                                        <td>{{$item->contractor->caption??""}}</td>
                                        <td>{{$item->contractor_operation->caption??""}}</td>
                                        <td>{{$item->warehouse->caption??""}}</td>
                                        <td>{{$item->delivery_unit->caption??""}}</td>
                                        <td>{{$item->amount??""}}</td>
                                        <td>{{$item->number}}</td>
                                        <td>{{$item->percent_of_use}}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                        @break

                    @endswitch


                </div>
            </div>
        </div>
    </div>
</div>
