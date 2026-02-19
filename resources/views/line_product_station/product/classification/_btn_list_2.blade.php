<a href="{{route("line_product_station.product.index")}}" class="btn btn-outline-dark">بازگشت به
    داشبورد</a>

<a href="{{route("line_product_station.product.warehouse.index",$product)}}"
   class="btn btn-outline-dark">مرحله قبل</a>

@include("component.input._hidden",["id"=>"valid_property","value"=>""])
<button type="submit" class="btn btn-primary"> ذخیره و ادامه</button>
