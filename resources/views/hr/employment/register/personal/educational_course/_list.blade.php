@if($employment->worker->user_educational_courses->count()>=1)
    <div class="col-md-12 center ">
    <div class="table-responsive">
        <table class="table table-styling">
            <thead>
            <tr>
                <th>ردیف</th>
                <th>نام دوره</th>
                <th>نام موسسه</th>
                <th> تاریخ شروع دوره</th>
                <th>تاریخ پابان دوره</th>
                <th>مدت دوره</th>
                <th>
                    @if($allow_show_upload)
                        @if(in_array($employment->status_id,[4640107,4640108]))
                        @foreach($document_receive_step_document_type_list as  $item)
                            {{ $item->document_type->caption }}
                        @endforeach
                    @endif
                    @endif
                </th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @php $row=0;@endphp
            @foreach($employment->worker->user_educational_courses as $user_educational_course)
                <tr>
                    <td>{{++$row}}</td>
                    <td>{{$user_educational_course->course_name}}</td>
                    <td>{{$user_educational_course->name_of_institution}}</td>
                    <td>{{$user_educational_course->get_start_date()}}</td>
                    <td>{{$user_educational_course->get_end_date()}}</td>
                    <td>{{$user_educational_course->duration}}</td>
                    <td>
                        @if($allow_show_upload)
                        @if(in_array($employment->status_id,[4640107,4640108]))
                            <a href="{{route("hr.employment.register.personal.educational_course.upload",[$employment->key, $user_educational_course])}}"><i
                                        class="fa fa-upload "></i> </a>
                        @endif
                        @endif
                    </td>
                    @php
                    $employment_document_type=$user_educational_course->get_employment_document_type();
                    @endphp
                    @if($panel_type=='register')
                    @if($employment_document_type)
                        <td><a href="{{route("hr.employment.register.personal.academic_degree.download",[$employment,$employment_document_type])}}">
                                <i class="fa fa-download "></i> </a></td>
                    @endif
                    @endif
                    @if($panel_type=='admin')
                        @if($employment_document_type)
                            <td><a href="{{route("hr.employment.admin.confirm.upload_document.download",[$employment,$employment_document_type])}}">
                                    <i class="fa fa-download "></i> </a></td>
                        @endif
                    @endif
                    <td>
                        @if($allow_delete)
                        <a href="{{route("hr.employment.register.personal.educational_course.destroy",[$employment->key,$user_educational_course->id])}}"
                           onclick="return confirm('آیا از حذف دوره تحصیلی اطمینان دارید؟')"><i
                                class="fa fa-trash text-danger"></i> </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    </div><br/>

@else
    <p class=" alert alert-warning">هیچ اطلاعاتی ثبت نشده است.</p>
@endif

