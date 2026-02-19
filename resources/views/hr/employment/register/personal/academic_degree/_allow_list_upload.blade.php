
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
                                @foreach($document_receive_step_document_type_list as  $item)
                                    {{ $item->document_type->caption }}
                                @endforeach
                        @endif
                    </th>
                    <th></th>



                </tr>
                </thead>
                <tbody>
                @php $row=0;@endphp
                @foreach($allow_user_academic_degree_upload as $user_academic_degree)
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

                                    <a href="{{route("hr.employment.register.personal.academic_degree.upload",[$employment->key,$user_academic_degree->user_academic_degree_id])}}"><i
                                                class="fa fa-upload "></i> </a>

                            @endif
                        </td>

{{--                        @php--}}
{{--                            $employment_document_type=$user_academic_degree->user_academic_degree_id->get_employment_document_type();--}}
{{--                        @endphp--}}
                        <td>
                            @if(isset($employment_document_types[$user_academic_degree->user_academic_degree_id]))
                                <a href="{{ route('hr.employment.register.personal.academic_degree.download', [$employment->key, $employment_document_types[$user_academic_degree->user_academic_degree_id]->id]) }}">
                                    <i class="fa fa-download"></i>
                                </a>
                            @endif
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div><br/>


        <a class="btn  mb-4" href="{{route("hr.employment.register.personal.confirm_upload_document.index",$employment->key)}}">بازگشت</a>

