@if(count($transport_list)>0)

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> {{$header_caption}} </h5>
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
                            @if(isset($route_downlaod_dcbl))
                                <th>بارنامه</th>
                            @endif
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($transport_list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>
                                    <a href="{{route(isset($route_name)?$route_name:"DCBL_QR",[$item,$item->getRandom()])}}">

                                        {{$item->getCode()}}
                                    </a>
                                </td>
                                <td>{{$item->create_datetime()}}</td>
                                <td>{{$item->car->driver_firstname??""}} {{$item->car->driver_lastname??""}}</td>
                                <td>{{$item->car->driver_mobile??""}}</td>
                                <td>{{$item->car->car_plaque??""}}</td>
                                <td>{{$item->status->caption??""}}</td>
                                <td>
                                    @if(isset($route_downlaod_dcbl))
                                        <a href="{{route($route_downlaod_dcbl,[$item,$item->random])}}">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @endif
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
