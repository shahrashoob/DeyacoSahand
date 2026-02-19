<div class="row">
    <div class="col-md-12 center">
        <h4 class="alert alert-primary">
            لیست بسته بندی ها
        </h4>
        <table class="table center table_report">
            <tbody>
            @php $row=1;$packing_number=0;@endphp
            @foreach($qc_data["new_packing_form"][$band_code] as $item)
                @if($packing_number !=$item["packing_form_number"] )
                    @php $row=1; $packing_number=$item["packing_form_number"]; @endphp
                    <tr>
                        <td colspan="4">
                            <br/>
                            <b style="font-size: 16px"> بسته بندی شماره {{$packing_number}}</b>
                            <br/>
                        </td>
                    </tr>
                    <tr>
                        <th>ردیف</th>

                        <th>از  {{$qc_data["unit_caption"]["caption"]}}</th>
                        <th>تا  {{$qc_data["unit_caption"]["caption"]}}</th>
                        <th>درجه</th>
                    </tr>

                @endif
                <tr>
                    <td>{{$row++}}</td>


                    <td>{{$item["start_point"]}}</td>
                    <td>{{$item["end_point"]}}</td>
                    <td>{{$item["degree"]["caption"]}}</td>
                </tr>

            @endforeach
            </tbody>

        </table>
    </div>
    @if(isset($qc_data["item_faults"][$band_code])>0)
        <div class="col-md-12 center">
            <h4 class="alert alert-primary">
                لیست نقص های کالا
            </h4>
            <table class="table center table_report">
                <thead>
                <tr>
                    <th>ردیف</th>

                    <th>عنوان نقص</th>
                    <th>از مقدار</th>
                    <th>تا مقدار</th>
                </tr>
                </thead>
                <tbody>
                @php $row=1;$packing_number=0;@endphp
                @foreach($qc_data["item_faults"][$band_code] as $fault_id=>$faults)

                    @foreach($faults as $fault)
                    <tr>
                        <td>{{$row++}}</td>

                        <td>{{$qc_data["faults"][$fault_id]["caption"]}}</td>
                        <td>{{isset($fault["point"])?$fault["point"]:$fault["start_point"]}}</td>
                        <td>{{isset($fault["end_point"])?$fault["end_point"]:""}}</td>

                    </tr>
                    @endforeach
                @endforeach
                </tbody>

            </table>
        </div>
    @endif
</div>