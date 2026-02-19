@include("component.formatDecimal9")
@if(count($current_input_list) > 0)
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header" style="border: none">
                <h5>لیست ورودی های ماشین
                </h5>
            </div>
            <div class="card-block pading_0" style="padding-top: 0px;border: none">

                <div class="table-responsive" style="padding-bottom: 100px">
                    @if(isset($allow_injection) && $allow_injection)
                        <form id="form1"
                              action="{{route($injection_route,$machine)}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                    @endif

                       <table class="table table-styling" style="text-align: center!important;">
                                <thead>
                                <tr>

                                    <th>#</th>

                                    @if(isset($allow_injection) && $allow_injection)
                                        <th>بسته بندی جدید</th>
                                    @endif
                                    <th>کد کالا</th>
                                    <th>نام کالا</th>
                                    <th>مقدار مورد نیاز
                                    </th>
                                    <th>مقدار تحویل شده
                                    </th>
                                    <th> همبافت</th>
                                    <th>رسته کالایی</th>
                                    <th> شماره ورودی</th>
                                    <th> حامل / بسته بندی</th>
                                </tr>
                                <tr class="pading_0">

                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <span style="font-size: 10px !important; font-weight: normal">به ازای هر ورودی</span>
                                    </td>
                                    <td>
                                        <span style="font-size: 10px !important; font-weight: normal">به ازای هر کالا</span>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($current_input_list as $item)
                                    <tr>

                                        <td>{{++$row}}
                                        </td>
                                        @if(isset($allow_injection) && $allow_injection)
                                            <td>
                                                <input name="data[input][{{$item->id}}]" type="number"
                                                       style="width: 140px"
                                                       value="{{isset($before_input_value[$item->material_id][$item->input_line_code])
                                                        && $before_input_value[$item->material_id][$item->input_line_code]->packing_form ?
                                                        $before_input_value[$item->material_id][$item->input_line_code]->packing_form->getCodeNumber()
                                                        :""
                                                        }}"
                                                >
                                                /DCPK
                                            </td>
                                        @endif
                                        <td>
                                            {{$item->material->code??"***"}}
                                        </td>
                                        <td>
                                            {{$item->material->caption??"***"}}
                                        </td>
                                        <td>
                                            <div class="btn-group mb-2 mr-2 show">
                                                <a class="   dropdown-toggle" type="button" data-toggle="dropdown"
                                                   aria-haspopup="true" aria-expanded="true">
                                                    {{formatDecimal9($item->amount_required)}}

                                                </a>
                                                <div class="dropdown-menu " x-placement="bottom-start"
                                                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 45px, 0px);">
                                                    <a class="dropdown-item center" href="#!">
                                                        مقدار مورد نیاز به ازای هر واحد کالا
                                                        <br/>
                                                        {{formatDecimal9($item->amount+0)}}
                                                    </a>

                                                </div>
                                            </div>

                                        </td>

                                        <td>

                                            <div class="btn-group mb-2 mr-2 show">
                                                <a class="   dropdown-toggle" type="button" data-toggle="dropdown"
                                                   aria-haspopup="true" aria-expanded="true">
                                                    {{$item->amount_delivered()}}
                                                </a>
                                                <div class="dropdown-menu " x-placement="bottom-start"
                                                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 45px, 0px);">
                                                    <a class="dropdown-item center" href="#!">
                                                        <div style="max-height: 200px; overflow: auto; padding: 15px">
                                                            <table class="tbl_detail_info">
                                                                <tr>
                                                                    <td>کد فرم درخواست کالا از انبار</td>
                                                                    <td>تاریخ درخواست</td>
                                                                    <td>مقدار درخواست</td>
                                                                    <td>مقدار تحویل شده</td>
                                                                </tr>
                                                                @foreach($item->product_request_form_allocation_list() as $product_request_form_allocation)

                                                                    @php $product_request_form= $product_request_form_allocation->product_request_form_remove();@endphp
                                                                    <tr>
                                                                        <td> {{$product_request_form->code??"***"}}</td>
                                                                        <td> {{isset($product_request_form)?$product_request_form->get_create_date_and_time():""}}</td>
                                                                        <td> {{$product_request_form_allocation->amount_request}}</td>
                                                                        <td> {{$product_request_form_allocation->amount_delivered}}</td>
                                                                    </tr>

                                                                @endforeach
                                                            </table>
                                                        </div>
                                                    </a>

                                                </div>
                                            </div>

                                        </td>

                                        <td>
                                            {{$item->lot_number->code??""}}
                                        </td>
                                        <td>
                                            {{$item->goods_kind->caption??""}}
                                        </td>
                                        <td>
                                            @if($item->input_line_code==0)
                                                از ورودی {{$item->input_line_code_from}}
                                                تا ورودی
                                                {{$item->input_line_code_to}}

                                            @else
                                                ورودی {{$item->input_line_code}}
                                            @endif
                                        </td>
                                        <td>
                                            {{isset($item->carrier)?$item->carrier->getCaption():""}}
                                            - {{$item->packing_form->code??""}}
                                        </td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                    @if(isset($allow_injection) && $allow_injection)
                        <div class="col-md-12 center"><br/>
                            <button type="submit" class="btn btn-primary">ثبت تزریق</button>
                        </div>
                        </form>
                    @endif
                </div>

            </div>

        </div>

    </div>
@endif
<style>
    .tbl_detail_info td {
        padding-top: 3px !important;
        padding-bottom: 3px !important;
    }

    .pading_0 td, .pading_0 th {
        padding: 5px;
    }

    .pading_0 table td, .pading_0 table th {
        border: none;
    }
</style>
