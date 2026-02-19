@if($employment->worker->user_academic_degrees->count()>=1 && $employment->status_id!=4640107 )
    <div class="col-md-12 center ">
        <div class="table-responsive ">
            <table class="table table-styling">
                <thead>
                <tr>
                    <th> ردیف</th>
                    <th> دوره تحصیلی</th>
                    <th>رشته تحصیلی</th>
                    <th>نام موسسه</th>
                    <th>معدل</th>
                    <th>تاریخ شروع</th>
                    <th>تاریخ پایان</th>

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
                @foreach($employment->worker->user_academic_degrees as $user_academic_degree)
                    <tr>
                        <td>{{++$row}}</td>
                        <td>{{$user_academic_degree->academic_degree_type->caption??""}}</td>
                        <td>{{$user_academic_degree->feild_of_academic_degree->caption??""}}</td>
                        <td>{{$user_academic_degree->name_of_academic_degree}}</td>
                        <td>{{$user_academic_degree->average}}</td>
                        <td>{{$user_academic_degree->get_start_date()}}</td>
                        <td>{{$user_academic_degree->get_end_date()}}</td>

                        <td>
                            @if($allow_show_upload)
                                @if(in_array($employment->status_id,[4640107,4640108]))
                                    <a href="{{route("hr.employment.register.personal.academic_degree.upload",[$employment->key,$user_academic_degree])}}"><i
                                                class="fa fa-upload "></i> </a>
                                @endif
                            @endif
                        </td>

                        @php
                        $employment_document_type=$user_academic_degree->get_employment_document_type();
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
                                <a href="{{route("hr.employment.register.personal.academic_degree.destroy",[$employment->key,$user_academic_degree->id])}}"
                                   onclick="return confirm('آیا از حذف اطلاعات تحصیلی اطمینان دارید?')"><i
                                            class="fa fa-trash text-danger"></i> </a>
                            @endif
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div><br/>
@elseif( $allow_user_academic_degree_upload->count()>=1 && $employment->status_id==4640107 && $allow_show_upload )
    @include("hr.employment.register.personal.academic_degree._allow_list_upload")

@else
    <p class="alert alert-warning"> هیچ اطلاعاتی ثبت نشده است.</p>
    <a class="btn  mb-4" href="{{route("hr.employment.register.personal.confirm_upload_document.index",$employment->key)}}">بازگشت</a>

@endif

