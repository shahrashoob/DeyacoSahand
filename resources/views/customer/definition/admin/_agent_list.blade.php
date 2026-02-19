<div class="table-responsive">
    <table class="table table-styling center">
        <thead>
        <tr>
            <th>ردیف</th>
            <th>نام ونام خانودگی نماینده</th>
            <th>نوع ارتباط</th>
            <th>کدملی نماینده</th>
            <th>حق امضا</th>


        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($agent_list as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>
                    <a href="{{route("customer_group.definition.admin.edit_agent",[$customer,$item->id])}}"> {{$item->worker->fullname() ??""}}</a>
                </td>
                <td>{{$item->agent_type->caption ??""}}</td>
                <td>{{$item->worker->national_code ??""}}</td>
                <td>@if($item->has_the_right_to_sign==1)
                        <i class="fa fa-check"></i>
                    @else
                        <i class="fa fa-times"></i>
                    @endif</td>
            </tr>

        </tbody>
        @endforeach
    </table>
</div>
