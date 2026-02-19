@extends('hr.employment.register.layout._layout',["title_caption"=>"بارگذاری مدارک"])

@section('content')

    <div class="col-md-12 content-class">
        <form id="form1" method="post"
              action="{{route('hr.employment.register.personal.confirm_upload_document.submit',$employment->key)}}"
              enctype="multipart/form-data" autocomplete="false">
            @csrf
            <p>لطفا با کلیک بر روی هر لینک، مدارک مرتبط با ان را بارگزاری و پس از تکمیل بارگزاری تایید نمایید.</p>
            <div class="table-responsive">
                <table class="table table-styling">
                    <thead>
                    <tr>
                        <th> #</th>
                        <th> عنوان</th>

                    </tr>
                    @php $all_checked=1;@endphp
                    </thead>
                    <tbody>
                    @php $row=0;@endphp
                    @if($employment->post->post_document_receive_step_confirms()->where('receive_document_step_id',1)->where('confirm_type',2)->exists())
                        <tr>
                            <td>{{++$row}}</td>
                            <td>
                                @if(array_sum($document_personal_upload[1])>0)

                                    <i class="fas fa-check-square"></i>
                                @else
                                    @php $all_checked=0;@endphp
                                    <i class="fas fa-minus-square"></i>
                                @endif
                                <a style="color:#04a9f5"
                                   href="{{route("hr.employment.register.personal_info.upload",$employment->key)}}">بارگذاری
                                    مدارک شخصی</a>
                            </td>
                        </tr>
                    @endif
                    @if($employment->post->post_document_receive_step_confirms()->where('receive_document_step_id',2)->where('confirm_type',2)->exists())
                        <tr>

                            <td>{{++$row}}</td>
                            <td>
                                @if(array_sum($document_address_upload[2])>0)

                                    <i class="fas fa-check-square"></i>
                                @else
                                    @php $all_checked=0;@endphp
                                    <i class="fas fa-minus-square"></i>
                                @endif
                                <a style="color:#04a9f5"
                                   href="{{route("hr.employment.register.address.upload",$employment->key)}}">بارگذاری
                                    مدارک آدرس</a></td>
                        </tr>
                    @endif
                    @if($has_academic_degree_in_step2->count()>0 )
                        <tr>
                            <td>{{++$row}}</td>
                            <td>
                                @if($academic_degree_in_step2_upload->count()>=$has_academic_degree_in_step2->count() && $academic_degree_in_step2_upload->count()!=0 )

                                    <i class="fas fa-check-square"></i>
                                @else
                                    @php $all_checked=0;@endphp
                                    <i class="fas fa-minus-square"></i>
                                @endif

                                <a style="color:#04a9f5"
                                   href="{{route("hr.employment.register.personal.academic_degree.index",$employment->key)}}">بارگذاری
                                    مدارک تحصیلی</a></td>

                        </tr>
                    @endif
                    @if($employment->post->post_document_receive_step_confirms()->where('receive_document_step_id',8)->where('confirm_type',2)->exists()
                          && $employment->worker->user_job_informations()->count()>0)
                        <tr>
                            <td>{{++$row}}</td>
                            <td>
                                @if(array_sum($document_job_information_upload[8])>0)
                                    <i class="fas fa-check-square"></i>
                                @else
                                    @php $all_checked=0;@endphp
                                    <i class="fas fa-minus-square"></i>
                                @endif

                                <a style="color:#04a9f5"
                                   href="{{route("hr.employment.register.personal.job_information.index",$employment->key)}}">بارگذاری
                                    مدارک شغلی</a></td>
                        </tr>
                    @endif
                    @if(
                        $employment->post->post_document_receive_step_confirms()->
                                where('receive_document_step_id',9)->where('confirm_type',2)->exists() &&
                        $employment->worker->user_educational_courses()->count() > 0

                        )
                        <tr>
                            <td>{{++$row}}</td>
                            <td>
                                @if(array_sum($document_educational_course_upload[9])>0)

                                    <i class="fas fa-check-square"></i>
                                @else
                                    @php $all_checked=0;@endphp
                                    <i class="fas fa-minus-square"></i>
                                @endif
                                <a style="color:#04a9f5"
                                   href="{{route("hr.employment.register.personal.educational_course.index",$employment->key)}}">بارگذاری
                                    مدارک دوره های آموزشی</a></td>
                        </tr>
                    @endif
                    @if(
                               $employment->post->post_document_receive_step_confirms()->
                                       where('receive_document_step_id',12)->where('confirm_type',2)->exists() &&
                               $employment->worker->user_dependents()->count() > 0

                               )
                        <tr>
                            <td>{{++$row}}</td>
                            <td>
                                @if(array_sum($document_user_dependent_upload[12])>0)

                                    <i class="fas fa-check-square"></i>
                                @else
                                    @php $all_checked=0;@endphp
                                    <i class="fas fa-minus-square"></i>
                                @endif

                                <a style="color:#04a9f5"
                                   href="{{route("hr.employment.register.personal.dependent.index",$employment->key)}}">بارگذاری
                                    مدارک افراد تحت تکفل</a></td>
                        </tr>
                    @endif
                    </tbody>

                </table>
            </div>
            <div class="center">
                <br/>

                <a class="btn  mb-4" href="{{route("hr.employment.register.start.index",$employment->key)}}">بازگشت</a>
                @if($all_checked==1)
                    <button class="btn btn-primary shadow-2 mb-4">تایید</button>
                @endif
                <br/>
            </div>

        </form>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
