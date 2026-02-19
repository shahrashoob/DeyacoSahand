<div class="col-md-12">
    <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
       class="btn btn-outline-dark">بازگشت</a>

    <button type="submit"
       class="btn btn-primary">افزودن جدید</button>

    <a href="{{route("line_product_station.product.product_creation.replace_product.confirm_step",$product_creation_process)}}"
       class="btn btn-success" onclick="return confirm('آیا از صحت اطلاعات وارد شده اطمینان دارید؟')">تایید اطلاعات کالای جایگزین مصرف  </a>

</div>
