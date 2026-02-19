<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>سابقه تخصیص های انجام شده بر روی کارت تولید {{$production->serial}}
            </h5>
        </div>
        <div class="card-block">

            <div class="table-responsive">
                <table class="table table-styling" style="text-align: center!important;">
                    <thead>
                    <tr>
                        <td>#</td>
                        <td>شماره تخصیص</td>
                        @if($allow_show_log)
                            <td>اطلاعات تخصیص</td>
                        @endif
                        <th>ماشین</th>
                        <th> کالا</th>
                        <th>مقدار کارت تولید</th>
                        <th>مقدار تخصیص</th>
                        <th>وضعیت کارت تولید</th>
                        <th>تعداد داف</th>
                        <th>مشخصات هر داف</th>
                        <th>تاریخ رزرو</th>
                        <th>تاریخ شروع تولید</th>
                        <th>وضعیت تخصیص</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=$list->firstItem()-1;@endphp
                    @foreach($list as $item)
                        @php $other_items=$item->allocation->items()->where("production_id","!=",$item->production_id)->get(); @endphp

                        <tr>
                            <td rowspan="{{1+count($other_items)}}" style="vertical-align: middle">{{++$row}}</td>
                            <td rowspan="{{1+count($other_items)}}" style="vertical-align: middle">
                                <a href="{{route("$goods_kind_caption_en.production_card.finished_allocation.allocation_input",[$production,$item->allocation_id])}}">
                                    {{$item->allocation_id}}</a>
                            </td>
                            @if($allow_show_log)
                                <td>
                                    <a href="{{route("$goods_kind_caption_en.production_card.finished_allocation.allocation_data",[$production,$item->allocation_id])}}">
                                        <i class="fa fa-eye"></i> </a>
                                </td>
                            @endif
                            <td rowspan="{{1+count($other_items)}}" style="vertical-align: middle">
                                {{$item->machine->fullCaption()}}
                            </td>
                            <td>
                                {{$item->product->fullCaption()}}

                                @if($item->version_code)
                                    <br/>
                                    (V{{$item->version_code}})
                                @endif


                            </td>
                            <td>
                                {{$item->production->number}} {{$item->product->unit->caption??""}}
                            </td>
                            <td>
                                {{$item->production->get_allocation_amount($item->machine->id,1,$item->allocation_id)}} {{$item->product->unit->caption??""}}
                            </td>
                            <td>
                                {{$item->production->getStatus()}}
                            </td>
                            <td rowspan="{{1+count($other_items)}}" style="vertical-align: middle">
                                {{$item->max_number_of_doffs}}
                            </td>
                            <td rowspan="{{1+count($other_items)}}" style="vertical-align: middle">
                                @include("goods_kind_process.general.machine.allocation_card._allocation_doff_brand",["product"=>$item->product,"machine_allocation"=>$item])


                            </td>
                            <td>
                                {{$item->get_datetime()}}
                            </td>
                            <td>
                                {{$item->start_time()}}
                            </td>
                            <td>
                                {{$item->status->caption??""}}
                            </td>


                        </tr>
                        @foreach($other_items as $other_item)
                            <tr>
                                @if($allow_show_log)
                                    <td>
                                        <a href="{{route("$goods_kind_caption_en.production_card.finished_allocation.allocation_data",[$production,$item->allocation_id])}}">
                                            <i class="fa fa-eye"></i> </a>
                                    </td>
                                @endif

                                <td>
                                    {{$other_item->product->fullCaption()}}

                                    @if($other_item->version_code)
                                        <br/>
                                        (V{{$other_item->version_code}})
                                    @endif


                                </td>
                                <td>
                                    {{$other_item->production->number}} {{$other_item->product->unit->caption??""}}
                                </td>
                                <td>
                                    {{$other_item->production->get_allocation_amount($other_item->machine->id,1,$other_item->allocation_id)}} {{$item->product->unit->caption??""}}
                                </td>
                                <td>
                                    {{$other_item->production->getStatus()}}
                                </td>

                                <td>
                                    {{$other_item->get_datetime()}}
                                </td>
                                <td>
                                    {{$other_item->start_time()}}
                                </td>
                                <td>
                                    {{$other_item->status->caption??""}}
                                </td>



                            </tr>

                        @endforeach
                    @endforeach
                    </tbody>

                </table>
            </div>
            <div class="float-left">
                نمايش رکوردهای
                <b>{{$list->firstItem()}}</b>
                تا
                <b>{{$list->lastItem()}}</b>
                از
                <b>{{$list->total()}}</b>
                رکورد موجود
            </div>
        </div>
        <div class="text-center">
            {{$list->links('pagination::bootstrap-4')}}
        </div>
    </div>

</div>