@if ( $bom->permutations()->count() == 0 )
    <br/>
    <div class="alert alert-warning">از آنجایی که هیچ کدام از مواد BOM جایگزین مصرف ندارند،
        کالای جایگزین برای BOM وجود ندارد.
    </div>
@else
<div class="table-responsive center">
    <table class="table table-styling">
        <thead>
        <tr>
            <th>#</th>
            <th>کد</th>
            <th>کالا</th>
            <th> کالاهای مصرفی</th>
            <th>اطلاعات پایه</th>
            <th>وضعیت</th>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($bom->permutations as $item)
            <tr>
                <td title="{{$item->code}}">{{++$row}}</td>
                <td>  {{$item->getSystemCode()}}</td>
                <td>
                    جایگزین تولید
                   {{$row}}

                </td>
                <td>
                    <span title="{!! $item->getTitleFroView() !!}">{{$item->items()->count()}}  کالا </span>
                </td>
                <td>
                    وزن (کیلوگرم):
                    @if($item->active_status_id==1200)
                        {{$item->weight}}
                    @else
                        {{$item->weight}}
                    @endif

                </td>
                <td>
                    {{$item->active_status->caption}}

                </td>

            </tr>
        @endforeach
        </tbody>

    </table>
</div>
@endif
