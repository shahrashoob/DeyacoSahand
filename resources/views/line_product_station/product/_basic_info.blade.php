<div class="collapse {{isset($show_collapse)?"show":""}} col-md-12" id="{{$id??"collapseExample"}}">


    <div class="alert " style="border: 1px solid #0b0b0b; border-radius: 10px;overflow: auto">

        <div class="row">

            <table class="table table-styling center">
                <tr>
                    <td>عنوان</td>
                    <td>مقدار</td>
                </tr>


                    <tr>
                        <td>واحد اصلی</td>
                        <td style="font-weight: bold">{{$product->unit->caption??""}}</td>
                    </tr>
                @if($product->sub_unit)
                    <tr>
                        <td>واحد فرعی</td>
                        <td style="font-weight: bold">{{$product->sub_unit->caption??""}}</td>
                    </tr>
                @endif
                @if($product->sub_unit2)
                    <tr>
                        <td>واحد فرعی 2</td>
                        <td style="font-weight: bold">{{$product->sub_unit2->caption??""}}</td>
                    </tr>
                @endif
                @if($product->frame_ratio_unit2)
                    <tr>
                        <td>نسبت واحد فرعی 2 به واحد اصلی</td>
                        <td style="font-weight: bold">{{$product->frame_ratio_unit2??""}}</td>
                    </tr>
                @endif

                    <tr>
                        <td>وزن  کالا</td>
                        <td style="font-weight: bold">{{$product->weight??""}} کیلوگرم </td>
                    </tr>


            </table>
        </div>
    </div>

</div>