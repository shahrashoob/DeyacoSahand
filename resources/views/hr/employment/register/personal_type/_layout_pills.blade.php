<div class="row">
    <div class="col-md-3 ">
        <img style="width: 200px"
             src="{{asset("chatify_app/users-avatar/".($employment->worker->image->filename??''))}}"
             onerror="this.onerror=null;this.src='{{url("assets/images/avatar.png")}}';"
        />
        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" style="box-shadow: none"
            aria-orientation="vertical">
            @if($employment->status_id==4640107)
                @if($employment->post->post_document_receive_step_confirms()->where('receive_document_step_id',1)->where('confirm_type',2)->exists())
                    <li><a class="nav-link {{$panel_name=="personal_info"?"active":""}} "
                           href="{{route("hr.employment.register.personal_info.upload",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">مدارک فردی</a></li>
                @endif
                @if($employment->post->post_document_receive_step_confirms()->where('receive_document_step_id',2)->where('confirm_type',2)->exists())
                    <li><a class="nav-link {{$panel_name=="address"?"active":""}} "
                           href="{{route("hr.employment.register.address.upload",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">مدارک آدرس</a></li>
                @endif
                @if($employment->post->post_document_receive_step_confirms()->whereIn('receive_document_step_id',[3,4,5,6,7])->where('confirm_type',2)->exists()  && $employment->worker->user_academic_degrees()->count()>0)
                    <li><a class="nav-link {{$panel_name=="academic_degree"?"active":""}} "
                           href="{{route("hr.employment.register.personal.academic_degree.index",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">مدارک تحصیلی</a></li>
                @endif
                @if($employment->post->post_document_receive_step_confirms()->where('receive_document_step_id',8)->where('confirm_type',2)->exists()  && $employment->worker->user_job_informations()->count()>0)
                    <li><a class="nav-link {{$panel_name=="job_information"?"active":""}} "
                           href="{{route("hr.employment.register.personal.job_information.index",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">مدارک شغلی</a></li>
                @endif
                @if($employment->post->post_document_receive_step_confirms()->where('receive_document_step_id',9)->where('confirm_type',2)->exists() && $employment->worker->user_educational_courses()->count() > 0)
                    <li><a class="nav-link {{$panel_name=="educational_course"?"active":""}} "
                           href="{{route("hr.employment.register.personal.educational_course.index",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">مدارک دوره های آموزشی</a></li>
                @endif
                @if($employment->post->post_document_receive_step_confirms()->where('receive_document_step_id',12)->where('confirm_type',2)->exists() && $employment->worker->user_dependents()->count() > 0
                    &&$employment->nationality_id==1 )
                    <li><a class="nav-link {{$panel_name=="dependent"?"active":""}}"
                           href="{{route("hr.employment.register.personal.dependent.index",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">افراد تحت تکفل</a></li>
                @endif
                @if($employment->post->post_document_receive_step_confirms()->where('receive_document_step_id',9)->where('confirm_type',2)->exists() && $employment->worker->user_educational_courses()->count() > 0)
                    <li><a class="nav-link "
                           href="{{route("hr.employment.register.personal.confirm_upload_document.index",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">تایید بارگزاری مدارک</a></li>
                @endif

            @endif
            @if($employment->status_id==4640113)
                <li><a class="nav-link {{$panel_name=="confirm_drafting_contract"?"active":""}} "
                       href="{{route("hr.employment.register.supplier.confirm_drafting_contract.index",$employment->key)}}"
                       role="tab"
                       aria-controls="v-pills-educational"
                       aria-selected="false">تایید قرارداد</a></li>
            @endif
                @if($employment->status_id==4640123)
                    <li><a class="nav-link {{$panel_name=="confirm_drafting_contract_customer"?"active":""}} "
                           href="{{route("hr.employment.register.customer.confirm_drafting_contract.index",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">تایید قرارداد</a></li>
                @endif
                @if($employment->status_id==4640132)
                    <li><a class="nav-link {{$panel_name=="confirm_drafting_contract_contractor"?"active":""}} "
                           href="{{route("hr.employment.register.contractor.confirm_drafting_contract.index",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">تایید قرارداد</a></li>
                @endif
            @if(in_array($employment->status_id,[4640115,4640118,4640121]))
                @if($employment->worker->absorption_type_id==1)
                    <li><a class="nav-link {{$panel_name=="work_medicine"?"active":""}} "
                           href="{{route("hr.employment.register.personal.work_medicine.index",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false"> طب کار </a></li>
                @endif
                <li><a class="nav-link {{$panel_name=="bank_information"?"active":""}} "
                       href="{{route("hr.employment.register.personal.bank_information.index",$employment->key)}}"
                       role="tab"
                       aria-controls="v-pills-educational"
                       aria-selected="false"> اطلاعات مالی </a></li>
                <li><a class="nav-link {{$panel_name=="confirm_drafting_contract_personal"?"active":""}} "
                       href="{{route("hr.employment.register.personal.confirm_drafting_contract.index",$employment->key)}}"
                       role="tab"
                       aria-controls="v-pills-educational"
                       aria-selected="false">تایید قرارداد</a></li>
            @endif
            @if(!in_array($employment->status_id,[4640113,4640115,4640118,4640107,4640121,4640123,4640132]) )
                <li><a class="nav-link  {{$panel_name=="personal_info"?"active":""}}"
                       href="{{route("hr.employment.register.personal_info.index",$employment->key)}}" role="tab"
                       aria-controls="v-pills-home"
                       aria-selected="true">اطلاعات
                        اولیه</a></li>


                <li><a class="nav-link {{$panel_name=="address"?"active":""}} "
                       href="{{route("hr.employment.register.address.index",$employment->key)}}" role="tab"
                       aria-controls="v-pills-address"
                       aria-selected="false">
                        @if($employment->personal_type_id == 1)
                        اطلاعات تماس و آدرس
                        @else
                         اطلاعات تماس و آدرس شرکت
                        @endif
                    </a></li>

                    @if($employment->cooperation_type_id==3 && $employment->personal_type_id==2 )
                        <li><a class="nav-link  {{$panel_name=="agent_customer"?"active":""}}"
                               href="{{route("hr.employment.register.customer.agent.index",$employment->key)}}"
                               role="tab"
                               aria-controls="v-pills-agent"
                               aria-selected="false">نمایندگان</a></li>
                    @endif
                    @if($employment->cooperation_type_id==6 && $employment->personal_type_id==2 )
                        <li><a class="nav-link  {{$panel_name=="agent_supplier"?"active":""}}"
                               href="{{route("hr.employment.register.supplier.agent.index",$employment->key)}}"
                               role="tab"
                               aria-controls="v-pills-agent"
                               aria-selected="false">نمایندگان</a></li>
                    @endif
                    @if($employment->cooperation_type_id==2 && $employment->personal_type_id==2 )
                        <li><a class="nav-link  {{$panel_name=="agent_contractor"?"active":""}}"
                               href="{{route("hr.employment.register.contractor.agent.index",$employment->key)}}"
                               role="tab"
                               aria-controls="v-pills-agent"
                               aria-selected="false">نمایندگان</a></li>
                    @endif
                    @if(in_array($employment->cooperation_type_id,[1,11]) )
                    <li><a class="nav-link  {{$panel_name=="academic_degree"?"active":""}}"
                           href="{{route("hr.employment.register.personal.academic_degree.index",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">اطلاعات تحصیلی</a></li>

                    <li><a class="nav-link  {{$panel_name=="job_information"?"active":""}}"
                           href="{{route("hr.employment.register.personal.job_information.index",$employment->key)}}"
                           role="tab"
                           aria-controls="v-pills-educational"
                           aria-selected="false">سابقه شغلی</a></li>


                    <li><a class="nav-link  {{$panel_name=="educational_course"?"active":""}}"
                           href="{{route("hr.employment.register.personal.educational_course.index",$employment->key)}}"
                           role="tab" aria-controls="v-pills-educational"
                           aria-selected="false">دوره های آموزشی</a></li>
                    @if( !empty($employment->worker))
                        @if(!empty($employment->worker->user_address()->first()))
                            @if( ($employment->nationality_id==1 &&$employment->worker->user_address()->first()->address->country_id==112 )||
                         ($employment->nationality_id==2 &&$employment->worker->user_address()->first()->address->country_id==112))
                                <li><a class="nav-link  {{$panel_name=="dependent"?"active":""}}"
                                       href="{{route("hr.employment.register.personal.dependent.index",$employment->key)}}"
                                       role="tab" aria-controls="v-pills-educational"
                                       aria-selected="false">اطلاعات افراد تحت تکفل</a></li>
                            @endif
                        @endif
                    @endif
                    @if(!empty($employment->worker->gender_id))
                        @if($employment->worker->gender_id==1 && $employment->nationality_id==1 )
                            <li><a class="nav-link  {{$panel_name=="military_information"?"active":""}}"
                                   href="{{route("hr.employment.register.personal.military_information.index",$employment->key)}}"
                                   role="tab" aria-controls="v-pills-educational"
                                   aria-selected="false">اطلاعات سربازی</a></li>
                        @endif


                        {{--                    @if($employment->status_id!=4640108)--}}
                        {{--                        @if($employment->post && $employment->post->post_document_types()->where('document_delivery_type',1)->count()>=1 )--}}
                        {{--                            <li><a class="nav-link  {{$panel_name=="upload_primary_document"?"active":""}}"--}}
                        {{--                                   href="{{route("hr.employment.register.upload_primary_document.index",$employment->key)}}"--}}
                        {{--                                   role="tab" aria-controls="v-pills-educational"--}}
                        {{--                                   aria-selected="false">بارگزاری مدارک اولیه</a></li>--}}
                        {{--                        @endif--}}
                        {{--                        @endif--}}
                    @endif
                @endif

                <li><a class="nav-link {{$panel_name=="other"?"active":""}} "
                       href="{{route("hr.employment.register.other.index",$employment->key)}}" role="tab"
                       aria-controls="v-pills-educational"
                       aria-selected="false">سایر توضیحات</a></li>


                <li><a class="nav-link {{$panel_name=="confirm_information"?"active":""}} "
                       href="{{route("hr.employment.register.confirm_information.index",$employment->key)}}"
                       role="tab"
                       aria-controls="v-pills-educational"
                       aria-selected="false">تایید نهایی</a></li>
            @endif
        </ul>

    </div>
    <div class="col-md-9 content-class">
        <div style="box-shadow: 0 3px 10px 0 rgba(0,0,0,.05)">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                     aria-labelledby="v-pills-home-tab">
                    <div class="col-sm-11">
                        <div style="margin-top: -20px;margin-bottom: 15px ">

                            @yield("content_pill")

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
