<div class="row">

    <div class="md-col-12 " style="margin: auto">

        <div class="table-responsive text-center">
            <table class="table table-styling" style="text-align: center!important;">
                <thead>
                <tr>
                    <td>ردیف</td>
                    <th>تاریخ</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>

                </thead>
                <tbody>
                <tr>
                    @php $row=1; $first_day=null;@endphp
                    @foreach($list as $item)
                        @if($first_day==null || $first_day !=$item->datetime)
                            @php $first_day=$item->datetime@endphp
                </tr>
                <tr>
                    <td>  {{$row++}}</td>
                    <td> {{$item->getDatetime("%A, %d %B ")}}</td>
                    @endif

                    <td title="{{$item->id}}">
                        {{$item->getStartDatetime()}} - {{$item->getEndDatetime()}}
                        <br/>
                        {{$item->shift->caption}} - ({{$item->shift_work->caption}})
                    </td>


                    @endforeach

                </tr>
                </tbody>

            </table>
        </div>
    </div>
</div>
