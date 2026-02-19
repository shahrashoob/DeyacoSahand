@if($employment->employment_document_types->count()>=1)
<div class="col-md-12 center ">
        <div class="table-responsive ">
            <table class="table table-styling">
                <thead>
                <tr>
                    <th> ردیف</th>
                    <th>عنوان</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @php $row=0;@endphp
                @foreach($employment->employment_document_types as $item)
                    <tr>
                        <td>{{++$row}}</td>
                        <td><a href="{{route("hr.employment.admin.confirm.upload_document.download",[$employment,$item->id])}}">{{$item->document_type->caption ??""}}</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
