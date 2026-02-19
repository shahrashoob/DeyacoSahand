<div class="table-responsive">
    <table class="table table-styling center">
        <tr>
            <th>عنوان مشخصه</th>
            @foreach($route->line_product_station as $item)
                <th>اولویت {{$item->priority_number}}</th>
            @endforeach
        </tr>



        <tr>
            <th>تامین کننده</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->supplier->caption??""}}</td>
            @endforeach
        </tr>



        <tr>
            <th class="">ظرفیت خرید (واحد کالا در یک ماه)
            </th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->purchasing_capacity}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">حداقل خرید
            </th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->min_of_production}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">حداکثر خرید
            </th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->max_of_production}}</td>
            @endforeach
        </tr>



        <tr>
            <th class="verticalTableHeader">مدت زمان دریافت کالا <br>(ساعت)</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->receiving_time}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="verticalTableHeader">بچ خرید</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->batch??""}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="verticalTableHeader">تعداد اضافه خرید</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->extra_production}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="verticalTableHeader">درصد اضافه خرید</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->percent_of_extra_production}}</td>
            @endforeach
        </tr>
        <tr>
            <th class="verticalTableHeader">کد کالا در سامانه تامین کننده</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->product_code_in_supplier_system}}</td>
            @endforeach
        </tr>
        <tr>
            <th class="verticalTableHeader">نام کالا در سامانه تامین کننده</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->product_caption_in_supplier_system}}</td>
            @endforeach
        </tr>

        <tr>

            <th>وضعیت</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->status->caption??""}}</td>
            @endforeach
        </tr>




    </table>
</div>
