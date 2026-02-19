<div class="col-md-12">
    <br/>
    <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
       class="btn btn-outline-dark">بازگشت</a>

    @if($all_pricing_claculated)
    <a href="{{route("line_product_station.product.product_creation.add_tariff_rows.confirm_step",$product_creation_process)}}"
       class="btn btn-success" onclick="return confirm('آیا از صحت اطلاعات وارد شده اطمینان دارید؟')">تایید اطلاعات تعرفه ها  </a>
    @else
        <a href="#0"
           class="btn btn-outline-warning" onclick="return confirm('با توجه به اینکه قیمت به روز تمامی بسته بندی ها قابل محاسبه نمی باشد،  تایید اطلاعات امکان پذیر نیست، \n لطفا فایل قیمت گذاری را بارگذاری نمایید.')">تایید اطلاعات تعرفه ها  </a>
    @endif
</div>
