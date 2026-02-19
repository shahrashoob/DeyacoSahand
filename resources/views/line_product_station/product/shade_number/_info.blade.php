<div class="row">
    <div class="col-md-8">
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
                                    @include("line_product_station.product.shade_number._label",["shade_number"=>$item,"id"=>$item->id])
                                    @include("line_product_station.product.shade_number._collapse",["shade_number"=>$item,"id"=>$item->id])
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

                @include("component.input._text",["id"=>"code",'label'=>"شماره  شید جدید","value"=>""])


            </div>


            <button type="submit" class="btn btn-primary"> ثبت</button>


        </form>
    </div>
    <div class="col-12">

        @include($view_path."_btn_list")



    </div>
</div>


