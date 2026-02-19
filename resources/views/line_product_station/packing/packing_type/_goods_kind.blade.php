<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>رسته های کالایی مجاز</h5>
        </div>
        <div class="card-block">

            <div class="row">
                @foreach($goods_kind_list as $item)
                    <div class="col-md-2">
                        <input type="checkbox"
                               name="data[goods_kind_ids][{{$item->id}}]" {{isset($goods_kind_packing_type[$item->id])?"checked":""}}>
                        {{$item->caption}}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>