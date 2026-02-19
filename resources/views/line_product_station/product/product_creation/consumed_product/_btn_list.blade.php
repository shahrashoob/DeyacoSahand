
<div class="col-md-12">
    <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
       class="btn btn-outline-dark">بازگشت</a>

    <a href="{{route("line_product_station.product.product_creation.consumed_product.confirm_step",$product_creation_process)}}"
       class="btn btn-success" onclick="return confirm('آیا از صحت اطلاعات وارد شده اطمینان دارید؟')">تایید اطلاعات کالاهای مصرفی  </a>
    <a href="{{route("line_product_station.product.product_creation.new_form.index",$product_creation_process->id)}}"
       class="btn btn-primary">ثبت درخواست طراحی کالای جدید</a>

</div>
