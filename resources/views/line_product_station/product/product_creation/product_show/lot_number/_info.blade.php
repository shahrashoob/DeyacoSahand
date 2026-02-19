<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><b>  شماره لات های کالا </b></h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>شماره لات </th>

                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1; @endphp
                        @foreach($product->lot_number as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    @include("line_product_station.product.product_creation.product_show.lot_number._label",["lot_number"=>$item,"id"=>$item->id])

                                    @include("line_product_station.product.product_creation.product_show.lot_number._collapse",["lot_number"=>$item,"id"=>$item->id])

                                </td>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </div>


    @include($view_path."_btn_list")
</div>


