
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>سابقه عملیات بر روی فرم تولید
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>
                                <td>ردیف</td>
                                <th>تاریخ و ساعت</th>
                                <th>اقدام کننده</th>
                                <th>رویداد</th>
                                <th>باند خروجی</th>
                                <th>توضیحات</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($production_form->logs as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        {{$item->get_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->worker->fullname()}}
                                    </td>
                                    <td>
                                        {{$item->event->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->band_code??""}}
                                    </td>
                                    <td>
                                        {{$item->message->text??""}}
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>

        </div>
