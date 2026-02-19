@if( count($allocation_list) > 0)

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>تخصیص های انجام شده
                </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center!important;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شماره تخصیص</th>
                            <th> کالا</th>
                            <th>مقدار</th>
                            <th>تاریخ و زمان ثبت</th>
                            <th>تاریخ و زمان هماهنگی</th>
                            <th>وضعیت</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($allocation_list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>{{$item->allocation->code()}}</td>
                                <td>
                                    {{$item->product->fullCaption()}}
                                </td>
                                <td>
                                    {{$item->allocation_amount}} {{$item->product->unit->caption??""}}
                                </td>
                                <td>
                                    {{$item->get_datetime()}}
                                </td>
                                <td>
                                    {{$item->allocation->get_partical_datetime()}}
                                </td>
                                <td>
                                    {{$item->status->caption??""}}
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
