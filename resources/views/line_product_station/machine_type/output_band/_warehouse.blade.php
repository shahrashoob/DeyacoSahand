<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5> تنظیمات انبار تحویل کالای تولید {{$machine_type->caption}} در رسته کالایی در رسته
                کالایی {{$machine_type_output_band_goods_kind->goods_kind->caption}}</h5>
        </div>
        <div class="card-block">

            <form id="form1"
                  action="{{route("line_product_station.machine_type.output_band.submit_output_band_warehouse",[$machine_type,$machine_type_output_band_goods_kind])}}"
                  method="post"
                  autocomplete="off"
                  novalidate="novalidate">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive" >
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th> کد درجه</th>
                                    <th> نام درجه</th>
                                    <th>انبار تحویل کالا</th>
                                    <th>انبار کنترل کیفیت</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($degrees as $degree)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            {{$degree->code}}
                                        </td>
                                        <td>
                                            {{$degree->caption}}
                                        </td>
                                        <td>
                                            @include("component.input._select_simple",[
                                                        "id"=>"warehouse_".$degree->id,
                                                        "label"=>"",
                                                        "option"=>$warehouse_option[$degree->id]["items"],
                                                        "text"=>$warehouse_option[$degree->id]["text"],
                                                        "val"=>$warehouse_option[$degree->id]["value"],
                                                        "class_col"=>""
                                                        ])
                                        </td>
                                        <td>
                                            @include("component.input._select_simple",[
                                                        "id"=>"quality_control_warehouse_".$degree->id,
                                                        "label"=>"",
                                                        "option"=>$quality_control_warehouse_option[$degree->id]["items"],
                                                        "text"=>$quality_control_warehouse_option[$degree->id]["text"],
                                                        "val"=>$quality_control_warehouse_option[$degree->id]["value"],
                                                        "class_col"=>""
                                                        ])
                                        </td>
                                    </tr>

                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <a href="{{route("line_product_station.machine_type.output_band.index",$machine_type)}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت انبارهای تحویل کالا</button>
                    </div>

                </div>

            </form>

        </div>
    </div>
</div>