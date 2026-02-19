<div class="table-responsive">
    <table class="table table-styling center">
        <thead>
        <tr>
            <th>#</th>
            <th>کد فرم</th>
            <th>تاریخ ایجاد</th>
            <th>کاربر ایجاد کننده فرم</th>
            <th>درخواست دهنده</th>
            <th>شماره مرجع</th>
            <th>مجوز بارگیری</th>
            <th>انبار</th>
            <th>تعداد بسته بندی<br/> حمل و نقل</th>
            <th> وضعیت</th>

            <th></th>
            <th></th>
            <th></th>
        </tr>

        </thead>
        <tbody>
        @php $row=$firstItem;@endphp
        @foreach($list as $item)

            <tr {{$item->active_status_id ==7005102?"style=background:#1e3953":""}}>
                <td>{{$row++}}</td>
                <td>
                    <a href="{{route("wh.out.dashboard.view",$item)}}/{{$currentPage}}">{{$item->getCode()}}</a>
                </td>
                <td>{{$item->get_create_date_and_time()}}</td>
                <td>{{$item->worker->fullname()}}</td>
                <td>{{$item->applicant->fullCaption()}}  </td>
                <td>
                    @if($item->applicant_type_id == 30)
                        <a href="{{route("wh.out.dashboard.view_order",$item)}}">{{$item->getReferenceNumber()}}</a>
                    @else
                        {{$item->getReferenceNumber()}}
                    @endif
                </td>
                <td>
                    {{$item->order && $item->applicant_type_id == 30?$item->order->loading_date():""}}
                </td>
                <td>{{$item->warehouse->caption??""}}</td>
                <td>{{$item->transport_items()->count()}}</td>
                <td>{{$item->getStatus()}}</td>
                <td>
                    <button class="text-primary"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: none;border: none;">
                        <i class="fa fa-print"></i>
                    </button>
                    <div class="dropdown-menu center" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(163px, 210px, 0px);">
                        <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$item,3,"print"])}}" onclick="return confirm('آیا از پرینت درخواست اطمینان دارید؟')"> قالب A۵ </a>
                        <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$item,4,"print"])}}" onclick="return confirm('آیا از پرینت درخواست اطمینان دارید؟')"> قالب A۴ </a>
                        <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$item,3,"download"])}}" > دانلود A5 </a>
                        <a class="dropdown-item" href="{{route("wh.out.dashboard.print_product_request_form",[$item,4,"download"])}}" >  دانلود A4 </a>
                    </div>
                </td>
                <td>
                    <a href="{{route("wh.transport.dashboard.download_transport",$item)}}"><i
                                class="fa fa-download"></i> </a>
                </td>
                <td>
                    <a href="{{route("wh.transport.dashboard.download_report1",$item)}}"> <i
                                class="fa fa-download"></i> </a>
                </td>

            </tr>
        @endforeach
        </tbody>

    </table>
</div>