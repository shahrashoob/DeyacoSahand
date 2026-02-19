<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                    <h5> افزودن ردیف تعرفه برای{{$product->fullCaption()}} </h5>
            </div>
            <div class="card-block">

                <form id="form1" action="{{route($route_path."store_product_tariff",[$product,$product_creation_process])}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate">
                    @csrf

                    @include("line_product_station.product.product_creation.add_tariff_rows._tariff_rows")

                    @include($view_path."_btn_back")

                    <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>

                </form>

            </div>
        </div>
    </div>

</div>
