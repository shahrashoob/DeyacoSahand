
  <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center!important;">
                        <thead>
                        <tr>

                            <th>#</th>
                            <th>نام کالا</th>
                            <th>رسته کالایی</th>
                            <th> شماره ورودی</th>
                            <th>لات</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($current_input_list as $item)
                            <tr>

                                <td>{{++$row}}
                                </td>
                                <td>
                                    {{$item->material->caption??"***"}}
                                </td>
                                <td>
                                    {{$item->goods_kind->caption??""}}
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
                                    <input name="data[input][{{$item->id}}]" type="text" style="width: 140px"
                                           value=""
                                    >

                                </td>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>

