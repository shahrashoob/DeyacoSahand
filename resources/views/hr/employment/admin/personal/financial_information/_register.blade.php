<div class="row">
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>
                ثبت مرکز تامین(کد تفضیلی)
            </h5>

        </div>
        <div class="card-block">
            <div class="w-100"></div>
            <div class="col-md-6">

            @if($company_have_separate_warehousing_software)
                @include("component.input._aotocomplet2",[
                    "id"=>"cost_center_id",
                    "label"=>"مرکز هزینه ",
                    "option"=>$cost_center_option["items"],
                    "val"=>$cost_center_option["value"],
                    "text"=>$cost_center_option["text"],
                    "class_col"=>"",
                    "url"=>route("hr.employment.admin.personal.cost_center.create",$employment),
                    "url_text"=>" <i class='fa fa-plus'></i> "." "." مرکز هزینه جدید ",
                    'mark'=>'*'
                    ])
            @endif
            @if($company_have_separate_financial_software)
                @include("component.input._text", ["id"=>"detailed_code", 'label'=>"کد تفضیلی",  "class_col"=>"",'mark'=>'*'])
            @endif
            </div>
            <div class="col-md-3">
            <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}" class="btn btn-outline-dark">بازگشت</a>

            <button type="submit" class="btn btn-primary">ثبت</button>
        </div>
        </div>

    </div>

</div>

</div>
