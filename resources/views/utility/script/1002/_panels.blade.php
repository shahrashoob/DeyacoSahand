<div class="card">
    <div class="card-header">
        <h5> لیست پست های سازمان </h5>
    </div>
    <div class="card-block" style="overflow: auto">
        <table class="table table-bordered center">
            @php $row=0;@endphp
            <tr>
                <th>عنوان پست</th>
                <th>عنوان پست</th>
                <th>عنوان پست</th>
                <th>عنوان پست</th>
                <th>عنوان پست</th>
            </tr>
            <tr>
                @foreach($posts as $item)
                    @php $key="k".$item->id;@endphp
                    <td>
                        <a href="{{route("utility.script.1002.edit_post",[$script,$item])}}">{{$item->caption}}</a>
                        ({{$item->worker()->count()}} نفر)
                    </td>
                    @if($row % 5==4)
            </tr>
            <tr>
                @endif
                @php $row++;@endphp
                @endforeach
            </tr>
        </table>

        <div class="float-left">
            نمايش رکوردهای
            <b>{{$posts->firstItem()}}</b>
            تا
            <b>{{$posts->lastItem()}}</b>
            از
            <b>{{$posts->total()}}</b>
            رکورد موجود


        </div>
    </div>
    <div class="text-center">
        {{$posts->links('pagination::bootstrap-4')}}
    </div>
</div>
