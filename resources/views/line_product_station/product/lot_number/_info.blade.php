<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><b> شماره لات های کالا </b></h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>شماره لات</th>

                            <th style="width: 50px"></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1; @endphp
                        @foreach($product->lot_number as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    @include("line_product_station.product.lot_number._label",["lot_number"=>$item,"id"=>$item->id])

                                    @include("line_product_station.product.lot_number._collapse",["lot_number"=>$item,"id"=>$item->id])

                                </td>
                                <td>
                                    <a href="{{route($route_path."edit",[$product,$item,$product_creation_process])}}"><i
                                                class="fa fa-edit text-primary"></i> </a>

                                </td>

                            </tr>
                            @php $value=$item->getPropertyValue( 1, "value" ); @endphp
                            @if($value   )

                            @endif
                            <tr>
                                <td colspan="3">
                                    {{"کیلوگرم بر متر کالا: ".$value}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </div>

    <div class="col-sm-4">
        <form id="form1" action="{{route($route_path."store",[$product,$product_creation_process])}}" method="post"
              autocomplete="off"
              novalidate="novalidate">
            @csrf
            <div class="row">

                @include("component.input._text",["id"=>"code",'label'=>"شماره  لات (همبافت) جدید","value"=>""])


            </div>


            <button type="submit" class="btn btn-primary"> ثبت</button>


        </form>
    </div>

    @include($view_path."_btn_list")
</div>


