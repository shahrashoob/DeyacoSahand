
@if($employment->worker->user_job_informations->count()>=1)
    <div class="col-md-12 center ">
    <div class="table-responsive">
        <table class="table table-styling">
            <thead>
            <tr>
                <th> ردیف</th>
                <th>پست سازمانی</th>
                <th>نام شرکت</th>
                <th>تاریخ شروع کار</th>
                <th>تاریخ پایان کار</th>
                <th>نام معرف</th>
                <th>شماره معرف</th>
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
            @foreach($employment->worker->user_job_informations as $job_information)
                <tr>
                    <td>{{++$row}}</td>
                    <td>{{$job_information->post_caption}}</td>
                    <td>{{$job_information->company_name_of_work}}</td>
                    <td>{{$job_information->get_start_date_of_work()}}</td>
                    <td>{{$job_information->get_end_date_of_work()}}</td>
                    <td>{{$job_information->identifier_name}}</td>
                    <td>{{$job_information->identification_number}}</td>
                    <td>
                        @if($allow_show_upload)
                            @if(in_array($employment->status_id,[4640107,4640108]))
                            <a href="{{route("hr.employment.register.personal.job_information.upload",[$employment->key,$job_information])}}"><i
                                        class="fa fa-upload "></i> </a>
                        @endif
                        @endif
                    </td>
                    @php
                    $employment_document_type=$job_information->get_employment_document_type();
                    @endphp
                    @if($panel_type=='register')
                    @if($employment_document_type)
                        <td><a href="{{route("hr.employment.register.personal.job_information.download",[$employment,$employment_document_type])}}">
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
                        <a href="{{route("hr.employment.register.personal.job_information.destroy",[$employment->key,$job_information->id])}}"
                           onclick="return confirm('آیا از حذف سابقه شغلی اطمینان دارید؟')"><i
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
    <p class="alert alert-warning"> هیچ اطلاعاتی ثبت نشده است.</p>
@endif

