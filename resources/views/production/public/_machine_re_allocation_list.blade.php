@if(count($production->machine_reallocation)>0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5>تخصیص های مجدد</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>نام و کد ماشین</th>
                            <th>زمان ایجاد تخصیص</th>
                            <th>مقدار در انتظار</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php
                            $row=1;
                            $allocaiton_list=[];
                        @endphp
                        @foreach($production->machine_reallocation as $item)
                            {{--                        @if(!isset($allocation_list[$item->allocation_id."_".$item->production_id]))--}}
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    <a href="{{route("fabric.machine_allocation.reallocation",[$item->production_id,$item])}}">ادامه
                                        تخصیص</a>
                                </td>
                                <td>{{$item->get_datetime()}}</td>
                                <td>
                                    @if($item->allocation_amount)
                                    {{$item->allocation_amount}} {{$item->product->unit->caption}}
                                    @endif

                                    @if($item->number_of_packing_form)
                                    {{$item->number_of_packing_form}} بسته بندی
                                    @endif

                                </td>
                                <td></td>
                            @php $allocation_list[$item->allocation_id."_".$item->production_id]=1;@endphp
                            {{--                        @endif--}}
                        @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
@endif