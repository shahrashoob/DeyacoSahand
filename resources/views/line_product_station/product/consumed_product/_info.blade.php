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
                                    <th></th>
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
                                            <a href="{{route($route_path."delete",[$item,$product,$item->material_id??-1,$product_creation_process??null])}}"
                                               onclick="return confirm('آیا از حذف اطمینان دارید؟')"
                                               class="text-danger"><i
                                                        class="fa fa-trash"></i> </a>

                                            @if($item->material_id)
                                                <a href="{{route($route_path."replace",[$item,$product,$product_creation_process??null])}}">
                                                    <i class="fa  fa-retweet"></i> جابجایی کالا </a>
                                            @endif
                                        </td>
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
                                                <input class="in_ordering_customer_can_choose"
                                                       data-material_id="{{$item->material_id}}"
                                                       type="checkbox" {{$item->in_ordering_customer_can_choose?"checked":""}}>
                                            </td>
                                        @endif

                                        {{--                                <td>{{$item->status->caption}} {{$item->product_creation_process?"(".$item->product_creation_process->code.")":""}}</td>--}}


                                    </tr>
                                @endforeach

                                </tbody>

                            </table>
                        </div>
                        <form id="form1"
                              action="{{route($route_path."submit",[$product,$product_creation_process??null])}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="col-md-1"></div>
                                @include("component.input._aotocomplet2",[
                                                                    "id"=>"material_id",
                                                                    "label"=>" کالای مصرفی ",
                                                                    "option"=>$product_option["items"],
                                                                    "val"=>"",
                                                                    "text"=>"",
                                                                    "class_col"=>"col-md-4 col-sm-4"
                                                                    ])
                                <div class="col-md-4" style="text-align: right; padding-top: 8px">
                                    <br/>
                                    <button type="submit" class="btn btn-primary"> افزودن کالای مصرفی</button>
                                </div>


                            </div>
                        </form>
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


    </div>

    @include($view_path."_btn_list")


</div>

<script>
    $(".in_ordering_customer_can_choose").change(function () {
        if (confirm("آیا از ثبت تغییرات اطمینان دارید؟"))
            window.location = '{{route($route_path."change_choose_material",[$product])}}' + '/' + $(this).data("material_id") + '/{{$product_creation_process->id??null}}';
    })

</script>
