<div class="card task-board-left">
    <div class="card-header">
        <h5>{{$office_automation_work->code}}</h5>
    </div>
    <div class="card-block ">

        <div class="task-right">
            <div class="taskboard-right-progress">
                <div class="row">
                    @include("component.input._lable",["lable"=>" ایجاد کننده کار","value"=>$office_automation_work->worker->fullName(),"class_col"=>"col-sm-12"])
                    @include("component.input._lable",["lable"=>" تاریخ ایجاد","value"=>$office_automation_work->get_create_date_and_time(),"class_col"=>"col-sm-12"])
                    @include("component.input._lable",["lable"=>" تاریخ پایان","value"=>$office_automation_work->end_datetime(),"class_col"=>"col-sm-12"])
                    @include("component.input._lable",["lable"=>" وضعیت ","value"=>$office_automation_work->status->caption])
                    @include("component.input._lable",["lable"=>" اولویت ","value"=>$office_automation_work->priority->caption])
                    @include("component.input._lable",["lable"=>" نوع ","value"=>$office_automation_work->office_automation_to_do_type->caption])

                    <div class="col-md-12">
                        <label>رونوشت: </label>
                        @foreach($office_automation_work->to_view_list as $to_do)

                                <b class="">{{$to_do->worker->fullName()}}</b>
                                (<small class="text-c-blue">{{$to_do->status->caption}}</small>)
                           ,
                        @endforeach
                    </div>
                </div>
                @php $percent=$office_automation_work->percent_of_doing_work();@endphp
                <h6 class="m-t-10"> در صد انجام کار ({{$percent}}%)</h6>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{$percent}}%"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>


            {{--                            <div class="taskboard-right-revision user-box">--}}

            {{--                                <div class="media">--}}
            {{--                                    <div class="media-left">--}}
            {{--                                        <a class="btn btn-outline-danger btn-icon" href="#!" role="button"><i class="fas fa-share-alt"></i>--}}
            {{--                                        </a>--}}
            {{--                                    </div>--}}
            {{--                                    <div class="media-body">--}}
            {{--                                        <div class="chat-header f-w-400 mb-1">نمودار تایم لاین</div>--}}
            {{--                                       --}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
        </div>
    </div>
</div>
