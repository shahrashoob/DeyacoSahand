<div class="col-sm-12">

    <div class="card">
        <div class="card-header">
            <h5> فرم درخواست طراحی کالا {{$product_creation_process->getCode()}}</h5>

        </div>
        <div class="card-block">

            <div class="table-responsive">
                <table class="table table-styling center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>تاریخ و زمان</th>
                        <th>اقدام کننده</th>
                        <th>رویداد</th>
                        <th>وضعیت</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=$list->firstItem();@endphp
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
                                    {!! $item->message->text !!}
                                </td>

                            </tr>
                            @endif
                    @endforeach
                    </tbody>

                </table>
            </div>
            <div class="float-left">
                نمايش رکوردهای
                <b>{{$list->firstItem()}}</b>
                تا
                <b>{{$list->lastItem()}}</b>
                از
                <b>{{$list->total()}}</b>
                رکورد موجود
            </div>
        </div>
        <div class="text-center">
            {{$list->links('pagination::bootstrap-4')}}
        </div>
    </div>
</div>
