<div class="row">
    @if(count($product->goods_kind->classification) == 0)
        <div class="col-sm-12">

            <div class="alert alert-warning">
                برای رسته کالایی محصول، هیچ نوع طبقه بندی وجود ندارد.
            </div>

        </div>
    @else

        @foreach( $classification_product as $item)

            @include("component.input._lable", ["id"=>"classification_id",'label'=>$item->goods_kind_classification->caption, "value" =>$item->goods_kind_classification_option->caption ])

        @endforeach

    @endif
        @include($view_path."_btn_list")
</div>
