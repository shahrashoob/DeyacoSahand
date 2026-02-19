<form id="form1" style="display: inline" action="{{route("hr.post.employment.info_setting",$post)}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="col-md-6">
    <div class="col-md-12">
        <input type="checkbox" id="does_it_have_shift_work" name="does_it_have_shift_work"
                {{$post->does_it_have_shift_work?"checked":""}}>
        دارای شیفت کاری است.
        <br/>
    </div>
    </div>
    <div class="col-md-12">
        @include("component.input._aotocomplet2",[
            "id"=>"shift_id",
            "label"=>"شیفت  ",
            "option"=>$shift_option["items"],
            "val"=>$shift_option["value"],
            "text"=>$shift_option["text"],
            "class_col"=>"col-md-3"
            ])

    </div>
    <div class="col-md-12">
        @include("component.input._aotocomplet2",[
            "id"=>"shift_delivery_module_id",
            "label"=>"ماژول تحویل شیفت  ",
            "option"=>$shift_delivery_module_option["items"],
            "val"=>$shift_delivery_module_option["value"],
            "text"=>$shift_delivery_module_option["text"],
             "class_col"=>"col-md-3"
            ])
    </div>
    <div class="col-md-12">
        @include("component.input._radio_box01",["id"=>"allow_show_result_of_selection","label"=>"آیا افراد دیگر می توانند نتیجه ارزیابی مصاحبه را مشاهده کنند؟","label0"=>"خیر","label1"=>"بله","value"=>$post->allow_show_result_of_selection])
    </div>
    <div class="col-md-6">

        @include("component.input._select",[
                    "id"=>"contract_id",
                    "label"=>"قرارداد جهت همکاری",
                    "option"=>$contract_option["items"],
                     ])
        <br/>
        {{--        <div class="col-md-12">--}}
        {{--            نوع قرارداد:--}}
        {{--            <br/>--}}
        {{--            @foreach($contract_types as $item)--}}

        {{--                <input type="checkbox" name="post_contract_type[{{$item->id}}]"--}}
        {{--                        {{in_array($item->id,$post_contract_type_ids)?"checked='checked'":""}}--}}
        {{--                > {{$item->caption}} &nbsp;&nbsp;--}}
        {{--            @endforeach--}}
        {{--            <br/>--}}
        {{--            <br/>--}}
        {{--        </div>--}}

        @include("component.input._radio_box01",["id"=>"is_basis_for_calculation_working_hour_on_labor_low","label"=>"مبنای محاسبه ساعت کاری","label1"=>"طبق قانون کار","label0"=>"طبق جدول شیفت","value"=>$post->is_basis_for_calculation_working_hour_on_labor_low])

        @include("component.input._radio_box01",["id"=>"is_basis_for_daily_salary_on_labor_low","label"=>"مبنای محاسبه مزد روزانه براساس قانون کار","label1"=>" هست","label0"=>" نیست ","value"=>$post->is_basis_for_daily_salary_on_labor_low])

        <label class="col-md-12" id="daily_salary_label" for="daily_salary">مزد مبنا روزانه</label>
        @include("component.input._text",["id"=>"daily_salary","value"=>$post->daily_salary ??""])

        @include("component.input._number",["id"=>"right_to_work",  "label"=>"حق شغل(ریال)","value"=>$post->right_to_work ??""])
        @include("component.input._number",["id"=>"number_of_days_before_termination_of_contract",  "label"=>"تعداد روز اعلام عدم همکاری قبل از فسخ قرارداد","value"=>$post->number_of_days_before_termination_of_contract ??""])

        @include("component.input._radio_box01",["id"=>"is_basis_for_duties_on_the_opinion_of_employer","label"=>"مبنای انتخاب شرح شغل طبق نظر کارفرما","label1"=>"هست ","label0"=>" نیست ","value"=>$post->is_basis_for_duties_on_the_opinion_of_employer])
        <label class="col-md-12" id="duties_label" for="duties">شرح وظایف</label>
        @include("component.input._textarea",["id"=>"duties","value"=>$post->duties ??""])


    </div>

    @include("hr.post.employment._employment_step")


    <button type="submit" class="btn btn-primary"> ذخیره</button>
    <a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>
</form>

