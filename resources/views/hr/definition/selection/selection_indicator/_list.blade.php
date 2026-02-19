<div class="row">

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> لیست شاخص های
                    {{$selection->caption}}
                </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>ردیف</th>
                            <th> عنوان شاخص</th>
                            <th>نوع شاخص</th>
                            <th>وزن شاخص</th>
                            <th>حداقل امتیاز</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($selection_indicators as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>
                                    <a href="{{route("hr.definition.selection.selection_indicator.edit",$item)}}">{{$item->caption?? ""}}</a>
                                </td>
                                <td>{{$item->field_type->caption}}</td>
                                <td>{{$item->weight}}</td>
                                <td>{{$item->min_score}}</td>
                                <td>
                                    <a href="{{route("hr.definition.selection.selection_indicator.destroy",[$item->selection_id,$item])}}"
                                       onclick="return confirm('آیا از حذف شاخص اطمینان دارید؟')"><i
                                            class="fa fa-trash text-danger"></i> </a>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

