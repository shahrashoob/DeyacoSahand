<form id="form1"
      action="{{route("$route_path.update_next_ones",[$machine_type,$production_channel_type])}}"
      method="post"
      autocomplete="off"
      novalidate="novalidate">
    @csrf
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-styling center">
                <thead>
                <tr>
                    <th>اولویت</th>
                    <th>نوع کانال مجاز بعدی </th>
                    <th></th>
                </tr>

                </thead>
                <tbody>
                @php $row=0;@endphp
                @foreach($production_channel_type->get_production_channel_next_ones($machine_type) as $item)
                    <tr>
                        <td>{{$item->priority_number}}</td>
                        <td>
                            {{$item->next_production_channel_type->caption}}

                        </td>

                        <td>
                            <a class="text-danger"
                               href="{{ route("$route_path.delete_next_ones",[$machine_type,$item->production_channel_type_id,$item->next_production_channel_type_id])}}"
                               onclick="return confirm('آیا از حذف اطمینان دارید؟')"><i
                                        class="fa fa-trash"></i> </a>

                        </td>


                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
<div class="row">
    @include("component.input._number",[
                                        "id"=>"priority_number",
                                        "label"=>" اولویت  ",
                                        "class_col"=>"col-md-3"
                                        ])
    <div class="w-100"></div>
    <div class="col-md-3">
        @include("component.input._aotocomplet2",[
            "id"=>"next_production_channel_type_id",
            "label"=>"نوع کانال مجاز بعدی ",
            "option"=>$production_channel_type_option["items"],
            "val"=>$production_channel_type_option["value"],
            "text"=>$production_channel_type_option["text"],
            "class_col"=>""
            ])
    </div>


</div>
    <button type="submit" class="btn btn-primary"> افزودن</button>

</form>