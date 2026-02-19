<ul class="nav nav-tabs" id="myTab" role="tablist">

    <li class="nav-item">
        <a class="nav-link {{$active_tab=='personal_info'?"active":""}} show text-uppercase"
           id="tab4-tab"
           data-toggle="tab" href="#tab4"
           role="tab"
           aria-controls="tab4" aria-selected="false">
            @if($employment->personal_type_id==1)
                اطلاعات شخصی
            @else
                اطلاعات مدیر عامل
            @endif
        </a>
    </li>
    @if($employment->personal_type_id==2)
        <li class="nav-item">
            <a class="nav-link {{$active_tab=='company_info'?"active":""}} show text-uppercase"
               id="tab11-tab"
               data-toggle="tab" href="#tab11"
               role="tab"
               aria-controls="tab11" aria-selected="false">
                اطلاعات شرکت
            </a>
        </li>
    @endif
    @if(!in_array($employment->cooperation_type_id,[21,31,61]))
    <li class="nav-item">
        <a class="nav-link  {{$active_tab=='address'?"active":""}}  text-uppercase" id="tab1-tab"
           data-toggle="tab"
           href="#tab1"
           role="tab" aria-controls="tab1" aria-selected="false">
            @if($employment->personal_type_id==1)
                اطلاعات آدرس
            @else
                اطلاعات آدرس شرکت
            @endif
            </a>
    </li>
    @endif
    @if(in_array($employment->cooperation_type_id,[1,11]) )
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
    @endif
    @if(!in_array($employment->cooperation_type_id,[21,31,61]))
    <li class="nav-item">
        <a class="nav-link {{$active_tab=='other'?"active":""}}  text-uppercase" id="tab6-tab"
           data-toggle="tab" href="#tab6"
           role="tab"
           aria-controls="tab6" aria-selected="true">
            سایر توضیحات
        </a>
    </li>
    @endif
    @if($employment->employment_selections()->count()>0)
        <li class="nav-item">
            <a class="nav-link {{$active_tab=='employment_process'?"active":""}}  text-uppercase" id="tab8-tab"
               data-toggle="tab" href="#tab8"
               role="tab"
               aria-controls="tab8" aria-selected="true">
                فرایند استخدام
            </a>
        </li>
    @endif
    @if($employment->logs()->count()>0)
        <li class="nav-item">
            <a class="nav-link {{$active_tab=='employment_log'?"active":""}}  text-uppercase" id="tab9-tab"
               data-toggle="tab" href="#tab9"
               role="tab"
               aria-controls="tab9" aria-selected="true">
                سابقه عملیات بر روی در خواست همکاری
            </a>
        </li>
    @endif
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

                @if($employment->status_personal_id == 4641401 && $confirm_info_permission && $employment->status_id!=4640109 )
                    @if(in_array($employment->cooperation_type_id,[1,11]) )
                        <p class="alert alert-warning">لطفاً مدارک را با دقت مرور کرده و با متن ارائه شده مطابقت دهید.
                            در
                            صورت عدم مغایرت، تایید نمایید. </p>
                    @else
                        <p class="alert alert-warning">لطفاً اطلاعات با دقت بخوانید و ودر صورت عدم نیاز به اصلاح، تایید
                            نمایید. </p>
                    @endif
                @endif

                @include("hr.employment.register.personal_type.".$employment->personal_type_id.".cooperation_type.".$employment->cooperation_type_id."._preview_info")
                @if(in_array($employment->cooperation_type_id,[1,11]) )
                    @include("component.input._lable",["id"=>"user_id","label"=>"شماره پرسنلی ","value"=>$employment->worker->id??""])
                @endif
                @include("component.input._lable",["id"=>"status_id","label"=>"وضعیت درخواست همکاری ","value"=>$employment->get_status()])
                @include("component.input._lable",["id"=>"","label"=>"تاریخ ثبت درخواست","value"=>$employment->create_date()??""])

                @if(in_array($employment->cooperation_type_id,[1,11]) )
                    @include("component.input._lable",["id"=>"","label"=>"نام کاربری اینترنت","value"=>$employment->worker->internet_account_username??""])
                @endif


                @if($employment_document_types_for_personal)
                    @foreach($employment_document_types_for_personal as $employment_document_type)
                        <td>
                            <a href="{{route("hr.employment.admin.confirm.upload_document.download",[$employment,$employment_document_type])}}?{{rand(1,9999)}}">
                                {{$employment_document_type->document_type->caption}} ، </a></td>
                    @endforeach
                @endif
            </div>
        </div>

        @if($employment->status_personal_id == 4641401 && $confirm_info_permission && $employment->status_id!=4640109 )

            <a href="{{route('hr.employment.admin.dashboard.index')}}"
               class="btn btn btn-outline-dark ">بازگشت</a>
            <a class="btn btn btn-primary text-white"
               href="{{route("hr.employment.admin.confirm.personal_info.confirm",$employment)}}"
               onclick="return confirm('آیا از تایید اطلاعات شخصی اطمینان دارید؟')">تایید </a>
            <button class="btn btn-danger  md-trigger md-setperspective" data-modal="modal-15"
                    href="#!"><i class="fa fa-times"></i>عدم تایید برای اصلاح
            </button>
        @endif
    </div>

    @if($employment->personal_type_id==2)
        <div class="tab-pane fade {{$active_tab=='company_info'?"active  show":""}}" id="tab11" role="tabpanel"
             aria-labelledby="tab11-tab">
            @if($employment->status_company_id == 4641401  && $confirm_info_permission && $employment->status_id!=4640109)
                <p class="alert alert-warning">لطفاً اطلاعات با دقت بخوانید و ودر صورت عدم نیاز به اصلاح، تایید
                    نمایید. </p>
            @endif
            @include('hr.employment.admin.dashboard._company_info')

            @if($employment->status_company_id == 4641401  && $confirm_info_permission && $employment->status_id!=4640109)

                <a href="{{route('hr.employment.admin.dashboard.index')}}"
                   class="btn btn btn-outline-dark ">بازگشت</a>
                <a class="btn btn btn-primary text-white"
                   href="{{route("hr.employment.admin.confirm.company.confirm",$employment)}}"
                   onclick="return confirm('آیا از تایید اطلاعات شرکت اطمینان دارید؟')">تایید </a>
                <button class="btn btn-danger  md-trigger md-setperspective" data-modal="modal-14"
                        href="#!"><i class="fa fa-times"></i>عدم تایید برای اصلاح
                </button>
            @endif
        </div>
    @endif



@if(!in_array($employment->cooperation_type_id,[21,31,61]))
    <div class="tab-pane fade {{$active_tab=='address'?"active  show":""}}" id="tab1" role="tabpanel"
         aria-labelledby="tab1-tab">
        @if($employment->status_address_id == 4641401  && $confirm_info_permission && $employment->status_id!=4640109)
            @if(in_array($employment->cooperation_type_id,[1,11]) )
                <p class="alert alert-warning">لطفاً مدارک را با دقت مرور کرده و با متن ارائه شده مطابقت دهید. در
                    صورت عدم مغایرت، تایید نمایید. </p>
            @else
                <p class="alert alert-warning">لطفاً اطلاعات با دقت بخوانید و ودر صورت عدم نیاز به اصلاح، تایید
                    نمایید. </p>
            @endif
        @endif
        @include('hr.employment.register.address._preview',["panel_type"=>"admin"])

        @if($employment->status_address_id == 4641401  && $confirm_info_permission && $employment->status_id!=4640109)

            <a href="{{route('hr.employment.admin.dashboard.index')}}"
               class="btn btn btn-outline-dark ">بازگشت</a>
            <a class="btn btn btn-primary text-white"
               href="{{route("hr.employment.admin.confirm.address.confirm",$employment)}}"
               onclick="return confirm('آیا از تایید اطلاعات آدرس اطمینان دارید؟')">تایید </a>
            <button class="btn btn-danger  md-trigger md-setperspective" data-modal="modal-10"
                    href="#!"><i class="fa fa-times"></i>عدم تایید برای اصلاح
            </button>
        @endif
    </div>
    @endif
    @if(in_array($employment->cooperation_type_id,[1,11]) )
        <div class="tab-pane fade {{$active_tab=='academic_degree'?"active show":""}} " id="tab2"
             role="tabpanel"
             aria-labelledby="tab2-tab">
            @if($employment->status_academic_degree_id == 4641401  && $confirm_info_permission && $employment->status_id!=4640109)
                <p class="alert alert-warning">لطفاً مدارک را با دقت مرور کرده و با متن ارائه شده مطابقت دهید. در صورت
                    عدم مغایرت، تایید نمایید. </p>
            @endif
            @include("hr.employment.register.personal.academic_degree._list",["panel_type"=>"admin"])

            @if($employment->status_academic_degree_id == 4641401  && $confirm_info_permission && $employment->status_id!=4640109)

                <a href="{{route('hr.employment.admin.dashboard.index')}}"
                   class="btn btn btn-outline-dark ">بازگشت</a>
                <a class="btn btn btn-primary text-white"
                   href="{{route("hr.employment.admin.confirm.academic_degree.confirm",$employment)}}"
                   onclick="return confirm('آیا از تایید اطلاعات تحصیلی اطمینان دارید؟')">تایید </a>
                <button class="btn btn-danger  md-trigger md-setperspective" data-modal="modal-19"
                        href="#!"><i class="fa fa-times"></i>عدم تایید برای اصلاح
                </button>
            @endif

        </div>

        <div class="tab-pane fade {{$active_tab=='job_information'?"active show":""}} " id="tab3"
             role="tabpanel"
             aria-labelledby="tab3-tab">
            @if($employment->status_job_information_id == 4641401  && $confirm_info_permission&& $employment->status_id!=4640109)
                <p class="alert alert-warning">لطفاً مدارک را با دقت مرور کرده و با متن ارائه شده مطابقت دهید. در صورت
                    عدم مغایرت، تایید نمایید. </p>
            @endif
            @include("hr.employment.register.personal.job_information._list",["panel_type"=>"admin"])

            @if($employment->status_job_information_id == 4641401  && $confirm_info_permission&& $employment->status_id!=4640109)

                <a href="{{route('hr.employment.admin.dashboard.index')}}"
                   class="btn btn btn-outline-dark ">بازگشت</a>
                <a class="btn btn btn-primary text-white"
                   href="{{route("hr.employment.admin.confirm.job_information.confirm",$employment)}}"
                   onclick="return confirm('آیا از تایید اطلاعات شغلی اطمینان دارید؟')">تایید </a>
                <button class="btn btn-danger  md-trigger md-setperspective" data-modal="modal-13"
                        href="#!"><i class="fa fa-times"></i>عدم تایید برای اصلاح
                </button>
            @endif
        </div>

        <div class="tab-pane fade {{$active_tab=='educational_course'?"active show":""}}" id="tab5"
             role="tabpanel"
             aria-labelledby="tab5-tab">
            @if($employment->status_educational_course_id == 4641401 && $confirm_info_permission && $employment->status_id!=4640109)
                <p class="alert alert-warning">لطفاً مدارک را با دقت مرور کرده و با متن ارائه شده مطابقت دهید. در صورت
                    عدم مغایرت، تایید نمایید. </p>
            @endif
            @include("hr.employment.register.personal.educational_course._list",["panel_type"=>"admin"])

            @if($employment->status_educational_course_id == 4641401 && $confirm_info_permission && $employment->status_id!=4640109)

                <a href="{{route('hr.employment.admin.dashboard.index')}}"
                   class="btn btn btn-outline-dark ">بازگشت</a>
                <a class="btn btn btn-primary text-white"
                   href="{{route("hr.employment.admin.confirm.educational_course.confirm",$employment)}}"
                   onclick="return confirm('آیا از تایید دوره های اموزشی اطمینان دارید؟')">تایید </a>
                <button class="btn btn-danger  md-trigger md-setperspective" data-modal="modal-16"
                        href="#!"><i class="fa fa-times"></i>عدم تایید برای اصلاح
                </button>
            @endif
        </div>
        <div class="tab-pane fade {{$active_tab=='dependent'?"active show":""}}" id="tab10"
             role="tabpanel"
             aria-labelledby="tab5-tab">
            @if($employment->status_dependent_id == 4641401 && $confirm_info_permission && $employment->status_id!=4640109)
                <p class="alert alert-warning">لطفاً مدارک را با دقت مرور کرده و با متن ارائه شده مطابقت دهید. در
                    صورت عدم مغایرت، تایید نمایید. </p>
            @endif
            @include("hr.employment.register.personal.dependent._list",["panel_type"=>"admin"])

            @if($employment->status_dependent_id == 4641401 && $confirm_info_permission && $employment->status_id!=4640109)

                <a href="{{route('hr.employment.admin.dashboard.index')}}"
                   class="btn btn btn-outline-dark ">بازگشت</a>
                <a class="btn btn btn-primary text-white"
                   href="{{route("hr.employment.admin.confirm.dependent.confirm",$employment)}}"
                   onclick="return confirm('آیا از تایید افراد تحت تکفل اطمینان دارید؟')">تایید </a>
                <button class="btn btn-danger  md-trigger md-setperspective" data-modal="modal-17"
                        href="#!"><i class="fa fa-times"></i>عدم تایید برای اصلاح
                </button>
            @endif
        </div>
        <div class="tab-pane fade {{$active_tab=='financial_information'?"active show":""}}" id="tab11"
             role="tabpanel"
             aria-labelledby="tab11-tab">
            <h5>مرکز هزینه</h5>
            @include("hr.employment.admin.personal.financial_information._preview")
            <h5>اطلاعات بانکی</h5>
            @include("hr.employment.register.personal.bank_information._list",["panel_type"=>"admin"])


        </div>
        @if(!in_array($employment->cooperation_type_id,[21,31,61]))
        <div class="tab-pane fade {{$active_tab=='other'?"active show":""}}" id="tab6" role="tabpanel"
             aria-labelledby="tab6-tab">
            توضیحات:

            @if($employment->description)
                @include("component.input._lable", ["id"=>"description", 'label'=>"توضیحات بیشتر",    "value"=>$employment->description??"", "class_col"=>""])
            @else
                <p class="alert alert-warning">کاربر هیچ توضیحاتی ثبت نکرده است.</p>
            @endif
        </div>
        @endif
        <div class="tab-pane fade {{$active_tab=='document'?"active show":""}}" id="tab7" role="tabpanel"
             aria-labelledby="tab7-tab">
            @include("hr.employment.register.personal.upload_final_document._list")
        </div>
    @endif
    <div class="tab-pane fade {{$active_tab=='employment_process'?"active show":""}}" id="tab8" role="tabpanel"
         aria-labelledby="tab8-tab">
        @include("hr.employment.admin.dashboard._employment_process")
    </div>
    <div class="tab-pane fade {{$active_tab=='employment_log'?"active show":""}}" id="tab9" role="tabpanel"
         aria-labelledby="tab9-tab">
        @include('hr.employment.admin.dashboard._employment_log')

    </div>
    <div class="tab-pane fade " id="tab6" role="tabpanel"
         aria-labelledby="tab6-tab">
        @if($employment->description)
            @include("component.input._lable", ["id"=>"description", 'label'=>"توضیحات بیشتر",    "value"=>$employment->description??"", "class_col"=>""])
        @else
            <p class="alert  alert-warning">کاربر هیچ توضیحاتی ثبت نکرده است.</p>
        @endif
    </div>



