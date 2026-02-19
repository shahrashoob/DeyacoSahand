@if(count($form_list) > 0)
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>لیست فرم های تحویل چله به انبار
                </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center!important;">
                        <thead>
                        <tr>

                            <th>شماره فرم</th>
                            <th>استخراج کننده چله</th>
                            <th> حامل</th>
                            <th>درجه</th>
                            <th>متراژ</th>
                            <th>وضعیت</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($form_list as $item)
                            <tr>

                                <td>

                                    <a href="{{route("fabric_raw.jacquard.machine.warps_delivery_to_warehouse.index",[$machine,$item])}}">{{$item->code}}</a>
                                </td>
                                <td>
                                    {{$item->worker->fullname()}}
                                </td>
                                <td>
                                    {{$item->item[0]->carrier->getCaption()}}
                                </td>
                                <td>
                                    {{$item->item[0]->degree->caption}}
                                </td>
                                <td>
                                    {{$item->item[0]->amount}}
                                </td>
                                <td>
                                    {{$item->status->caption}}
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

