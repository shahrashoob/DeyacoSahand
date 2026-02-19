<div class="row">
    <div class="col-sm-12">
        <a href="{{route("line_product_station.product.index")}}" class="btn btn-outline-dark">بازگشت به
            داشبورد</a>
        <a href="{{route("line_product_station.product.replace_product.index",$product)}}"
           class="btn btn-outline-dark">
            مرحله
            قبل </a>

        <button type="submit" class="btn btn-primary"> ثبت</button>
        <a href="{{route("line_product_station.product.material_flow.index",$product)}}" class="btn btn-outline-dark">مرحله
            بعد </a>
    </div>


</div>
