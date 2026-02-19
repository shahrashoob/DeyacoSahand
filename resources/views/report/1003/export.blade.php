<html>

<table style=" ">
    <thead>
    <tr>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد کالا</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام کالا</th>

        @if($breaking_by_classification)
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">طبقه بندی</td>
        @endif

        @if($breaking_by_degree)
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام درجه</td>
        @endif
        @if($breaking_by_lot_number)
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">شماره همبافت (لات)</td>
        @endif

        @if($breaking_by_packing_item)
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">نوع بسته بندی</td>
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">شماره فرم بسته بندی</td>
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">نوع و کد حامل</td>
        @endif
        @if($breaking_by_transaction)
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">تاریخ تراکنش</td>
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf"> مرکز هزینه (IC)</td>
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf"> نام هزینه (IC)</td>
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">نوع رخداد</td>
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf"> فرم درخواست کالا از انبار</td>
            <td style="text-align:center; border:2px solid #000000;background: #bfbfbf"> برگ خروج / فرم انبار</td>
        @endif

        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">انبار</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">واحد اصلی</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">تعداد در واحد اصلی</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مجموع ورودی (واحد اصلی)</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مجموع خروجی (واحد اصلی)</th>
        @if(!$breaking_by_transaction)
            <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مانده (واحد اصلی)</th>
        @endif

        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">واحد فرعی</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مجموع ورودی (واحد فرعی)</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مجموع خروجی (واحد فرعی)</th>
        @if(!$breaking_by_transaction)
            <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مانده (واحد فرعی)</th>
        @endif

    </tr>

    </thead>
    <tbody>
    @foreach($values as $item)
        <tr>

            <td style="text-align:center; border:2px solid #000000;">{{isset($item["product"])?$item["product"]->code:"***"}}</td>


            <td style="text-align:center; border:2px solid #000000;">{{isset($item["product"])?$item["product"]->caption:"***"}}</td>
            @if($breaking_by_classification)
                <td style="text-align:center; border:2px solid #000000;"> {{$item["classification_caption"]??""}}</td>
            @endif

            @if($breaking_by_degree)
                <td style="text-align:center; border:2px solid #000000;"> {{$item["degree"]->caption??""}}</td>
            @endif
            @if($breaking_by_degree)
                <td style="text-align:center; border:2px solid #000000;"> {{$item["degree"]->caption??""}}</td>
            @endif
            @if($breaking_by_lot_number)
                <td style="text-align:center; border:2px solid #000000;">{{$item["lot_number"]->code??""}}</td>
            @endif
            @if($breaking_by_packing_item)
                <td style="text-align:center; border:2px solid #000000;">{{isset($item["packing_type"])?($item["packing_type"]->caption??""):""}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{isset($item["packing_form_item"])?($item["packing_form_item"]->packing_form->code??""):""}} </td>
                <td style="text-align:center; border:2px solid #000000;">{{isset($item["carrier"])?$item["carrier"]->getCaption():""}}</td>
            @endif

            @if($breaking_by_transaction)
                <td style="text-align:center; border:2px solid #000000;">{{$item["created_at"]}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item["ic"]}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{isset($cost_center_list[$item["ic"]])?$cost_center_list[$item["ic"]]:"***"}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item["trans_kind"]}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item["product_request_form_code"]}}</td>
                <td style="text-align:center; border:2px solid #000000;">{{$item["form_code"]}}</td>
            @endif

            <td style="text-align:center; border:2px solid #000000;">{{$item["warehouse"]->caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item["product"]->unit->bach_caption}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{$item["product"]->number_in_carton}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{isset($item["input"])?$item["input"]:0}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{isset($item["output"])?$item["output"]:0}}</td>
            @if(!$breaking_by_transaction)
                <td style="text-align:center; border:2px solid #000000;">{{isset($item["inventory"])?round($item["inventory"],7):0}}</td>
            @endif

            <td style="text-align:center; border:2px solid #000000;">{{$item["product"]->sub_unit->caption??""}}</td>

            <td style="text-align:center; border:2px solid #000000;">{{isset($item["sub_input"])?$item["sub_input"]:0}}</td>
            <td style="text-align:center; border:2px solid #000000;">{{isset($item["sub_output"])?$item["sub_output"]:0}}</td>

            @if(!$breaking_by_transaction)
                <td style="text-align:center; border:2px solid #000000;">{{isset($item["sub_inventory"])?round($item["sub_inventory"],7):0}}</td>
            @endif
        </tr>
    @endforeach
    </tbody>

</table>
</html>
