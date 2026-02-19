<div class="col-md-12 col-sm-12">
    <div class="card card-border-c-blue">
        <div class="card-header">

                کد
                {{$work->getCode()}}

            <span class="label label-primary float-right"> {{$work->status->caption}} </span>
        </div>
        <div class="card-block">


            <div class="col-sm-12">
                <div class="row">
                    <div class="col-md-12">
                        <label>عنوان:
                            {{$work->caption}}</label>
                    </div>
                    <div class="col-md-12">
                        <label>
                            ایجاد کننده:
                            {{$work->worker->fullName()}}
                        </label>
                    </div>
                    <div class="col-md-12">
                        <label>
                            تاریخ ایجاد:
                            {{$work->get_create_date_and_time()}}
                        </label>
                    </div>
                    <div class="col-md-12">
                        <label>
                            تاریخ پایان:
                            {{$work->end_datetime()}}
                        </label>
                    </div>
                    <div class="col-md-12">
                        <label>
                            اولویت:
                            {!! $work->priority->getHtml() !!}
                        </label>
                    </div>


                </div>
            </div>


        </div>
    </div>
</div>
