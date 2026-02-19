@if(count($contractor_packing_list)>0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> لیست فرم های بسته بندی </h5>
            </div>
            <div class="card-block" style="overflow: auto">

                <table class="table table-styling center">
                    <tr>
                        <th></th>
                        <th>کد کالا</th>
                        <th>نام کالا</th>
                        <th>شماره حامل</th>
                        <th> فرم انبار</th>
                        <th> فرم تولید</th>
                        <th>کد بسته بندی</th>
                        <th>نوع بسته بندی</th>
                        <th>تعداد بسته بندی<br/>اقلام</th>
                        <th>مقدار</th>
                        <th>مقدار<br/> فرعی</th>
                        <th>وضعیت</th>
                        <th></th>
                    </tr>
                    <tr>
                        @php $row=1;@endphp
                        @foreach($contractor_packing_list as $item)
                            <td>{{$row++}}</td>
                            <td>{{$contractor_allocation->product->code}}</td>
                            <td>{{$contractor_allocation->product->caption}}</td>
                            <td>{{$item->packing_form->carrier->code??""}}</td>
                            <td>{{$item->packing_form->form->code??"---"}}</td>
                            <td>
                                @php $pfitem=$item->packing_form->items()->first()->production_form_item; @endphp
                                <a href="{{route("production.production_form.view",$pfitem->production_form_id)}}">
                                    {{$pfitem->code}}
                                </a>
                            </td>
                            <td>
                                <a href="{{route("contractor.panel.dashboard.view_packing",[$contractor_allocation,$item->packing_form ])}}">
                                    {{$item->packing_form->getCode()}}
                                </a>
                            </td>
                            <td>{{$item->packing_form->packing_type->caption??""}}</td>
                            <td>{{$item->packing_form->items()->count()}}</td>
                            <td>{{$item->packing_form->getAllAmount("final_amount")}}</td>
                            <td>{{$item->packing_form->getAllAmount("sub_amount")}}</td>
                            <td>{{$item->packing_form->status->caption}}</td>
                            <td>


                                <a href="{{route("fabric_raw.packing_form.print_qr.index",[$item->packing_form,"contractor.panel.dashboard.view",$contractor_allocation->id] )}}">
                                    <i class="fa fa-print text-info"></i>

                                </a>

                                <a href="{{route("fabric_raw.packing_form.print_qr.download",[$item->packing_form,"contractor.panel.dashboard.view",$contractor_allocation->id] )}}">
                                    <i class="fa fa-download"></i>

                                </a>


                            </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endif
