


    <div class="col-sm-12">
        @include("goods_kind_process.fabric.special_production.machine.dashboard._search_view",["route"=>"fabric.special_production.machine.dashboard.view",])
        <div class="card">
            <div class="card-header">
                <h5>کارت (های) تولید رزرو
                </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center!important;">
                        <thead >
                        <tr>

                            <th>اولویت</th>
                            <th>شماره تخصیص</th>
                            <th>کارت تولید</th>
                            <th> کالا</th>
                            <th>مقدار</th>
                            <th>تاریخ رزرو</th>
                            <th>
{{--                                وضعیت تخصیص--}}
                            </th>


                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=$reserve_allocation_list->firstItem();@endphp
                        @foreach($reserve_allocation_list as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>{{$item->allocation_id}}</td>
                                <td>

                                    <a href="{{route("fabric.special_production.machine.register_production.index",$item->id)}}">{{$item->production->serial()}}
                                        @if($item->production->production_type_id ==2)
                                            <i class="fa fa-vial text-dark"></i>
                                        @endif
                                    </a>


                                </td>
                                <td>
                                    {{$item->product->fullCaption()}}
                                </td>
                                <td>
                                    {{$item->allocation_amount}} {{$item->product->unit->capiton}}
                                </td>
                                <td>
{{--                                    {{$item->status->caption}}--}}
                                </td>
                                <td>
                                    {{$item->get_datetime()}}
                                </td>

                                <th>
                                    <a  href="{{route("fabric.special_production.machine.allocation_card.index",[$machine,$item->allocation_id])}}">
                                        <i class="fa fa-print"></i>
                                    </a>

                                </th>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>
                <div class="float-left">
                    نمايش رکوردهای
                    <b>{{$reserve_allocation_list->firstItem()}}</b>
                    تا
                    <b>{{$reserve_allocation_list->lastItem()}}</b>
                    از
                    <b>{{$reserve_allocation_list->total()}}</b>
                    رکورد موجود


                </div>
                <div class="text-center">
                    {{$reserve_allocation_list->links('pagination::bootstrap-4')}}
                </div>

            </div>
        </div>

    </div>


