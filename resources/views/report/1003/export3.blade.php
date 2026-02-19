<html>

<table style=" ">
    <thead>
    <tr>
        <th  style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد کالا</th>

        @if($breaking_by_degree)
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد درجه</td>
        @endif

        <th  style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام کالا</th>


            <td  style="text-align:center; border:2px solid #000000;background: #bfbfbf">شماره همبافت (لات)</td>


            <td   style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام درجه</td>


            <td   style="text-align:center; border:2px solid #000000;background: #bfbfbf">نوع بسته بندی</td>
            <td   style="text-align:center; border:2px solid #000000;background: #bfbfbf">شماره فرم بسته بندی</td>
            <td   style="text-align:center; border:2px solid #000000;background: #bfbfbf">نوع و کد حامل</td>


        <th   style="text-align:center; border:2px solid #000000;background: #bfbfbf">انبار</th>
        <th   style="text-align:center; border:2px solid #000000;background: #bfbfbf">واحد اصلی</th>
        <th   style="text-align:center; border:2px solid #000000;background: #bfbfbf">تعداد در واحد اصلی</th>
        <th   style="text-align:center; border:2px solid #000000;background: #bfbfbf">مجموع ورودی (واحد اصلی)</th>
        <th   style="text-align:center; border:2px solid #000000;background: #bfbfbf">مجموع خروجی (واحد اصلی)</th>
        <th   style="text-align:center; border:2px solid #000000;background: #bfbfbf">مانده (واحد اصلی)</th>
        <th   style="text-align:center; border:2px solid #000000;background: #bfbfbf">واحد فرعی</th>
        {{--        <th>مجموع ورودی (واحد فرعی)</th>--}}
        {{--        <th>مجموع خروجی (واحد فرعی)</th>--}}
        {{--        <th>مانده  (واحد فرعی)</th>--}}
    </tr>

    </thead>
    <tbody>
    @foreach($values as $item)
        <tr>

            <td style="text-align:center; border:2px solid #000000;">{{$item->product->code??""}}</td>

            @if($breaking_by_degree)
                <td style="text-align:center; border:2px solid #000000;"> {{$item->degree->code??""}}</td>
            @endif

            <td style="text-align:center; border:2px solid #000000;">{{$item->product->caption??""}}</td>


                <td style="text-align:center; border:2px solid #000000;">{{$item->lot_number->code??""}}</td>


                <td style="text-align:center; border:2px solid #000000;"> {{$item->degree->caption??""}}</td>


                <td style="text-align:center; border:2px solid #000000;">{{$item->packing_type->caption??""}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item->packing_form_item->code??""}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item->carrier->caption??""}}</td>


            <td style="text-align:center; border:2px solid #000000;">{{$item->warehouse->caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->product->unit->bach_caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->product->number_in_carton??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"></td>
            <td style="text-align:center; border:2px solid #000000;"></td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->value}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item->product->sub_unit->caption??""}}</td>
            <td>{{$item->packing_form_item_id}}</td>
            {{--            <td style=text-align:center></td>--}}
            {{--            <td style=text-align:center></td>--}}
            {{--            <td style=text-align:center></td>--}}


        </tr>
    @endforeach
    </tbody>

</table>
</html>
