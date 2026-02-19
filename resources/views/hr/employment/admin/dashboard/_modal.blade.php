@section("modals")

    @include("component.modal.md-modal._modal_input",[
            "id"=>"15",
            "theme"=>"-danger",
            "title"=>" آیا از عدم تایید اطلاعت شخصی اطمینان دارید؟ ",
            "content"=>view("hr.employment.admin.confirm.reject")->render(),
            "btn_class"=>"btn-danger",
            "btn_title"=>"",
            "url"=>route("hr.employment.admin.confirm.personal_info.reject",$employment)
            ])
    @include("component.modal.md-modal._modal_input",[
            "id"=>"10",
            "theme"=>"-danger",
            "title"=>" آیا از عدم تاییداطلاعات آدرس اطمینان دارید؟ ",
            "content"=>view("hr.employment.admin.confirm.reject")->render(),
            "btn_class"=>"btn-danger",
            "btn_title"=>"",
            "url"=>route("hr.employment.admin.confirm.address.reject",$employment)
            ])
    @include("component.modal.md-modal._modal_input",[
               "id"=>"19",
               "theme"=>"-danger",
               "title"=>" آیا از عدم تاییداطلاعات تحصیلی اطمینان دارید؟ ",
               "content"=>view("hr.employment.admin.confirm.reject")->render(),
               "btn_class"=>"btn-danger",
               "btn_title"=>"",
               "url"=>route("hr.employment.admin.confirm.academic_degree.reject",$employment)
                ])
    @include("component.modal.md-modal._modal_input",[
            "id"=>"13",
            "theme"=>"-danger",
            "title"=>" آیا از عدم تاییداطلاعات شغلی اطمینان دارید؟ ",
            "content"=>view("hr.employment.admin.confirm.reject")->render(),
            "btn_class"=>"btn-danger",
            "btn_title"=>"",
            "url"=>route("hr.employment.admin.confirm.job_information.reject",$employment)
             ])

    @include("component.modal.md-modal._modal_input",[
        "id"=>"16",
        "theme"=>"-danger",
        "title"=>" آیا از عدم تایید دوره های آموزشی اطمینان دارید؟ ",
        "content"=>view("hr.employment.admin.confirm.reject")->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"",
        "url"=>route("hr.employment.admin.confirm.educational_course.reject",$employment)
         ])

    @include("component.modal.md-modal._modal_input",[
        "id"=>"17",
        "theme"=>"-danger",
        "title"=>" آیا از عدم تایید افراد تحت تکفل اطمینان دارید؟ ",
        "content"=>view("hr.employment.admin.confirm.reject")->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"",
        "url"=>route("hr.employment.admin.confirm.dependent.reject",$employment)
         ])
    @include("component.modal.md-modal._modal_input",[
        "id"=>"14",
        "theme"=>"-danger",
        "title"=>" آیا از عدم تایید اطلاعات شرکت اطمینان دارید؟ ",
        "content"=>view("hr.employment.admin.confirm.reject")->render(),
        "btn_class"=>"btn-danger",
        "btn_title"=>"",
        "url"=>route("hr.employment.admin.confirm.company.reject",$employment)
         ])
@endsection