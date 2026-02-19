<ul class="nav nav-tabs" id="myTab" role="tablist">

    <li class="nav-item">
        <a class="nav-link {{$active_tab=='personal_info'?"active":""}} show text-uppercase"
           id="tab4-tab"
           data-toggle="tab" href="#tab4"
           role="tab"
           aria-controls="tab4" aria-selected="false">اطلاعات شخصی</a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{$active_tab=='address'?"active":""}}  text-uppercase" id="tab1-tab"
           data-toggle="tab"
           href="#tab1"
           role="tab" aria-controls="tab1" aria-selected="false">اطلاعات آدرس</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$active_tab=='academic_degree'?"active":""}}  text-uppercase"
           id="tab2-tab"
           data-toggle="tab" href="#tab2"
           role="tab"
           aria-controls="tab2" aria-selected="true">
            اطلاعات تحصیلی
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$active_tab=='job_information'?"active":""}}  text-uppercase"
           id="tab3-tab"
           data-toggle="tab" href="#tab3"
           role="tab"
           aria-controls="tab3" aria-selected="true">
            اطلاعات شغلی
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{$active_tab=='educational_course'?"active":""}} text-uppercase"
           id="tab5-tab"
           data-toggle="tab" href="#tab5"
           role="tab"
           aria-controls="tab5" aria-selected="true">
            دوره های آموزشی
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{$active_tab=='dependent'?"active":""}} text-uppercase"
           id="tab10-tab"
           data-toggle="tab" href="#tab10"
           role="tab"
           aria-controls="tab10" aria-selected="true">
            افراد تحت تکفل
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{$active_tab=='financial_information'?"active":""}} text-uppercase"
           id="tab11-tab"
           data-toggle="tab" href="#tab11"
           role="tab"
           aria-controls="tab11" aria-selected="true">
            اطلاعات مالی
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{$active_tab=='document'?"active":""}} text-uppercase" id="tab7-tab"
           data-toggle="tab" href="#tab7"
           role="tab"
           aria-controls="tab7" aria-selected="true">
            مدارک بارگذاری شده
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{$active_tab=='other'?"active":""}}  text-uppercase" id="tab6-tab"
           data-toggle="tab" href="#tab6"
           role="tab"
           aria-controls="tab6" aria-selected="true">
            سایر توضیحات
        </a>
    </li>
</ul>
<div class="tab-content " id="myTab1">
    <div class="tab-pane fade   {{$active_tab=='personal_info'?"active  show":""}}  " id="tab4"
         role="tabpanel"
         aria-labelledby="tab4-tab">

        <div class="row">
            <div class="col-md-3">
                <img style="width: 200px"
                     src="{{asset("chatify_app/users-avatar/".($employment->worker->image->filename??''))}}"
                     onerror="this.onerror=null;this.src='{{url("assets/images/avatar.png")}}';"
                />
            </div>
            <div class="col-md-9">
                @include("hr.employment.register.personal_type.".$employment->personal_type_id.".cooperation_type.".$employment->cooperation_type_id."._preview_info")
                @include("component.input._lable",["id"=>"user_id","label"=>"شماره پرسنلی ","value"=>$employment->worker->id??""])
                @include("component.input._lable",["id"=>"status_id","label"=>"وضعیت درخواست همکاری ","value"=>$employment->get_status()])
                @include("component.input._lable",["id"=>"","label"=>"تاریخ ثبت درخواست","value"=>$employment->create_date()??""])

                @if($employment_document_types_for_personal)
                    @foreach($employment_document_types_for_personal as $employment_document_type)
                        <td>
                            <a href="{{route("hr.employment.admin.confirm.upload_document.download",[$employment,$employment_document_type])}}">
                                {{$employment_document_type->document_type->caption}} ، </a></td>
                    @endforeach
                @endif
            </div>
        </div>

    </div>

    <div class="tab-pane fade {{$active_tab=='address'?"active  show":""}}" id="tab1" role="tabpanel"
         aria-labelledby="tab1-tab">
        @include('hr.employment.register.address._preview',["panel_type"=>"admin"])


    </div>
    <div class="tab-pane fade {{$active_tab=='academic_degree'?"active show":""}} " id="tab2"
         role="tabpanel"
         aria-labelledby="tab2-tab">
        @include("hr.employment.register.personal.academic_degree._list",["panel_type"=>"admin"])

    </div>

    <div class="tab-pane fade {{$active_tab=='job_information'?"active show":""}} " id="tab3"
         role="tabpanel"
         aria-labelledby="tab3-tab">
        @include("hr.employment.register.personal.job_information._list",["panel_type"=>"admin"])

    </div>

    <div class="tab-pane fade {{$active_tab=='educational_course'?"active show":""}}" id="tab5"
         role="tabpanel"
         aria-labelledby="tab5-tab">
        @include("hr.employment.register.personal.educational_course._list",["panel_type"=>"admin"])


    </div>
    <div class="tab-pane fade {{$active_tab=='dependent'?"active show":""}}" id="tab10"
         role="tabpanel"
         aria-labelledby="tab5-tab">
        @include("hr.employment.register.personal.dependent._list",["panel_type"=>"admin"])
    </div>
    <div class="tab-pane fade {{$active_tab=='financial_information'?"active show":""}}" id="tab11"
         role="tabpanel"
         aria-labelledby="tab11-tab">
        <h5>مرکز هزینه</h5>
        @include("hr.employment.admin.personal.financial_information._preview")
        <h5>اطلاعات بانکی</h5>
        @include("hr.employment.register.personal.bank_information._list",["panel_type"=>"admin"])


    </div>

    <div class="tab-pane fade {{$active_tab=='other'?"active show":""}}" id="tab6" role="tabpanel"
         aria-labelledby="tab6-tab">
        توضیحات:

        <b>{{$employment->description??""}}</b>
    </div>

    <div class="tab-pane fade {{$active_tab=='document'?"active show":""}}" id="tab7" role="tabpanel"
         aria-labelledby="tab7-tab">
        @include("hr.employment.register.personal.upload_final_document._list")

    </div>





