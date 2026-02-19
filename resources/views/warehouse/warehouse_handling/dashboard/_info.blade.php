@include("component.input._lable",["id"=>"start_datetime",'label'=>"تاریخ شروع ","value"=>$warehouse_handling->start_datetime(),"class_col"=>"col-md-3"])
<div class="w-100"></div>
@include("component.input._lable",["id"=>"start_datetime",'label'=>"تاریخ پایان ","value"=>$warehouse_handling->end_datetime(),"class_col"=>"col-md-3"])
<div class="w-100"></div>
@include("component.input._lable",["id"=>"warehouse",'label'=>"انبار ","value"=>$warehouse_handling->warehouse->caption,"class_col"=>"col-md-3"])
<div class="w-100"></div>
@include("component.input._lable",["id"=>"warehouse",'label'=>"آیا مقدار بسته بندی در انبارگردانی چک می شود","value"=>$warehouse_handling->check_diff_in_amount?"بله":"خیر","class_col"=>"col-md-6"])
<div class="w-100"></div>
@include("component.input._lable",["id"=>"warehouse",'label'=>"آیا وزن خالص بسته بندی در انبارگردانی چک  می شود","value"=>$warehouse_handling->check_diff_in_weight?"بله":"خیر","class_col"=>"col-md-6"])
<div class="w-100"></div>
@include("component.input._lable",["id"=>"warehouse",'label'=>"آیا تعداد بسته بندی فرعی بسته بندی در انبارگردانی چک می شود","value"=>$warehouse_handling->check_diff_in_sub_packing_form_number?"بله":"خیر","class_col"=>"col-md-6"])
<div class="w-100"></div>
@include("component.input._lable",["id"=>"max_diff_allowed","value"=>$warehouse_handling->max_diff_allowed." درصد","label"=>"حداکثر اختلاف مجاز بین مقدار/وزن خالص انبارگردانی و مقدار/وزن خالص بسته بندی","class_col"=>"col-md-6"])
@if($warehouse_handling->smart_object)
    <div class="w-100"></div>
    @include("component.input._lable",["id"=>"max_diff_allowed","value"=>$warehouse_handling->smart_object->caption,"label"=>"باسکول","class_col"=>"col-md-6"])
@endif
<div class="w-100"></div>


@include("component.input._lable",["id"=>"status",'label'=>"وضعیت ","value"=>$warehouse_handling->status->caption,"class_col"=>"col-md-3"])
<div class="w-100"></div>
@if($warehouse_handling->products()->count()==0)
    <div class="w-100"></div>
    @include("component.input._lable",["value"=>"همه کالاها","label"=>"کالاهای مجاز در انبارگردانی:","class_col"=>"col-md-6"])
@else
    <div class="col-md-12 offset-md-6">
        <a class="" data-toggle="collapse" href="#collapseProducts" role="button"
           aria-expanded="true" aria-controls="collapseProducts">
            <b>کالاهای مجاز در انبارگردانی: {{$warehouse_handling->products()->count()}}
                کالا </b>
        </a>
    </div>
    <div class="col-md-12 col-md-offset-1 center collapse  " id="collapseProducts" >
        <div class="table-responsive">
            <table class="table table-styling center">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th>کد کالا</th>
                    <th>نام کالا</th>
                </tr>

                </thead>
                <tbody>
                @php $row=1;@endphp
                @foreach($warehouse_handling->products as $item)
                    <tr>
                        <td>
                            {{$row++}}
                        </td>
                        <td>
                            {{$item->product->code}}
                        </td>
                        <td>
                            {{$item->product->caption}}
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>

        </div>
    </div>
@endif