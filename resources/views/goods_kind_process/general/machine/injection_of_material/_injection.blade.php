{{--<div class="row">--}}

{{--    <div class="col-sm-12">--}}
{{--        <div class="card">--}}
{{--            <div class="card-header">--}}
{{--                <h5>--}}
{{--                    تزریق مواد اولیه به {{$machine->caption??""}}</h5>--}}
{{--            </div>--}}
{{--            <div class="card-block">--}}

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center!important;">
                        <thead>
                        <tr>

                            <th>#</th>
                            <th>کارت تولید</th>
                            <th>کد کالا</th>
                            <th>نام کالا</th>
                            <th>رسته کالایی</th>
                            <th>انبار مصرف کالا</th>
                            <th> شماره ورودی</th>
                            <th> شماره بسته بندی</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($current_input_list as $item)
                            <tr>

                                <td>{{++$row}}
                                </td>
                                <td>
                                    {{$item->production->serial??"***"}}
                                </td>
                                <td>
                                    {{$item->material->code??"***"}}
                                </td>
                                <td>
                                    {{$item->material->caption??"***"}}
                                </td>
                                <td>
                                    {{$item->goods_kind->caption??""}}
                                </td>
                                <td>
                                    {{$item->consume_warehouse->caption??""}}
                                </td>
                                <td>
                                    @if($item->input_line_code==0)
                                        از ورودی {{$item->input_line_code_from}}
                                         تا ورودی
                                        {{$item->input_line_code_to}}
{{--                                        <input name="data[input_line_code_to][{{$item->id}}]" type="number" style="width: 60px"--}}
{{--                                               value="{{$item->input_line_code_to}}"--}}
{{--                                        >--}}
                                    @else
                                        ورودی {{$item->input_line_code}}
                                    @endif
                                </td>
                                <td>
                                    <input name="data[input][{{$item->id}}]" type="number" style="width: 140px"
                                           value="{{isset($before_input_value[$item->material_id][$item->input_line_code])
                                                        && $before_input_value[$item->material_id][$item->input_line_code]->packing_form ?
                                                        $before_input_value[$item->material_id][$item->input_line_code]->packing_form->getCodeNumber()
                                                        :""
                                                        }}"
                                    >
                                    /DCPK
                                </td>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>

{{--            </div>--}}

{{--        </div>--}}

{{--    </div>--}}


{{--</div>--}}
