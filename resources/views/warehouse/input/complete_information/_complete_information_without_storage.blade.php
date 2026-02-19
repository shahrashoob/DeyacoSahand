<div class="col-sm-12">

    <div class="card">
        <div class="card-header">
            <h5>تکمیل اطلاعات انبارش</h5>
        </div>
        <div class="card-block">
            <h6>روش ورود اطلاعات</h6>
            <div class="row">

            </div>
            <br/>
            <div class="table-responsive">
                <table class="table table-styling">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>کد کالا</th>
                        <th>درجه</th>
                        <th>لات</th>


                        <th  style="width: 250px">مقدار واحد اصلی</th>




                    </tr>

                    </thead>
                    <tbody>
                    @php $row=1;@endphp
                    @foreach($form->general_items as $item)



                                <tr>
                                    <td>{{$row++}}
                                        <input type="hidden" style="width: 60px" min="0"
                                               name="product_amount[{{$item->id}}][form_general_item_id]"
                                               value="{{$item->id}}" required>

                                    </td>
                                    <td title="{{$item->product->caption}}"><a href="#000">{{$item->product->code}}</a>
                                    </td>
                                    <td>{{$item->degree->caption??""}}</td>
                                    <td>{{$item->lot_number->code??""}}</td>



                                    <td >
                                        <input type="number" style="width: 60px" min="0"
                                               name="product_amount[{{$item->id}}][unit_amount]"
                                               value="{{isset($product_amount_rows["unit_amount"])?$product_amount_rows["unit_amount"]:""}}"
                                               required>
                                    </td>


                                </tr>
                            @endforeach


                    </tbody>

                </table>
            </div>


            <div class="col-md-12 center">
                <input type="hidden" value="no_save" name="save_data_status" id="save_data_status">
                <a class="btn btn-outline-dark" style="width: 130px"
                   href="{{route("wh.dashboard.index")}}">
                    بازگشت
                </a>
                <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>

                <button type="submit" id="save_data" class="btn btn-warning"> ذخیره موقت</button>

                @if($form->status_id ==500000430  )
                    <a class="btn btn-danger" style="width: 130px"
                       href="{{route("wh.dashboard.reject_packing_list",[$form])}}"
                       onclick='return confirm("آیا از عدم تایید فرم اطمینان دارید؟")'>عدم
                        تایید
                        انبار
                    </a>

                @endif
            </div>
        </div>
    </div>
</div>