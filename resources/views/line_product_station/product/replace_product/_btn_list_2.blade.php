<div class="col-md-12">
    <button type="submit" class="btn btn-primary"> ثبت جایگزین</button>
    <a href="{{route("line_product_station.product.index")}}" class="btn btn-outline-dark">بازگشت به
        داشبورد</a>

    <a href="{{route("line_product_station.product.bom_permutation.index",$product)}}"
       class="btn btn-outline-dark">مرحله
        قبل</a>


    <a href="{{route("line_product_station.product.waste.index",$product)}}"
       class="btn btn-outline-dark ">مرحله بعد</a>

</div>
