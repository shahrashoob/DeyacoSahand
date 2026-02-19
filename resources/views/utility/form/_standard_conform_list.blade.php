
<table class="table table-styling center" style=" width: 300px; margin: auto">
    <tr>
        <td colspan="4">
            <div class="alert alert-info">
                لطفا شماره بسته بندی یا کد حامل هر بسته بندی را در کادر(های) زیر وارد نمایید،
                ترتیب ورود اهمیتی ندارد

            </div>
        </td>
    </tr>
    <tr>
        <th>ردیف</th>
        <th> شماره بسته بندی
        </th>
        <th></th>
        <th>
            شماره حامل
        </th>
    </tr>
    @php $row=0;@endphp
    @foreach($packing_form as $key=>$form_item)
        <tr>
            <td>بسته {{++$row}}</td>
            <td>
                <input tabindex="{{$row}}" type="number" id="{{"packing_".$key}}" name="{{"packing_".$key}}"
                       style="width: 120px" @if(isset($allow_show_packing_form_code) && $allow_show_packing_form_code) value="{{$key+1000}}" @endif>
                /DCPK
            </td>
            <td>
                یا
            </td>
            <td>
                <input tabindex="{{10000+$row}}" type="number" id="{{"carrier_".$key}}" name="{{"carrier_".$key}}"
                       style="width: 120px">

            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="4">
            <a href="{{$back_route}}"
               class="btn btn-outline-dark">بازگشت</a>


            <button type="submit" class="btn btn-primary">ثبت</button>

        </td>
    </tr>
    <tr>
        <td colspan="4">لیست آیتم های داخل بسته بندی (ها):</td>
    </tr>
    @foreach( $form->item as $item)
        <tr>
            <td colspan="3">
                {{$item->product->code}} -  {{$item->product->caption}}
            </td>
            <td >
                {{$item->amount}} {{$item->product->unit->caption}}
            </td>
        </tr>

    @endforeach
</table>



