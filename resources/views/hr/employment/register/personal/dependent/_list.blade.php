@if($employment->worker->user_dependents->count()>=1)
    <div class="col-md-12 center ">
        <div class="table-responsive ">
            <table class="table table-styling">
                <thead>
                <tr>
                    <th> ردیف</th>

                    <th>نوع ارتباط با فرد</th>
                    <th>نام و نام خانوادگی</th>
                    <th>{{($employment->nationality_id==2 &&$employment->worker->user_address()->first()->address->country_id==112) ?"کد فراگیر" :"کدملی"}}</th>
                    <th>تاریخ تولد</th>
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
                @foreach($employment->worker->user_dependents as $user_dependent)
                    <tr>
                        <td>{{++$row}}</td>

                        <td>{{$user_dependent->dependent_type->caption??""}}</td>
                        <td>{{$user_dependent->fullname()}}</td>
                        <td>{{$user_dependent->national_code}}</td>
                        <td>{{$user_dependent->get_date_of_birth()}}</td>

                        <td>
                            @if($allow_show_upload)
                                @if(in_array($employment->status_id,[4640107,4640108]))
                                    <a href="{{route("hr.employment.register.personal.dependent.upload",[$employment->key, $user_dependent])}}"><i
                                                class="fa fa-upload "></i> </a>
                                @endif
                            @endif
                        </td>
                        @php
                            $employment_document_type= $user_dependent->get_employment_document_type();
                        @endphp
                        @if($panel_type=='register')
                            @if($employment_document_type)
                                <td><a href="{{route("hr.employment.register.personal.dependent.download",[$employment,$employment_document_type])}}">
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
                        <td>
                            @if($allow_delete)
                                <a href="{{route("hr.employment.register.personal.dependent.destroy",[$employment->key,$user_dependent->id])}}"
                                   onclick="return confirm('آیا از حذف اطلاعات فرد تحت تکفل اطمینان دارید?')"><i
                                            class="fa fa-trash text-danger"></i> </a>
                            @endif
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <p class="alert alert-warning">هیچ اطلاعاتی ثبت نشده است.</p>
@endif

