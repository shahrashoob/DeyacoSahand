<div class="row">


    @include("component.input._lable",["id"=>"possibility_of_sale",'label'=>"آیا محصول امکان فروش دارد؟","value"=>$product->possibility_of_sale?"بله":"خیر"])

    @if($product->possibility_of_sale)
        @foreach($type_of_sale_product_list as $item)
           @if(in_array($item->id,$type_of_sale_product_list_for_product))
            @include("component.input._lable",["id"=>"type_of_sale_id",'label'=>"نوع فروش","value"=>$item->caption])
            @if($item->id == 2)
                @if(isset($type_of_sale_product_product) && isset($type_of_sale_product_product->service))
                @include("component.input._lable",["id"=>"service_id_2",'label'=>"نام و کد خدمت متناظر در سامانه","value"=>$type_of_sale_product_product->service->caption."-".$type_of_sale_product_product->service->code])
                @endif
            @endif
            @endif
        @endforeach
    @endif
</div>
        @include($view_path."_btn_list")
{{--        <div class="table-responsive">--}}
{{--            <table class="table table-styling">--}}
{{--                <tr>--}}
{{--                    <td>نوع فروش</td>--}}
{{--                    @if($item->id == 2)--}}
{{--                        <td></td>--}}
{{--                    @endif--}}
{{--                </tr>--}}
{{--                @foreach($type_of_sale_product_list as $item)--}}
{{--                    <tr>--}}
{{--                        <td>{{$item->caption}}</td>--}}
{{--                        @if($item->id == 2)--}}
{{--                            <td>{{$type_of_sale_product_product->service_id}}</td>--}}
{{--                        @endif--}}
{{--                    </tr>--}}

{{--                @endforeach--}}
{{--            </table>--}}
{{--        </div>--}}


