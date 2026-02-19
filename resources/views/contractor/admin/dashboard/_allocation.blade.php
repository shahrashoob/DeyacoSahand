@if( count($allocation_list) > 0)

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>تخصیص های انجام شده
                </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center!important;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شماره تخصیص</th>
                            <th></th>
                            <th>پیمانکار</th>
                            <th> کالا</th>
                            <th>مقدار</th>
                            <th>تاریخ رزرو</th>
                            <th>وضعیت</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($allocation_list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>
                                    @if($item->allocation->items()->count() > 1)
                                        <a class="" data-toggle="collapse" href="#allocation_row{{$item->allocation_id}}" role="button"
                                           aria-expanded="true"
                                           aria-controls="multiCollapseExample1">

                                            {{$item->allocation->code()}}
                                        </a>
                                    @else
                                        {{$item->allocation->code()}}
                                    @endif

                                </td>
                                <th>
                                    @if($item->allocation->items()->count() > 1)
                                        <a class="" data-toggle="collapse" href="#allocation_row{{$item->allocation_id}}" role="button"
                                           aria-expanded="true"
                                           aria-controls="multiCollapseExample1">

                                            {{$item->allocation->items()->count()}} پیمان
                                        </a>

                                    @endif
                                </th>
                                <td>
                                    <a href="{{route("contractor.admin.dashboard.log",$item)}}">
                                        {{$item->allocation->contractor->caption}}
                                    </a>
                                </td>
                                <td>
                                    {{$item->product->fullCaption()}}
                                </td>
                                <td>
                                    {{$item->allocation_amount}} {{$item->product->unit->caption??""}}
                                </td>
                                <td>
                                    {{$item->get_datetime()}}
                                </td>
                                <td>

                                    {{$item->status->caption??""}}

                                </td>

                            </tr>


                                @foreach($item->allocation->items()->where("id","!=",$item->id)->get() as $other_item)


                                    <tr class="alert alert-info multi-collapse mt-2 collapse " id="allocation_row{{$item->allocation_id}}">
                                    <td></td>
                                    <td colspan="3">

                                        {{$other_item->production->serial()}}
                                    </td>
                                    <td>
                                        {{$other_item->product->fullCaption()}}
                                    </td>
                                    <td>
                                        {{$other_item->allocation_amount}} {{$item->product->unit->caption??""}}
                                    </td>
                                    <td>
                                        {{$other_item->get_datetime()}}
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

            </div>
        </div>

    </div>

@endif
