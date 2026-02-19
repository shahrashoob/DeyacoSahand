<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5> شید های <b> {{$product->fullCaption()}}</b></h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th style="width: 60px">#</th>
                            <th>شماره شید</th>

                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1; @endphp
                        @foreach($product->shade_number as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    @include("line_product_station.product.product_creation.product_show.shade_number._label",["shade_number"=>$item,"id"=>$item->id])
                                    @include("line_product_station.product.product_creation.product_show.shade_number._collapse",["shade_number"=>$item,"id"=>$item->id])
                                </td>

                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </div>


    <div class="col-12">

        @include($view_path."_btn_list")



    </div>
</div>


