@include("component.input._number",["id"=>"amount",'label'=>"مقدار کارت تولید ","value"=>""])

@include("component.input.datepicker._datepicker",["id"=>"max_delivery_datetime","lable"=>"حداکثر تاریخ تحویل  ","value"=>$max_delivery_datetime1??null])

@switch(count($packing_type_list))
    @case (0)
        <div class="col-md-8">
            <div class=" alert alert-danger">
                با توجه به نوع کارت تولید (تولیدی/نمونه گیری) و روش برنامه ریزی تولید هیچ نوع
                بسته بندی پیشنهادی برای کالا وجود ندارد.
            </div>
        </div>
        <div class="w-100"></div>
        @break
    @case(1)

        @include("component.input._lable",["id"=>"","lable"=>"بسته بندی مجاز ","value"=>$packing_type_list[0]->packing_type->caption])
        @include("component.input._hidden",["id"=>"packing_type_id","value"=>$packing_type_list[0]->packing_type->id])

        @break
    @default
        <div class="col-md-12">
            <table>
                <tr>

                    <td>بسته بندی های مجاز:</td>
                    <td></td>
                </tr>
                @foreach($packing_type_list as $item)
                    <tr>
                        <td>


                        </td>
                        <td>
                            <input type="radio" value="{{$item->packing_type->id}}"
                                   name="packing_type_id">
                            {{$item->packing_type->caption}}
                        </td>
                    </tr>
                @endforeach
            </table>
            <br/>
            <br/>
        </div>
        </div>
@endswitch