<div class="col-sm-12">

    <div class="card">
        <div class="card-header">
            <h5> سابقه عملیات بر روی فرم {{$packing_form->getCode()}}</h5>
        </div>
        <div class="card-block">

            <div class="table-responsive">
                <table class="table table-styling center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>تاریخ و ساعت</th>
                        <th>اقدام کننده</th>
                        <th>رویداد</th>
                        <th>وضعیت</th>
                        <th></th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=$logs->firstItem();@endphp
                    @foreach($logs as $item)
                        <tr>
                            <td>{{$row++}}</td>

                            <td>{{$item->get_datetime()}}</td>
                            <td>{{$item->worker->fullname()}}</td>
                            <td>{{$item->event->caption??""}}</td>
                            <td>{{$item->status->caption??""}}</td>
                            <td>
                                @if($item->form && $item->form->form_type_id ==0 )
                                    <a target="_blank" href="{{route("DCEF_QR",[$item->form,$item->form->getRandom()])}}">{{$item->form->code??""}}</a>
                                @endif
                                    @if($item->form && $item->form->form_type_id !=0 )
                                       {{$item->form->code??""}}
                                    @endif

                                {{$item->packing_form_master->code??""}}
                            </td>
                        </tr>

                        @if(isset($item->message->text))
                            <tr>
                                <td colspan="6" style="padding: 0">
                                    <div class="alert alert-info ">
                                        {{$item->message->text??""}}
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>

                </table>

            </div>
            <div class="float-left">
                نمايش رکوردهای
                <b>{{$logs->firstItem()}}</b>
                تا
                <b>{{$logs->lastItem()}}</b>
                از
                <b>{{$logs->total()}}</b>
                رکورد موجود
            </div>
            <div class="text-center">
                {{$logs->links('pagination::bootstrap-4')}}
            </div>

        </div>
    </div>
</div>

