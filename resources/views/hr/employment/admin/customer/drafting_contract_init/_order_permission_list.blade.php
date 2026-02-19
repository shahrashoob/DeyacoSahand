<div class="table-responsive">
    <table class="table table-styling ">
        <thead>
        <tr>
            <th>تایید کنندگان</th>
            <th>پست جهت اطلاع رسانی</th>
        </tr>
        </thead>
        <tbody>
        @foreach($employment->customer->order_permission as $item)
            <tr>
                <td>{{$item->order_permission_type->caption}}</td>
                <td>{{ $item->post->caption ?? ""}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>