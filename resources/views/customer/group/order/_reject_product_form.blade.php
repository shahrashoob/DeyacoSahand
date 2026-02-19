<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>فرم مرجوعی {{$reject_product_form->code}}</h5>
            </div>
            <div class="card-block">


                <div class="row">
                    @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$reject_product_form->applicant->fullCaption()])
                    @include("component.input._lable",["label"=>"وضعیت","value"=>$reject_product_form->status->caption??""])
                    @include("component.input._lable",["label"=>"تاریخ و زمان ثبت","value"=>$reject_product_form->get_create_date_and_time()])
                    @include("component.input._lable",["label"=>"علت مرجوعی","value"=>$reject_product_form->reject_product_reason_type->caption??""])

                    <div class="table-responsive">
                        <table class="table table-styling center" style="">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>کد بسته بندی</th>
                                <th> مقدار مرجوعی</th>
                                <th> بسته سالم است</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($reject_product_form->items as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->packing_form->getCode()}}</td>
                                    <td>{{ $item->packing_is_safe?$item->packing_form->getFinalAmount():$item->amount_remaining}}</td>
                                    <td>{!! $item->packing_is_safe==1?"<i class='fa fa-check'/>":"" !!}

                                    </td>


                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>


                </div>
                @if($reject_product_form->status_id == 7009007)
                    <div class="alert alert-warning">
                        {{$reject_product_in_send_product}}
                    </div>
                @endif
                <div class="center">
                    <a href="{{route($back_route_path,$order)}}"
                       class="btn btn-outline-dark">بازگشت</a>
                    @if($reject_product_form->status_id == 7009007 && isset($customer_reject))
                        <a href="{{route("customer_group.order.reject_product.download_form",[$order,$reject_product_form])}}"
                           class="btn btn-primary"><i class="fa fa-download"></i> دانلود فرم مرجوعی </a>
                        <form id="form1" autocomplete="off" style="display: inline"
                              action="{{route("customer_group.order.reject_product.submit_send_product",[$order,$reject_product_form])}}"
                              method="post"
                              novalidate="novalidate">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('آیا از ثبت فرم مرجوعی اطمینان دارید؟')">ثبت ارسال</button>
                        </form>

                    @endif

                </div>
            </div>
        </div>
    </div>


</div>