<div class="col-sm-12">

    <div class="card">
        <div class="card-header">
            <h5> {{__("product design form, :code",["code"=>$product_creation_process->getCode()])}}</h5>

        </div>
        <div class="card-block">

            <div class="table-responsive">
                <table class="table table-styling">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{__("input.date and time")}}</th>
                        <th>{{__("input.action user_id")}}</th>
                        <th>{{__("input.event_id")}}</th>
                        <th>{{(__("input.status_id"))}}</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=0;@endphp
                    @foreach($list as $item)
                        <tr>
                            <td>{{++$row}}</td>
                            <td>
                                {{$item->create_datetime()}}
                            </td>
                            <td>{{$item->worker->fullName()}}</td>
                            <td>  {{$item->event->caption??""}}</td>
                            <td>  {{$item->status->caption??""}}</td>


                        </tr>
                        @if($item->message)
                            <tr>
                                <td colspan="5" class="alert-success">
                                    {{$item->message->text}}
                                </td>

                            </tr>
                            @endif
                    @endforeach
                    </tbody>

                </table>
            </div>
            <div class="float-left">
                {{__("table.show records")}}
                <b>{{$list->firstItem()}}</b>
                {{__("table.up")}}
                <b>{{$list->lastItem()}}</b>
                {{__("table.from")}}
                <b>{{$list->total()}}</b>
                {{__("table.available records")}}
            </div>
        </div>
        <div class="text-center">
            {{$list->links('pagination::bootstrap-4')}}
        </div>
    </div>
</div>
