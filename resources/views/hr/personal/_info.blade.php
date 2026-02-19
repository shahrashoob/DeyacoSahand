<div class="row">
    <div class="" style="float: right">
        {{$qr}}
        <br/>
        <br/>
          <img style="width: 200px" src="{{asset("chatify_app/users-avatar/".($worker->image->filename??''))}}"
             onerror="this.onerror=null;this.src='{{url("assets/images/avatar.png")}}';"
        />
        <br/>
        <br/>
    </div>
    <div class="col-md-8">
        <div class="row">
            @include("component.input._lable",["id"=>"firstname","label"=>"نام ","value"=>$worker->firstname??""])
            @include("component.input._lable",["id"=>"lastname","label"=>"نام خانوادگی ","value"=>$worker->lastname??""])
            @include("component.input._lable",["id"=>"national_code","label"=>"نام پدر ","value"=>$worker->father_name??""])
            @include("component.input._lable",["id"=>"national_code","label"=>"کد ملی ","value"=>$worker->national_code??""])
            @include("component.input._lable",["id"=>"national_code","label"=>"شماره پرسنلی ","value"=>$worker->id??""])
            @include("component.input._lable",["id"=>"national_code","label"=>"تاریخ قرارداد ","value"=>$worker->end_date_of_contract()])
            @include("component.input._lable",["id"=>"national_code","label"=>"وضعیت ","value"=>$worker->status->caption??""])
            @include("component.input._lable",["id"=>"national_code","label"=>"مجوز ورود به سازمان ","value"=>$worker->entry_permit_status->caption??""])
            @include("component.input._lable",["id"=>"national_code","label"=>"مجوز خروج از  سازمان   ","value"=>$worker->exit_permit_status->caption??""])

            <div class="col-md-12 offset-md-12">
                <div class="form-group">


                    <label>پست دیجیتال سازمانی:</label>
                    @if(count($post_user_list)==0)
                        <span class="text-danger">
                        ---
                        </span>
                    @endif
                    @foreach($post_user_list as $item)

                        @php
                            $exist=false;
                          foreach($current_post_users as $current_item){
                              if($current_item["post_id"] == $item->post_id && $current_item["shift_work_id"] == $item->shift_work_id ){
                                  $exist=true;
                              }
                          }

                        @endphp
                        <a href="#!" data-toggle="collapse" style="{{$exist?'color: #0ccb07':""}}"
                           data-target="#collapseExample{{$item->id}}"
                           aria-expanded="false" aria-controls="collapseExample{{$item->id}}">
                            <b>{{$item->post->caption}} </b>({{$item->shift_work->caption??""}})
                        </a> ,

                    @endforeach

                    @foreach($post_user_list as $item)
                        <div class="collapse col-md-12" id="collapseExample{{$item->id}}">


                            <div class="alert "
                                 style="border: 1px solid #0b0b0b; border-radius: 10px">

                                <div class="row">
                                    {{$item->post->caption}} ({{$item->shift_work->caption??""}})
                                    <br/>
                                    <br/>
                                    شیفت:
                                    {{$item->post->shift->caption??""}}
                                    <br/>
                                    <br/>
                                    تعجیل مجاز برای ورود به سازمان
                                    {{$item->post->allowed_earlier_time_for_entry}}
                                    دقیقه می باشد.

                                    <br/>
                                    <br/>
                                    تاخیر مجاز برای ورود به سازمان
                                    {{$item->post->allowed_delay_time_for_entry}}
                                    دقیقه می باشد.

                                    <br/>
                                    <br/>
                                    تعجیل مجاز برای خروج از سازمان
                                    {{$item->post->allowed_earlier_time_for_exit}}
                                    دقیقه می باشد.

                                    <br/>
                                    <br/>
                                    تاخیر مجاز برای خروج از سازمان
                                    {{$item->post->allowed_delay_time_for_exit}}
                                    دقیقه می باشد.

                                    <br/>
                                    <br/>
                                    پست مافوق:
                                    {{$item->post->parent->caption??""}}
                                    <br/>
                                    <br/>
                                    @if(count($item->post->post_replace) > 0)
                                        لیست پست های جانشین:
                                        @foreach($item->post->post_replace as $pr)
                                            {{$pr->replace_post->caption??""}},
                                        @endforeach
                                    @endif

                                </div>
                            </div>

                        </div>

                    @endforeach
                </div>
            </div>


            <div class="col-md-12 offset-md-12">
                <div class="form-group">


                    <label>ساعت های کاری امروز:</label>

                    <a style="font-weight: bold" href="{{route("hr.personal.shift_work_day.index",$worker)}}">
                        @if(count($shift_work_list)>0)
                            @foreach($shift_work_list as $item)
                                {{$item->getStartDatetime()}} - {{$item->getEndDatetime()}} |
                            @endforeach
                        @else
                            فاقد ساعت کاری |
                        @endif

                        مشاهده گزارش عملکرد
                    </a>
                </div>
            </div>

            @include("component.input._lable",["id"=>"leave_reminder","label"=>"مانده مرخصی ","value"=>$leave_reminder[1]])


        </div>
    </div>
</div>
