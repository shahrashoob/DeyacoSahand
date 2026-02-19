<div class="row">
    @if(count($product->goods_kind->classification) == 0)
        <div class="col-sm-12">
            <form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}" method="post"
                  autocomplete="off"
                  novalidate="novalidate">
                @csrf
            <div class="alert alert-warning">
                برای رسته کالایی محصول، هیچ نوع طبقه بندی وجود ندارد.
            </div>
            @include($view_path."_btn_list_1")
            </form>
        </div>
    @else
        <div class="col-sm-12">
            <form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}" method="post"
                  autocomplete="off"
                  novalidate="novalidate">
                @csrf
                <div class="row">
                    @foreach( $product->goods_kind->classification as $item)


                        @include(
                         "component.input._aotocomplet2",
                         ["id"=>"classification_".$item->id,
                         'label'=>$item->caption,
                         "class_col"=>"col-md-6 property",
                         "option"=>$classification_option_list[$item->id]["items"],
                         "val"=>$classification_option_list[$item->id]["value"],
                         "text"=>$classification_option_list[$item->id]["text"],
                     ])

                        <div class="w-100"></div>

                    @endforeach
                </div>

                @include($view_path."_btn_list_2")
            </form>
        </div>
    @endif
</div>
