<div class="table-responsive">
    <table class="table table-styling center">
        <tr>
            <th>عنوان مشخصه</th>
            @foreach($route->line_product_station as $item)
                <th>اولویت {{$item->priority_number}}</th>
            @endforeach
        </tr>



        <tr>
            <th>مشتری</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->customer->caption??""}}</td>
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
            <th class="verticalTableHeader">کد کالا در سامانه مشتری</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->product_code_in_contractor_system}}</td>
            @endforeach
        </tr>
{{--        <tr>--}}
{{--            <th class="verticalTableHeader">کد خدمت در سامانه مشتری</th>--}}
{{--            @foreach($route->line_product_station as $item)--}}
{{--                <td>{{$item->service_code_in_contractor_system}}</td>--}}
{{--            @endforeach--}}
{{--        </tr>--}}

        <tr>

            <th>انبار تحویل کالا</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->applicant_warehouse->caption??""}}</td>
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
                    <a class="text-primary text-danger" onclick="confirm('آیا از حذف پیمانکار اطمینان دارید؟')"
                       href="{{route("line_product_station.product.product_station.destroy",[$product,$item,$product_creation_process])}}"><i
                                class="fa fa-trash"></i> </a>

                </td>
            @endforeach
        </tr>


    </table>
</div>
