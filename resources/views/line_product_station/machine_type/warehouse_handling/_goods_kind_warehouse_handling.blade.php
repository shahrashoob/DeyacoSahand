<div class="col-sm-6">
    <div class="card">
        <div class="card-header">
            <h5> تنظیمات محدودیت انبارگردانی (روز) </h5>
        </div>
        <div class="card-block">
            <div class="alert alert-info">
                لطفا به ازای هر رسته کالایی، محدودیت زمانی انبارگردانی در انبارک را مشخص نمایید.
            </div>
            <form id="form2"
                  action="{{$url}}"
                  method="post"
                  autocomplete="off"
                  novalidate="novalidate">
                @csrf
                <div class="row">
                    @foreach($goods_kind_list as $item)

                        @include("component.input._text",[
                        "id"=>"warehouse_limit[".$item->goods_kind_id."]",
                        "label"=>$item->goods_kind->caption,
                        "value"=>isset($warehouse_handling_goods_kind_time_limit[$item->goods_kind_id])?$warehouse_handling_goods_kind_time_limit[$item->goods_kind_id]:""])

                    @endforeach

                </div>

                <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

            </form>

        </div>
    </div>
</div>