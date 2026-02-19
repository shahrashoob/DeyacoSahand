<div class="table-responsive">
    <table class="table table-styling center">
        <tr>
            <th>عنوان مشخصه</th>
            @foreach($route->line_product_station as $item)
                <th>اولویت {{$item->priority_number}}</th>
            @endforeach
        </tr>


        <tr>
            <th class="">خط</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->line->caption??""}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">ایستگاه</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->station->caption??""}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">گروه ماشین</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->machine_type->caption??""}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">نوع عملیات</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->station_operation->caption??""}}</td>
            @endforeach
        </tr>

        <tr>
            <th class=""> عملیات فرعی</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->station_sub_operation->caption??""}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">ظرفیت عملی تولید

                ( واحد کالا در ساعت)
            </th>
            @foreach($route->line_product_station as $item)
                <td >{{$item->practical_capacity_of_production}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">حداقل تولید
            </th>
            @foreach($route->line_product_station as $item)
                <td >{{$item->min_of_production}}</td>
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

{{--        <tr>--}}
{{--            <th class="">زمان ستاپ</th>--}}
{{--            @foreach($route->line_product_station as $item)--}}
{{--                <td>{{$item->setup_time}}</td>--}}
{{--            @endforeach--}}
{{--        </tr>--}}
        <tr>
            <th class="">مدت زمان ستاب هم کانال (دقیقه)</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->setup_time_for_co_channel}}</td>
            @endforeach
        </tr>
        <tr>
            <th class="">مدت زمان ستاپ غیر هم کانال (دقیقه)</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->setup_time_for_non_co_channel}}</td>
            @endforeach
        </tr>
        <tr>
            <th class="">مدت زمان عملیات فرعی</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->setup_time_for_sub_operation}}</td>
            @endforeach
        </tr>
        <tr>
            <th class="">مدت زمان تنظمیات پایانی</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->setup_time_for_final_setting}}</td>
            @endforeach
        </tr>

        <tr>

            <th class="">بچ تولید</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->batch??""}}</td>
            @endforeach
        </tr>
        <tr>

            <th class="">درصد خطای بچ</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->batch_error_percentage??""}}</td>
            @endforeach
        </tr>
        <tr>

            <th class="">مواد اولیه وابسته به بچ</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->material_dependent_to_batch->caption??""}}</td>
            @endforeach
        </tr>
        <tr>

            <th class="">واحد مواد اولیه وابسته به بچ</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->material_unit_type_dependent_to_batch->caption??""}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">تعداد اضافه تولید</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->extra_production}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">درصد اضافه تولید</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->percent_of_extra_production}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">نوع کانال تولید</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->production_channel_type->caption??""}}</td>
            @endforeach
        </tr>

{{--        <tr>--}}
{{--            <th class="">آیا امکان انتخاب ایستگاه بعدی <br/>(در صورت عدم تایید کنترل کیفیت) وجود دارد؟</th>--}}
{{--            @foreach($route->line_product_station as $item)--}}
{{--                <td>{{$item->is_ability_to_choose_next_station?"بله":"خیر"}}</td>--}}
{{--            @endforeach--}}
{{--        </tr>--}}

{{--        <tr>--}}
{{--            <th class="">آیا نیاز به انجام عملیات نهایی دارد؟</th>--}}
{{--            @foreach($route->line_product_station as $item)--}}
{{--                <td>{{$item->is_need_final_setting?"بله":"خیر"}}</td>--}}
{{--            @endforeach--}}
{{--        </tr>--}}



{{--        <tr>--}}
{{--            <th class="">آیا بعد از انجام عملیات <br/>نیاز به تایید کنترل کیفیت می باشد؟</th>--}}
{{--            @foreach($route->line_product_station as $item)--}}
{{--                <td>{{$item->is_need_for_quality_control?"بله":"خیر"}}</td>--}}
{{--            @endforeach--}}
{{--        </tr>--}}

        <tr>
            <th class="">آیا در ابتدا نیاز به تخصیص دارد؟</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->is_need_allocation_at_first?"بله":"خیر"}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">آیا    نیاز به شروع ستاپ (setup) دارد؟</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->is_need_start_setup?"بله":"خیر"}}</td>
            @endforeach
        </tr>
        <tr>
            <th class="">آیا نیاز به شروع عملیات دارد؟ </th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->is_need_start_of_operation?"بله":"خیر"}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">آیا نیاز به پایان عملیات دارد؟</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->is_need_end_of_operation?"بله":"خیر"}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">آیا نیاز به انجام تنظیمات نهایی دارد؟</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->is_need_final_setting?"بله":"خیر"}}</td>
            @endforeach
        </tr>

        <tr>
            <th class="">آیا بعد از انجام عملیات <br/>
                نیاز به تایید کنترل کیفیت می باشد؟</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->is_need_for_quality_control?"بله":"خیر"}}</td>
            @endforeach
        </tr>


        <tr>
            <th class="">
                آیا امکان انتخاب ایستگاه بعدی <br/>(در صورت عدم تایید کنترل کیفیت) دارد؟
            </th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->is_ability_to_choose_next_station?"بله":"خیر"}}</td>
            @endforeach
        </tr>


        <tr>
            <th class="">وضعیت</th>
            @foreach($route->line_product_station as $item)
                <td>{{$item->status->caption??""}}</td>
            @endforeach
        </tr>





    </table>
</div>
