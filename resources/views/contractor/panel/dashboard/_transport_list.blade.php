@if(count($transport_list)>0)

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> لیست بارهای ارسال شده برای کارفرما </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شماره بار</th>
                            <th>تاریخ ایجاد</th>
                            <th>نام و نام خانوادگی راننده</th>
                            <th>شماره تماس راننده</th>
                            <th>شماره پلاک خودرو</th>
                            <th> وضعیت</th>
                            <th>بارنامه</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($transport_list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>
                                    <a href="{{route("DCBL_QR",[$item,$item->random])}}">

                                        {{$item->getCode()}}
                                    </a>
                                </td>
                                <td>{{$item->create_datetime()}}</td>
                                <td>{{$item->car->driver_firstname??""}} {{$item->car->driver_lastname??""}}</td>
                                <td>{{$item->car->driver_mobile??""}}</td>
                                <td>{{$item->car->car_plaque??""}}</td>
                                <td>{{$item->status->caption??""}}</td>
                                <td>
                                    <a href="{{route("contractor.panel.print.download_transport_card",[$item,$item->random])}}">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
