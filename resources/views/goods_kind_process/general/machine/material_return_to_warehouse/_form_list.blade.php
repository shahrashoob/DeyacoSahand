<div class="col-md-12 center">
    <br/>
    <br/>
    <h5>لیست فرم های ورود و خروج به ازای هر کالا</h5></div>
<div class="col-md-12" style="overflow: auto">
    <table class="table table-styling center">
        <tbody>
        <tr>
            <th></th>
            <th>نام و کد کالا</th>
            <th>وضعیت فرم ورود</th>
            <th>فرم ورود<br/> به انبار</th>
            <th>فرم خروج<br/> از انبارک</th>
            <th>فرم ورود<br/> تغییر درجه</th>
            <th>فرم خروج<br/> تغییر درجه</th>
            <th>فرم ورود<br/> تراکنش اصلاحیه</th>
            <th>فرم خروج<br/> تراکنش اصلاحیه</th>
            <th>فرم ورود<br/> ضایعات</th>
            <th>فرم خروج<br/> ضایعات</th>
            <th>فرم ورود<br/> اصلاحات</th>
            <th>فرم خروج<br/> اصلاحات</th>
            @if($allow_show_cost)
                <th>
                   میانگین بهای تمام شده
                    <br/>
                    به ازای تولید یک واحد کالا (ریال)
                </th>
            @endif
        </tr>
        @php $row=0;@endphp
        @foreach($machine_allocation_modification->forms as $machine_allocation_modification_form )
            <tr>
                <td>{{++$row}}</td>
                <td>
                    {{$machine_allocation_modification_form->product->fullCaption()}}
                </td>
                <td>
                    @if($allow_show_log && $machine_allocation_modification_form->json_data_id)
                        <a target="_blank" href="{{route("utility.json_view.actual_consumption_view",$machine_allocation_modification_form->json_data_id)}}">
                            {{$machine_allocation_modification_form->input_form_status->caption??""}}
                        </a>
                    @else
                        {{$machine_allocation_modification_form->input_form_status->caption??""}}

                    @endif

                </td>

                <td>
                    {{$machine_allocation_modification_form->input_form->code??""}}
                </td>
                <td>
                    {{$machine_allocation_modification_form->output_form->code??""}}
                </td>
                <td>
                    {{$machine_allocation_modification_form->degree_change_input_form->code??""}}
                </td>
                <td>
                    {{$machine_allocation_modification_form->degree_change_output_form->code??""}}
                </td>
                <td>
                    {{$machine_allocation_modification_form->amendment_input_form->code??""}}
                </td>
                <td>
                    {{$machine_allocation_modification_form->amendment_output_form->code??""}}
                </td>
                <td>
                    {{$machine_allocation_modification_form->change_waste_input_form->code??""}}
                </td>
                <td>
                    {{$machine_allocation_modification_form->change_waste_output_form->code??""}}
                </td>
                <td>
                    {{$machine_allocation_modification_form->other_correction_input_form->code??""}}
                </td>
                <td>
                    {{$machine_allocation_modification_form->other_correction_output_form->code??""}}
                </td>
                @if($allow_show_cost)
                    <td>
                        {{($machine_allocation_modification_form->avg_material_cost)}}
                    </td>
                @endif
            </tr>
            @if($machine_allocation_modification_form->actual_consumption_status_id == 6021303)
                <td colspan="14">
                <span class="text-danger">به دلیل عدم تایید فرم ورود
                    {{$machine_allocation_modification_form->input_form->code??""}}
                    ، مقدار مصرف واقعی محاسبه نشد.</span>
                </td>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
