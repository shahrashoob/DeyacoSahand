
@php
    $default_setting=[
      "copy_sale"=>1,
      "copy_sale_break"=>1,

        "copy_classification"=>1,
        "copy_classification_break"=>1,

        "copy_product_property_value"=>1,
        "copy_product_property_value_break"=>1,

        "copy_consumed"=>1,
        "copy_consumed_break"=>1,

        "copy_route"=>1,
        "copy_route_break"=>1,

        "copy_route_property"=>1,
        "copy_route_property_break"=>1,

        "copy_bom"=>1,
        "copy_bom_break"=>1,

        "copy_material_flow"=>1,
        "copy_material_flow_break"=>1,

        "copy_product_replace"=>1,
        "copy_product_replace_break"=>1,

        "copy_bom_permutation_break"=>1,

        "copy_packing_type"=>1,
        "copy_packing_type_break"=>1,

        "copy_lot_number"=>1,
        "copy_lot_number_break"=>1,

        "copy_warehouse"=>1,
        "copy_warehouse_break"=>1,


    ];
@endphp


@if(!isset($only_hidden) || !$only_hidden)
    <br/>
    <div class="w-100"></div>
    <br/>
    <table>

        <tr>

            <td>
                @include("component.input._radio_box01",["id"=>"copy_sale",'label'=>"آیا مشخصات فروش کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_sale_break",'label'=>"نیاز به ویرایش اطلاعات فروش می باشد؟ ","value"=>1])

            </td>
        </tr>
        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_classification",'label'=>"آیا اطلاعات طبقه بندی کالا کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_classification_break",'label'=>"نیاز به ویرایش اطلاعات طبقه بندی کالا می باشد؟ ","value"=>1])
            </td>
        </tr>
        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_product_property_value",'label'=>"آیا اطلاعات مشخصات کالا کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_product_property_value_break",'label'=>"نیاز به ویرایش اطلاعات مشخصات کالا می باشد؟","value"=>1])
            </td>
        </tr>
        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_consumed",'label'=>"آیا اطلاعات کالای مصرفی کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_consumed_break",'label'=>"نیاز به ویرایش اطلاعات کالای مصرفی می باشد؟","value"=>1])
            </td>
        </tr>
        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_route",'label'=>"آیا اطلاعات مسیر محصول کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_route_break",'label'=>"نیاز به ویرایش مسیر محصول می باشد؟","value"=>1])
            </td>
        </tr>
        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_route_property",'label'=>"آیا مشخصات مسیر محصول کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_route_property_break",'label'=>"نیاز به ویرایش مشخصات مسیر محصول می باشد؟","value"=>1])
            </td>
        </tr>
        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_bom",'label'=>"آیا اطلاعات BOM کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_bom_break",'label'=>"نیاز به ویرایش اطلاعات BOM می باشد؟","value"=>1])
            </td>
        </tr>
        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_material_flow",'label'=>"آیا گراف جریان همبافتی کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_material_flow_break",'label'=>"نیاز به طراحی گراف جریان همبافتی می باشد؟","value"=>1])
            </td>
        </tr>
        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_product_replace",'label'=>"آیا اطلاعات کالای جایگزین مصرف کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_product_replace_break",'label'=>"نیاز به ویرایش اطلاعات کالای جایگزین مصرف می باشد؟","value"=>1])
            </td>
        </tr>


        <tr>
            <td>
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_bom_permutation_break",'label'=>"نیاز به ویرایش اطلاعات جایگزین تولید می باشد؟","value"=>1])
            </td>
        </tr>
        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_packing_type",'label'=>"آیا اطلاعات بسته بندی کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_packing_type_break",'label'=>"نیاز به ویرایش اطلاعات بسته بندی می باشد؟","value"=>1])
            </td>
        </tr>


        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_waste",'label'=>"آیا اطلاعات ضایعات  کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_waste_break",'label'=>"نیاز به ویرایش اطلاعات ضایعات می باشد؟","value"=>1])
            </td>
        </tr>

        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_lot_number",'label'=>"آیا اطلاعات لات کالا کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_lot_number_break",'label'=>"نیاز به ویرایش اطلاعات لات کالا می باشد؟","value"=>1])
            </td>
        </tr>

        <tr>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_warehouse",'label'=>"آیا اطلاعات انبارش کالا کپی شود؟","value"=>1])
            </td>
            <td>
                @include("component.input._radio_box01",["id"=>"copy_warehouse_break",'label'=>"نیاز به ویرایش اطلاعات انبارش کالا می باشد؟","value"=>1])
            </td>
        </tr>
    </table>

@else
    @include("component.input._hidden",["id"=>"copy_sale",'label'=>"آیا مشخصات فروش کپی شود؟","value"=>1])
    @include("component.input._hidden",["id"=>"copy_classification",'label'=>"آیا اطلاعات طبقه بندی کالا کپی شود؟","value"=>1])
    @include("component.input._hidden",["id"=>"copy_product_property_value",'label'=>"آیا اطلاعات مشخصات کالا کپی شود؟","value"=>1])
    @include("component.input._hidden",["id"=>"copy_consumed",'label'=>"آیا اطلاعات کالای مصرفی کپی شود؟","value"=>1])
    @include("component.input._hidden",["id"=>"copy_route",'label'=>"آیا اطلاعات مسیر محصول کپی شود؟","value"=>1])
    @include("component.input._hidden",["id"=>"copy_route_property",'label'=>"آیا مشخصات مسیر محصول کپی شود؟","value"=>1])
    @include("component.input._hidden",["id"=>"copy_bom",'label'=>"آیا اطلاعات BOM کپی شود؟","value"=>1])
    @include("component.input._hidden",["id"=>"copy_material_flow",'label'=>"آیا گراف جریان همبافتی کپی شود؟","value"=>1])
    @include("component.input._hidden",["id"=>"copy_product_replace",'label'=>"آیا اطلاعات کالای جایگزین مصرف کپی شود؟","value"=>1])
    @include("component.input._hidden",["id"=>"copy_packing_type",'label'=>"آیا اطلاعات بسته بندی کپی شود؟","value"=>1])

@endif