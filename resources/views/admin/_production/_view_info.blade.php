
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>نمایش فرم ارزیابی عملکرد تولید</h5>
            </div>
            <div class="card-block">
                <form id="form1" action="{{route("submit_form1",$production)}}" method="post" novalidate="novalidate">
                    @csrf

                    @include("admin.production._info")

                    <div class="row">

                        @include("component.input._lable",["id"=>"date_of_production_date","lable"=>" تاریخ ","value"=>

                        jdate( \Carbon\Carbon::parse($production->date_of_production_date)->timestamp)->format('%A, %d %B %y')])


                        @include("component.input._lable",["id"=>"set_up_time","lable"=>" زمان ست آپ  (دقیقه) ","value"=>$production->set_up_time])

                        @include("component.input._lable",["id"=>"unemployment_time","lable"=>" زمان مجاز بی کاری (دقیقه) ","value"=>$production->unemployment_time])


                        @include("component.input._lable",["id"=>"down_time","lable"=>" دون تایم خط (دقیقه) ","value"=>$production->down_time])


                        @include("component.input._lable",["id"=>"line_code","lable"=>" کد خط ","value"=>$production->start_time])

                        @include("component.input._lable",["id"=>"start_time","lable"=>"ساعت شروع ","value"=>$production->end_time])


                        @include("component.input._lable",["id"=>"end_time","lable"=>"ساعت پایان ","value"=>$production->line_code])



                        @include("component.input._lable",["id"=>"number_product","lable"=>"تعداد تولید شده ","value"=>$production->number_product])

                        @include("component.input._lable",["id"=>"sub_number_product","lable"=>" تعداد تکی ","value"=>$production->sub_number_product])

                    </div>

                    <a href="{{route("production.print",$production)}}" class="btn btn-info">پرینت</a>

                    <a href="{{route("dashboard")}}" class="btn btn-outline-defualt">بازگشت</a>
                </form>
            </div>
        </div>
    </div>

</div>
