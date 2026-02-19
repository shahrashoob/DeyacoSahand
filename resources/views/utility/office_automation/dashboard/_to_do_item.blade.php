<div class="col-md-12 col-sm-12">
    <div class="card card-border-c-blue">
        <div class="card-header">
            <a href="{{route("utility.office_automation.dashboard.view",[$to_do->office_automation_work_id,$current_user->id])}}">
                کد
                {{$to_do->getCode()}}
            </a>
            <span class="label label-primary float-right"> {{$to_do->status->caption}} </span>
{{--            <a href="{{route("utility.office_automation.dashboard.print_office",[$to_do->office_automation_work_id,$to_do->office_automation_work->random,$to_do])}}" class=" label float-right"> <i class="fa fa-print"></i> </a>--}}
{{--            <a href="{{route("utility.office_automation.dashboard.download_office",[$to_do->office_automation_work_id,$to_do->office_automation_work->random,$to_do])}}" class=" label float-right"> <i class="fa fa-download"></i> </a>--}}
        </div>
        <div class="card-block">

            <div class="col-sm-12">
                <div class="row">
                    <div class="col-md-12">
                        <label>عنوان:
                            {{$to_do->caption}}</label>
                    </div>
                    <div class="col-md-12">
                        <label>
                            ایجاد کننده:
                            {{$to_do->worker->fullName()}}
                        </label>
                    </div>
                    <div class="col-md-12">
                        <label>
                            تاریخ ایجاد:
                            {{$to_do->get_create_date_and_time()}}
                        </label>
                    </div>
                    {{--                    <div class="col-md-12">--}}
                    {{--                        <label>--}}
                    {{--                            وضعیت:--}}
                    {{--                            {{$to_do->status->caption}}--}}
                    {{--                        </label>--}}
                    {{--                    </div>--}}
                    <div class="col-md-12">
                        <label>
                            تاریخ پایان:
                            {{$to_do->end_datetime()}}
                        </label>
                    </div>
                    <div class="col-md-12">
                        <label>
                            اولویت:
                            {!! $to_do->priority->getHtml() !!}
                        </label>
                    </div>


                    <div class="col-md-12">
                        <label>
                            اقدام کننده:
                            @if($to_do->showAllLogs($current_user->id))
                                @php $to_do_actions=$to_do->actions;@endphp
                                @foreach($to_do_actions as $action)

                                    @if($action->to_do_list_child()->count() !=0 )
                                        <a href="{{route("utility.office_automation.dashboard.view",[$action->office_automation_work_id,$action->user_id,$current_user->id])}}">
                                            {{$action->worker->fullName()}}
                                        </a>
                                    @else
                                        {{$action->worker->fullName()}}
                                    @endif

                                    @if($action->status_id == 5250005 && $action->office_automation_to_do_list->user_id == $user->id)

                                        (
                                        <a class="text-primary md-trigger md-setperspective"
                                           onclick="setActionConfirmId({{$action->id}})" data-modal="modal-19"
                                           href="#!">
                                            تایید انجام کار
                                        </a>
                                        |
                                        <a class="text-danger md-trigger md-setperspective"
                                           onclick="setActionRejectId({{$action->id}})" data-modal="modal-17" href="#!">
                                            ارجاع مجدد</a>

                                        )
                                    @else
                                        (<small class="text-c-blue">{{$action->status->caption}}</small>)

                                    @endif

                                    @if($to_do_actions[count($to_do_actions)-1 ]!=$action)
                                        ,
                                    @endif


                                @endforeach
                            @else
                                @php $action_log=$to_do->getAction($current_user->id);@endphp
                                {{$action_log->worker->fullName()}}
                                (<small class="text-c-blue">{{$action_log->status->caption}}</small>)

                            @endif
                        </label>
                    </div>


                    @if(count($to_do->to_view)>0)
                        <div class="col-md-12">
                            <label>
                                رونوشت:
                                @php $to_do_views=$to_do->to_view;@endphp
                                @foreach($to_do_views as $to_view)
                                    {{$to_view->worker->fullName()}}
                                    (<small class="text-c-blue">{{$to_view->status->caption}}</small>)


                                    @if($to_do_views[count($to_do_views)-1 ]!=$to_view)
                                        ,
                                    @endif
                                @endforeach
                            </label>
                        </div>
                    @endif


                    <div class="col-md-12">
                        {{--                        ایجاد ارجاع جدید--}}
                        @php $action_create_to_do=$to_do->getAction($current_user->id);@endphp
                        @if( $to_do->status_id != 5250004 && ($allow_new_to_do && $current_user->id == $user->id && isset($action_create_to_do) && $action_create_to_do->status_id ==5250002 || $current_user->id == $to_do->office_automation_work->user_id))
                            <a href="{{route("utility.office_automation.dashboard.create_to_do",[$to_do->id])}}"
                               class="btn btn-primary"> ارجاع جدید</a>
                        @endif

                        {{--                                                {{ $to_do->allowSubmitWorkDown($user->id)}}--}}
                        {{--                        ثبت انجام کار--}}
                        @if( $to_do->allowSubmitWorkDown($user->id) && $allow_new_to_do || (isset($action_create_to_do) && $action_create_to_do->user_id== $to_do->user_id &&  $action_create_to_do->status_id ==5250002 ) )
                            <button class="btn btn-primary collapsed" type="button"
                                    data-toggle="collapse"
                                    data-target="#collapse002" aria-expanded="false"
                                    aria-controls="collapse002">ثبت نتیجه انجام کار
                            </button>


                            <div class="collapse" id="collapse002" style="">

                                <form id="form_{{$to_do->id}}_3" class="form_to_do"
                                      action="{{route("utility.office_automation.work_done.submit",$to_do)}}"
                                      method="post"
                                      autocomplete="off"
                                      novalidate="novalidate"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>توضیحات</label>
                                            <textarea class="form-control max-textarea"
                                                      name="description"
                                                      maxlength="1000"
                                                      rows="4"></textarea>
                                            <br/>
                                        </div>
                                        @include("component.input._file_upload",["id"=>"work_file","label"=>"فایل (ها)","value"=>"","class_col"=>"col-md-12","multiple"=>1])

                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-success"> ثبت</button>

                                            <button class="btn btn-outline-dark collapsed" type="button"
                                                    data-toggle="collapse"
                                                    data-target="#collapseExample"
                                                    aria-expanded="false"
                                                    aria-controls="collapseExample">بعدا
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>


                        @endif


                    </div>


                    <div class="col-sm-12">
                        <hr/>
                        <label class="">شرح ارجاع ({{$to_do->get_create_date_and_time()}})
                            - {{$to_do->worker->fullName()}}</label>
                        :<br/>
                        <b>
                            {!! $to_do->description !!}
                        </b>
                        <br/>
                        @foreach($to_do->files as $file_item)
                            @if($file_item->office_automation_log->event_id == 5250002)
                                <a href="{{route("utility.office_automation.dashboard.download",[$to_do->office_automation_work_id,$file_item])}}">
                                    <i class="fa fa-download"></i>
                                    {{$file_item->file->caption}}
                                </a>
                            @endif
                        @endforeach
                        <br/>
                    </div>
                    @include("utility.office_automation.dashboard._logs")

                </div>
            </div>

        </div>
    </div>
</div>
