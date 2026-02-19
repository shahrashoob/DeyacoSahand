<div class="table-responsive">
    <table class="table table-styling center">
        <tr>
            <th>عنوان مشخصه</th>
            @foreach($route->line_product_station as $item)
                <th>اولویت {{$item->priority_number}}</th>
            @endforeach
        </tr>



        <tr>
            <th>پیمانکار</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->contractor->caption??""}}</td>
            @endforeach
        </tr>


        <tr>
            <th>عملیات پیمانکار</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->contractor_operation->caption??""}}</td>
            @endforeach
        </tr>



        <tr>
            <th>کانال پیمان</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->production_channel_type->caption??""}}</td>
            @endforeach
        </tr>


        <tr>
            <th class="">ظرفیت عملی تولید

                ( {{$product->unit->caption??"***"}} در ساعت)
            </th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->practical_capacity_of_production}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">حداقل تولید
            </th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->min_of_production}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">حداکثر تولید
            </th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->max_of_production}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">کارایی</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->efficiency}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="verticalTableHeader">زمان انتظار شروع به کار
                <br/>
                (دقیقه)
            </th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->setup_time}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="verticalTableHeader">مدت زمان تحویل کالا <br>(ساعت)</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->delivery_time}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="verticalTableHeader">مدت زمان دریافت کالا <br>(ساعت)</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->receiving_time}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="verticalTableHeader">بچ تولید</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->batch??""}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="verticalTableHeader">تعداد اضافه تولید</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->extra_production}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="verticalTableHeader">درصد اضافه تولید</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->percent_of_extra_production}}</td>
            @endforeach
        </tr>
        <tr>
            <th class="verticalTableHeader">کد کالا در سامانه پیمانکار</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->product_code_in_contractor_system}}</td>
            @endforeach
        </tr>
        <tr>
            <th class="verticalTableHeader">کد خدمت در سامانه پیمانکار</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->service_code_in_contractor_system}}</td>
            @endforeach
        </tr>

        <tr>

            <th>وضعیت</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->status->caption??""}}</td>
            @endforeach
        </tr>

        <tr>

            <th></th>
            @foreach($route->line_product_station as $item)
                <td>
                    <a class="text-primary text-warning"
                       href="{{route("line_product_station.product.product_station.edit",[$product,$item,$product_creation_process])}}"><i
                            class="fa fa-edit"></i> </a>
                    <a class="text-primary text-danger" onclick="confirm('آیا از حذف خط - محصول اطمینان دارید؟')"
                       href="{{route("line_product_station.product.product_station.destroy",[$product,$item,$product_creation_process])}}"><i
                            class="fa fa-trash"></i> </a>

                </td>
            @endforeach
        </tr>


    </table>
</div>
