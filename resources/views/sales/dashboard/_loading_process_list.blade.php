{{--count($order->loadingProcess)--}}
@if(1)
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5> ارسال بار</h5>
        </div>
{{--        <div class="card-block">--}}
{{--            <div class="table-responsive">--}}
{{--                <table class="table table-styling">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th>#</th>--}}
{{--                        <th>کد بارگیری</th>--}}
{{--                        <th>سرپرست جمع آوری</th>--}}
{{--                        <th>سرپرست بارگیری</th>--}}
{{--                        <th> وسیله نقلیه</th>--}}
{{--                        <th>وضعیت</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    @php $i=1;@endphp--}}
{{--                    @foreach($order->loadingProcess as $item)--}}
{{--                        <tr>--}}
{{--                            <td>{{$i++}}</td>--}}
{{--                            <td>{{$item->code()}}</td>--}}
{{--                            <td>{{$item->supervisor_collect_post->caption}}</td>--}}
{{--                            <td>{{$item->supervisor_loading_post->caption}}</td>--}}
{{--                            <td>{{$item->car_type->caption}}</td>--}}
{{--                            <td>{{$item->getStatus(1)}}</td>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>
</div>
@endif
