@php $case_id=$product->supply_type_id;
if($product->goods_kind->production_algorithm_type_id==3){
	$case_id=-1;
}
@endphp

@switch($case_id)
    @case(1)
    @case(3)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><b>لیست کالاهای مصرفی </b></h5>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive center">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>

                                    <th>کالای مصرفی</th>
                                    @if($product_is_wage_work)
                                        <th>در زمان سفارش گذاری،<br/> امکان انتخاب ارسال کالای مصرفی به مشتری داده شود؟
                                        </th>
                                    @endif
                                    {{--                            <th>وضعیت</th>--}}

                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1; @endphp
                                @foreach($product->consumed_product as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>
                                            @if($item->material)
                                                {{($item->material->code??"")." - ".($item->material->caption??"")}}
                                            @else
                                                {{$item->product_creation_process->caption??""}} -
                                                ({{$item->product_creation_process->code??""}})
                                            @endif
                                        </td>
                                        @if($product_is_wage_work)
                                            <td>
                                               {{$item->in_ordering_customer_can_choose?"بله":"خیر"}}
                                            </td>
                                        @endif

                                        {{--                                <td>{{$item->status->caption}} {{$item->product_creation_process?"(".$item->product_creation_process->code.")":""}}</td>--}}


                                    </tr>
                                @endforeach

                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        @break
    @case(2)
    @case(4)
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-warning">
                    با توجه به نوع تامین کالا، امکان تعریف کالاهای مصرفی برای این کالا وجود ندارد.
                </div>


            </div>
        </div>
        @break
    @case(-1)
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-warning">
                    با توجه به نوع روش برنامه ریزی تولید در رسته کالایی، امکان تعریف کالاهای مصرفی برای این کالا وجود
                    ندارد.
                </div>


            </div>
        </div>
        @break
@endswitch
<div class="row">


    <div class="col-sm-6">
        @include($view_path."_btn_list")

    </div>




</div>
