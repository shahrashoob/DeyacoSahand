<h5>
    اطلاعات شخصی
    <hr/>
</h5>

<div >
@include("hr.employment.register.personal_type.".$employment->personal_type_id.".cooperation_type.".$employment->cooperation_type_id."._preview_info")
</div>

<br/>

<h5>
    آدرس
    <hr/>
</h5>

@include("hr.employment.register.address._preview",["panel_type"=>"register"])

@if($user_academic_degrees->count()>0)
    <h5>
        اطلاعات تحصیلی

    </h5>
    @include("hr.employment.register.personal.academic_degree._list",["panel_type"=>"register"])
@endif

@if($user_job_informations->count()>0)
    <h5>

        اطلاعات شغلی
    </h5>
    @include("hr.employment.register.personal.job_information._list",["panel_type"=>"register"])
@endif

@if($user_educational_courses->count()>0)
    <h5>
        اطلاعات دوره های آموزشی
    </h5>
    @include("hr.employment.register.personal.educational_course._list",["panel_type"=>"register"])
@endif
@if($user_dependents->count()>0)
    <h5>
        اطلاعات افراد تحت تکفل
    </h5>
    @include("hr.employment.register.personal.dependent._list",["panel_type"=>"register"])
@endif


@if($employment->description)

    <h5>
        سایر توضیحات
        <hr/>
    </h5>

    <div class="col-md-12">
        {{$employment->description??""}}
    </div>
@endif

<br/>
<br/>
