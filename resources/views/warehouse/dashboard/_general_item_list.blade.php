@if(count($form->general_items)>0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5>لیست محتویات فرم {{$form->code}}</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>کد کالا</th>
                            <th>نام کالا</th>
                            <th>درجه</th>
                            <th>لات</th>
                            <th>نوع بسته بندی</th>
                            <th>تعداد بسته بندی</th>
                            <th>مقدار کل</th>
                            <th>{{isset($form->general_items->first()->product->sub_unit)?"مقدار فرعی ":""}}</th>

                            @if(isset($hasQuality))
                                <th>وضعیت تخلیه</th>
                            @endif

                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($form->general_items as $item)

                            <tr>
                                {{--                                <h6>{{$item}}</h6>--}}
                                <td>{{$row++}}</td>
                                <td>{{$item->product->code??""}}</td>
                                <td>{{$item->product->caption??""}}</td>
                                <td>{{$item->degree->caption??""}}</td>
                                <td>{{$item->lot_number->code??""}}</td>
                                @if ($item->getPackingFormCaption())
                                    <td>{{$item->getPackingFormCaption()}}</td>
                                @else
                                    <td></td>
                                @endif

                                <td>{{$item->packing_form_number??""}}</td>
                                <td>{{$item->amount??""}}</td>
                                <td>{{isset($form->general_items->first()->product->sub_unit)?($item->sub_amount??""):""}}</td>
                                @if(isset($hasQuality))
                                    <td>
                                        @if($item->status_id == 5002002)
                                            <a href="{{route('wh.input.complete_information_with_value.quality_control',$item)}}">
                                                کنترل گرماژ</a>
                                        @elseif($item->status_id == 5002006)
                                            <a href="{{route('wh.input.complete_information_with_value.separation',$item)}}">عملیات
                                                در حال تخلیه </a>
                                        @elseif($item->status_id == 5002007)
                                                پایان تخلیه

                                       @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>


                    </table>
                </div>


            </div>
        </div>
    </div>

@endif
