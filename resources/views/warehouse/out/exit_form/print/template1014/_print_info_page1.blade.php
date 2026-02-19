<div class="content" style="font-size: 12px">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="width: 100%">
        <tr>
            <td colspan="3">
                <br/>
                <br/>
                برگ خروج تصویری کالا از انبار
                -
                {{$form->code}}
                <br/>
                <br/>
                <br/>
                <br/>
            </td>
        </tr>

        {{--        <tr>--}}
        {{--            <th>ردیف</th>--}}
        {{--            <th>--}}
        {{--                کد کالا--}}
        {{--            </th>--}}
        {{--            <th style="max-width: 35%">--}}
        {{--                نام کالا--}}
        {{--            </th>--}}

        {{--        </tr>--}}

        {{--        به دست آوردن لیست به تفکیک کالا ها--}}
        @php
            $row=-1;
            $form=\App\Models\Form\Form::find($form->id);
        @endphp

        <tr>
            @foreach($form->itemOrderByTransportCode("group_by_product") as $item)

@php $row++;@endphp
                <td>


                    {{$item->packing_form_item->product->caption??""}}
                    <br/>
                    {{$item->packing_form_item->product->code??""}}
                    <br/>
                    @if($item->packing_form_item->product->image)
                        <img style="margin:10px; height: 300px "
                             src="{{public_path('../storage/app/upload/product/'.($item->packing_form_item->product->image->filename??''))}}">
                    @endif

                </td>

                @if($row % 3 ==2)
        </tr>
        <tr>
            @endif

            @endforeach

        </tr>


    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 10px">
    سازمان دیجیتال دیاکو
</div>
