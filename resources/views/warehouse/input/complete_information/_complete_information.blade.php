<div class="col-sm-12">

    <div class="card">
        <div class="card-header">
            <h5>تکمیل اطلاعات بسته بندی ها</h5>
        </div>
        <div class="card-block">
            <h6>روش ورود اطلاعات</h6>
            <div class="row">
                <div class="col-md-3">
                    @include("component.input._checkbox_simple",["id"=>"enter_weight","label"=>"ثبت وزن خالص","checked"=>$enter_weight])
                </div>
                <div class="col-md-3">
                    @include("component.input._checkbox_simple",["id"=>"enter_gross_weight","label"=>"ثبت وزن ناخالص","checked"=>$enter_gross_weight])
                </div>
                @if($show_enter_unit_amount)
                    <div class="col-md-3">
                        @include("component.input._checkbox_simple",["id"=>"enter_unit_amount","label"=>"ثبت واحد اصلی","checked"=>$enter_unit_amount])
                    </div>
                @endif
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

                        <th class="enter_gross_weight" style="width: 250px"> وزن ناخالص</th>


                        <th class="enter_weight" style="width: 250px">وزن خالص</th>

                        <th class="enter_unit_amount" style="width: 250px">مقدار واحد اصلی</th>

                        <th class="class_sub_packing_number" style="width: 250px">تعداد <br/>بسته بندی های فرعی</th>

                        <th>نوع بسته بندی</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=1;$k=0;@endphp
                    @foreach($form->general_items as $item)
                        @for($i=0;$i<$item->packing_form_number;$i++)
                            <tr>
                                <td>{{$row++}}
                                    <input type="hidden" style="width: 60px" min="0"
                                           name="packing_form_rows[{{$k}}][form_general_item_id]"
                                           value="{{$item->id}}" required>

                                </td>
                                <td title="{{$item->product->caption}}"><a href="#000">{{$item->product->code}}</a></td>
                                <td>{{$item->degree->caption??""}}</td>
                                <td>{{$item->lot_number->code??""}}</td>

                                <td class="enter_gross_weight">
                                    <input type="number" style="width: 60px" min="0"
                                           name="packing_form_rows[{{$k}}][gross_weight]"
                                           value="{{isset($packing_form_rows[$k]["gross_weight"])?$packing_form_rows[$k]["gross_weight"]:""}}"
                                           required>
                                </td>

                                <td class="enter_weight">
                                    <input type="number" style="width: 60px" min="0"
                                           name="packing_form_rows[{{$k}}][weight]"
                                           value="{{isset($packing_form_rows[$k]["weight"])?$packing_form_rows[$k]["weight"]:""}}"
                                           required>
                                </td>

                                <td class="enter_unit_amount">
                                    <input type="number" style="width: 60px" min="0"
                                           name="packing_form_rows[{{$k}}][unit_amount]"
                                           value="{{isset($packing_form_rows[$k]["unit_amount"])?$packing_form_rows[$k]["unit_amount"]:""}}"
                                           required>
                                </td>

                                <td>
                                    @if ($first_packing_type_layers[$item->packing_type_id]!=null)
                                        <input type="number" style="width: 60px" min="0"
                                               name="packing_form_rows[{{$k}}][sub_packing_form_number]"
                                               value="{{isset($packing_form_rows[$k]["sub_packing_form_number"])?$packing_form_rows[$k]["sub_packing_form_number"]:""}}"
                                               required>

                                    @else
                                        ---
                                    @endif

                                </td>

                                <td>{{$item->packing_type->fullCaption()}} </td>
                            @php $k++;@endphp
                        @endfor
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
