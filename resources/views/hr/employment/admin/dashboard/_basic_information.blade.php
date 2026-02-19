<div class="card-block">
    <div style="float: right">

        <div class="row">

            <div class="col-md-3">
            <img style="width: 200px" src="{{asset("upload/worker/".($worker->image->filename??''))}}"
                 onerror="this.onerror=null;this.src='{{url("upload/worker/worker.png")}}';"
            />
            <br/>
            <br/>
            </div>

        <div class="col-md-9">
            <div class="row">

                    @if($employment->worker->personal_type_id == 2)
                        @include("component.input._lable", [
                            "id" => "company_name",
                            "label" => "نام شرکت",
                            "value" => $employment->worker->company_name ?? ""
                        ])
                    @endif
                    @include("component.input._lable", ["id"=>"personal_type_id","label"=>"نوع شخصیت","value"=>$employment->worker->personal_type->caption??""])
                    @include("component.input._lable", ["id"=>"nationality_id","label"=>"ملیت","value"=>$employment->worker->nationality->caption??""])
                    @include("component.input._lable", ["id"=>"first_name","label"=>($employment->worker->personal_type_id==1 )?"نام":"نام مدیر عامل","value"=>$employment->worker->firstname??""])
                    @include("component.input._lable",["id"=>"last_name","label"=>($employment->worker->personal_type_id==1 )?"  نام خانوادگی ":"نام خانوادگی مدیر عامل","value"=>$employment->worker->lastname??""])
                    @if($employment->worker->personal_type_id == 1)
                        @include("component.input._lable", [
                            "id" => "father_name",
                            "label" => "نام پدر",
                            "value" => $employment->worker->father_name ?? ""
                        ])
                    @endif
                    @include("component.input._lable",["id"=>"user_id","label"=>"شماره پرسنلی ","value"=>$employment->worker->id??""])
                    @include("component.input._lable",["id"=>"insurance_number","label"=>"شماره بیمه ","value"=>$employment->worker->insurance_number??""])
                    @include("component.input._lable",["id"=>"national_code","label"=>($employment->worker->personal_type_id==1 )?" کدملی ":"شناسه ملی شرکت","value"=>$employment->worker->national_code??""])
                    @include("component.input._lable",["id"=>"date_of_birth","label"=>($employment->worker->personal_type_id==1 )?" تاریخ تولد ":"تاریخ ثبت شرکت","value"=>$employment->worker->get_date_of_birth()??""])
                    @include("component.input._lable",["id"=>"place_of_birth","label"=>"محل تولد","value"=>$employment->worker->place_of_birth??""])
                    @include("component.input._lable",["id"=>"marital_status_id","label"=>"وضعیت تاهل","value"=>$employment->worker->marital_status->caption??""])
                    @include("component.input._lable",["id"=>"gender_id","label"=>"جنسیت ","value"=>$employment->worker->gender->caption??""])
                    @include("component.input._lable",["id"=>"date_of_starting_work","label"=>"تاریخ شروع به کار","value"=>$employment->worker->get_date_of_starting_work()??""])
                    @include("component.input._lable",["id"=>"post_id","label"=>"پست سازمانی دیجیتال(مورد درخواست) ","value"=>$employment->post->caption??""])
                    @include("component.input._lable",["id"=>"status_id","label"=>"وضعیت ","value"=>$employment->status->caption??""])
                    @include("component.input._lable",["id"=>"","label"=>"تاریخ ثبت درخواست","value"=>$employment->create_date()??""])

            </div>
        </div>
    </div>
</div>
</div>
