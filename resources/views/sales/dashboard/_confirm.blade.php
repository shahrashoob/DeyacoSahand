@php $processed_type=$order->get_processed_type();
 $check_permission_sales_status_id= $post_user->checkButtonPermission("sales.".$order->status_id);
@endphp

@if(in_array($processed_type, [100,101,200]) && $check_permission_sales_status_id && in_array($order->status_id,[304010,304020,304030,304040,304050,304060,304070,304075,304080]))
    <div class="">

        @switch($processed_type)

            @case(100)
                <a href="{{route("sales.dashboard.change_processed_by_script",$order)}}" class="text-info" onclick="return confirm('آیا اطمینان دارید که نیاز نیست کارشناس دیجیتال دیاکو پردازش را انجام دهد؟')">

                        <i class="fa fa-check"></i>

                    پردازش سفارش توسط کارشناس دیجیتال دیاکو</a>
            @break
            @case(200)
                <a href="{{route("sales.dashboard.change_processed_by_script",$order)}}" class="text-danger" onclick="return confirm('آیا اطمینان دارید که  کارشناس دیجیتال دیاکو پردازش را انجام دهد؟')">

                    <i class="fa fa-times"></i>
                    عدم پردازش سفارش توسط کارشناس دیجیتال دیاکو</a>
            @break
            @case(101)
                <a href="{{route("sales.dashboard.change_processed_by_script",$order)}}" class="text-warning" onclick="return confirm('در صورتی که خطای پردازش گزارش شده توسط کارشناس دیجیتال دیاکو را برطرف کرده ایت،\n بر روی دکمه تایید کلیک نمایید.')">

                    <i class="fa fa-times"></i>
                       پردازش سفارش توسط کارشناس دیجیتال دیاکو با خطا مواجه شده (تلاش مجدد)</a>
            @break


        @endswitch


        <br/>
        <br/>
    </div>
@endif

<div class="text-center">

    @if(isset($back_url))
        <a href="{{$back_url}}" class="btn btn-outline-dark">
            <i class="fa fa-arrow-right"></i> بازگشت
        </a>
    @else
        <a href="{{url()->previous()}}" class="btn btn-outline-dark" type="button">
            <i class="fa fa-arrow-right"></i> بازگشت
        </a>
    @endif
    @if($order->status_id == 304080 && $post_user->checkButtonPermission("sales.will_be_processed_later") &&  $processed_type!= 100 )
        <a class="btn btn-primary" href="{{route("sales.dashboard.will_be_processed_later",$order)}}"
           onclick="return confirm('آیا اطمینان دارید که سفارش را پس از آماده سازی، پردازش می کنید؟')">
            <i class=" fas fa-check-circle"></i> انجام پردازش پس از آماده سازی
        </a>
    @endif

    @if($processed_type ==300 && $post_user->checkButtonPermission("sales.304080") )
        <a class="btn btn-primary" href="{{route("sales.dashboard.register_processed_later",$order)}}"
           onclick="return confirm('آیا از تایید پردازش پس از آماده سازی اطمینان دارید؟')">
            <i class=" fas fa-check-circle"></i> تایید پردازش (پس از آماده سازی)
        </a>
    @endif

    @if(!in_array($order->status_id, [35060,35070]))

        @if($check_permission_sales_status_id && in_array($order->status_id,[304010,304020,304030,304040,304050,304060,304070,304075,304080]))
            @if($order->status_id == 304030 && $order->register_user_id == \Auth::user()->id && $order->customer->user_id == $order->register_user_id )
                <button id="btn_confirm" class="btn btn-success md-trigger md-setperspective" data-modal="modal-17"
                        type="button">
                    <i class="fa fa-check"></i> تایید درخواست
                </button>
            @elseif($order->status_id != 304030)


                @if($order->status_id == 304080 )
                    @if( $processed_type!= 100)
                    <button id="btn_confirm" class="btn btn-success md-trigger md-setperspective" data-modal="modal-17"
                            type="button">
                        <i class="fa fa-check"></i> تایید پردازش
                    </button>
                        @endif
                @else
                    <button id="btn_confirm" class="btn btn-success md-trigger md-setperspective" data-modal="modal-17"
                            type="button">
                        <i class="fa fa-check"></i> تایید درخواست
                    </button>
                @endif

            @elseif($order->status_id == 304030 && $post_user->checkButtonPermission("sales.304030") )
                <a class="btn btn-success" href="{{route("sales.dashboard.receipt_from_customer",$order)}}">
                    <i class="fa fa-check"></i> تایید از جانب مشتری
                </a>
            @endif
        @endif

        @if($post_user->checkButtonPermission("sales.35060") &&  in_array($order->status_id,[304010,304020,304030,304040,304050,304060,304070,304075,304080]))
            <button class="btn btn-danger  md-trigger md-setperspective" data-modal="modal-18" href="#!"><i
                        class="fa fa-times"></i> کنسل کردن درخواست
            </button>
        @endif


        @if($post_user->checkButtonPermission("sales.pre_statue") &&  in_array($order->status_id,[304020,304030,304040,304050,304060,304070,304075,304080]))
            <button class="btn btn-primary md-trigger md-setperspective" data-modal="modal-19" href="#!"><i
                        class="fa fa-arrow-right"></i> ارجاع به مرحله قبل
            </button>
        @endif

        @if($post_user->checkButtonPermission("sales.go_to_customer") &&  in_array($order->status_id,[304020,304030,304040,304050,304060,304070,304075,304080]))
            <button class="btn btn-primary md-trigger md-setperspective" data-modal="modal-15" href="#!"><i
                        class="fa fa-user-tag"></i> ارجاع به کارتابل مشتری
            </button>
        @endif

        @if($post_user->checkButtonPermission("sales.go_to_304020") && in_array($order->status_id,[304020,304030,304040,304050,304060,304070,304075,304080]))
            <button class="btn btn-primary md-trigger md-setperspective" data-modal="modal-16" href="#!"><i
                        class="fa fa-user-tag"></i> ارجاع به کارتابل کارشناس فروش
            </button>
        @endif

        @if($post_user->checkButtonPermission("sales.edit_order") && in_array($order->status_id,[304010,304020,304030]))
            <a class="btn btn-primary " href="{{route("customer_group.buy.edit_order",$order)}}"><i
                        class="fa fa-edit"></i> ویرایش درخواست
            </a>
        @endif

        @if($post_user->checkButtonPermission("sales.exit_permission") && $order->exit_datetime==null && $order->status_id != 304010)
            <button class="btn btn-primary md-trigger md-setperspective" data-modal="modal-13" href="#!">
                <i style="transform: rotate(180deg)" class="fa fa-sign-out-alt"></i> ثبت مجوز بارگیری
            </button>
        @endif

        @if($post_user->checkButtonPermission("sales.special_off") && in_array($order->status_id,[304020,304030,304040,304050,304060,304070,304075]))
            <button class="btn btn-primary md-trigger md-setperspective" data-modal="modal-10" href="#!"><i
                        class="fa fa-star"></i> ثبت تخفیف خاص
            </button>
        @endif


        @if($post_user->checkButtonPermission("sales.terminate_order") &&  in_array($order->status_id,[35030,35040]))
            <button class="btn btn-danger  md-trigger md-setperspective" data-modal="modal-14" href="#!"><i
                        class="fa fa-times"></i> خاتمه یافته کردن
            </button>
        @endif

        {{--        @if($post_user->checkButtonPermission("sales.order_to_collection") && $order->status_id != 304010)--}}
        {{--            <a href="{{route("sales.loading.dashboard.create_order_to_collection",$order)}}" class="btn btn-primary "--}}
        {{--               id="">--}}
        {{--                <i class="fa fa-list"></i> صدور دستور جمع آوری--}}
        {{--            </a>--}}
        {{--        @endif--}}

    @endif
    <a href="{{route("sales.print.factor",$order)}}" class="btn btn-primary " id="btn_other"
    > <i class="fa fa-print"></i> پرینت پیش فاکتور
    </a>


</div>


