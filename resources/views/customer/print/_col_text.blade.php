<tr style="background: #9d9d9d;">
    <td colspan="{{array_sum($setting_data) +11}}">
        <div style="text-align: center;">

            <h3> مشخصات کالا / خدمات </h3>
        </div>
    </td>
</tr>


<tr style="background: #e2d7d7;">
    <th> ردیف</th>
    <th>کد کالا/خدمت</th>

    @if($setting_data["show_product_caption_in_pre_factor"])
    <th>نام کالا/خدمت</th>
    @endif

    @if($setting_data["show_packing_type_caption_in_pre_factor"] || $setting_data["show_packing_type_code_in_pre_factor"])
    <th>بسته بندی</th>
    @endif


    @if($setting_data["show_property_1_in_pre_factor"])
    <th>{{$property[1]->caption??"---"}}</th>
    @endif

    @if($setting_data["show_property_2_in_pre_factor"])
    <th>{{$property[2]->caption??"---"}}</th>
    @endif

    @if($setting_data["show_property_3_in_pre_factor"])
    <th>{{$property[3]->caption??"---"}}</th>
    @endif

    <th>درجه</th>
    <th>مقدار</th>
    <th>واحد سنجش</th>
    <th>مبلغ واحد</th>
    <th>مبلغ کل</th>
    <th>مبلغ تخفیف</th>
    <th>بها پس از تخفیف</th>
    <th>جمع مالیات و عوارض</th>
    <th>جمع مبلغ سطر</th>
</tr>
