@if(count($current_input_list) > 0)
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>لیست ورودی های ماشین
                </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center!important;">
                        <thead>
                        <tr>

                            <th>#</th>
                            <th>نام کالا</th>
                            <th>مقدار</th>
                            <th> همبافت</th>
                            <th>رسته کالایی</th>
                            <th> شماره ورودی</th>
                            <th> حامل</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($current_input_list as $item)
                            <tr>

                                <td>{{++$row}}
                                </td>
                                <td>
                                    {{$item->material->caption??"***"}}
                                </td>
                                <td>
                                    {{round($item->amount,3)}}
                                </td>
                                <td>
                                    {{$item->lot_number->code??""}}
                                </td>
                                <td>
                                    {{$item->goods_kind->caption??""}}
                                </td>
                                <td>
                                    ورودی  {{$item->input_line_code}}
                                </td>
                                <td>
                                    {{isset($item->carrier)?$item->carrier->getCaption():""}}
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
