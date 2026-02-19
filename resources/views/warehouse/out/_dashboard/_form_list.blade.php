@if(count($product_request_form->forms)>0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> لیست فرم های ثبت شده برای تحویل کالا </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling center" style="">
                        <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>شماره فرم</th>
                            <th>تاریخ ارسال</th>
                            <th> تعداد بسته بندی</th>
                            <th>وضعیت</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($product_request_form->forms()->groupBy("form_id")->get() as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>{{$item->form->code}}</td>
                                <td>{{$item->form->get_create_date_and_time()}}</td>
                                <td>{{$item->form->getPackingFromCount()}}</td>
                                <td>{{$item->form->status->caption}}</td>
                                <th>

                                    <a  class="btn drp-icon btn-rounded btn-outline-primary" href="{{route("wh.out.dashboard.show_form",[$product_request_form,$item->form,$page])}}"><i
                                            class="fa fa-eye"></i> </a>


                                    <button class="btn drp-icon btn-rounded btn-outline-primary dropdown-toggle dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-print"></i>
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start"
                                         style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                                        <a class="dropdown-item" href="{{route("wh.out.exit_form.print",[$product_request_form,$item->form,4])}}"
                                           onclick="return confirm('آیا از پرینت فرم خروج اطمینان دارید؟')"> قالب A4 </a>
                                        <a class="dropdown-item" href="{{route("wh.out.exit_form.print",[$product_request_form,$item->form,3])}}"
                                           onclick="return confirm('آیا از پرینت فرم خروج اطمینان دارید؟')"> قالب A5 </a>
                                        <a class="dropdown-item"  href="{{route("wh.out.exit_form.print",[$product_request_form,$item->form,1])}}"
                                           onclick="return confirm('آیا از پرینت فرم خروج اطمینان دارید؟')">  قالب 95*123 </a>
                                    </div>




                                </th>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
