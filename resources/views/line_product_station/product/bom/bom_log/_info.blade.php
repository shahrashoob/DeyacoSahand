@php $case_id=$product->supply_type_id;
if($product->goods_kind->production_algorithm_type_id==3){
	$case_id=-1;
}
@endphp
@switch($case_id)
    @case(2)
    @case(4)
        <div class="col-sm-12">
            <div class="alert alert-warning">
                با توجه به نوع تامین کالا، کالا BOM ندارد.
            </div>

        </div>
        @break
    @case(1)
    @case(3)
        <div class="col-sm-12 mb-3">
            <h5 class="mb-3">لیست سابقه {{$bom->fullCaption()}}





            </h5>

            <hr>
            <div class="accordion" id="accordionExample">
                @foreach($bom_logs as $bom_log)

                    <div class="card">
                        <div
                                class="card-header">

                            <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route{{$bom_log->id}}"
                                                aria-expanded="false" aria-controls="collapseOne" class="collapsed">

ورژن
                                    {{$bom_log->version}}
                                    @if($bom_log->production)
                                        - کارت تولید
                                        {{$bom_log->produciton->serial()}}
                                    @endif

                                    @if($bom_log->allocation)
                                        -
                                        تخصیص شماره
                                        {{$bom_log->allocation_id}}
                                    @endif

                                    @if($bom_log->user_id)
                                        -

                                        {{$bom_log->worker->fullname()}}
                                    @endif


                                </a>
                            </h5>

                        </div>
                        <div class="multi-collapse collapse "
                             id="route{{$bom_log->id}}"
                             style=""
                             data-parent="#accordionExample">


                                <div class="table-responsive center" style="font-size: 12px">
                                    <table class="table table-styling">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ماده اولیه</th>

                                            <th>مقدار</th>
                                            <th>تعداد</th>
                                            <th>درصد استفاده</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $list= $bom_log->items;@endphp
                                        @php $row=1; @endphp
                                        @foreach($list as $item)
                                            <tr>
                                                <td>{{$row++}}</td>
                                                <td>

                                                    {{($item->bom_item->material->code??"")." - ".($item->bom_item->material->caption??"")}}

                                                </td>

                                                <td>{{$item->amount??""}}</td>
                                                <td>{{$item->number}}</td>
                                                <td>{{$item->percent_of_use}}</td>


                                            </tr>
                                        @endforeach
                                        </tbody>

                                    </table>

                                </div>

                            <br/>
                        </div>
                    </div>

                @endforeach
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

@if($view_path)
    @include($view_path."_btn_list")
@endif
