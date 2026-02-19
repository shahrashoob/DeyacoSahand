@if(count($modification_temp_packing_forms) > 0)
    <div class="col-md-12 center"><h5>
            {{$caption_list}}
        </h5></div>
    <div class="col-md-12" style="overflow: auto">
        <table class="table table-styling center">
            <thead>
            <tr>
                <th>#</th>
                <td></td>
                <th>کد بسته بندی</th>
                <th>وضعیت</th>
                <th>وزن ناخالص <br/>(باقی مانده)</th>
                <th>وزن خالص <br/>(باقی مانده)</th>
                <th> تعداد بسته بندی فرعی <br/>(باقی مانده)</th>
                @if(isset($before_amount) && $before_amount)

                    <th>وزن ناخالص <br/>(قبل از ثبت)</th>
                    <th>وزن خالص <br/>(قبل از ثبت)</th>
                    <th> تعداد بسته بندی فرعی <br/>(قبل از ثبت)</th>
                    <th> مقدار نهایی <br/>(قبل از ثبت)</th>
                @endif
                @if(isset($show_product) && $show_product )
                    <th>نام اولین آیتم بسته بندی</th>
                @endif
                <th>مقدار مصرف</th>
                @if(isset($allow_show_cost) and $allow_show_cost)
                <th>بهای تمام شده <br/>بسته بندی (ریال)</th>
                @endif
            </tr>

            </thead>
            <tbody>
            @php $row=$modification_temp_packing_forms->firstItem();@endphp
            @foreach($modification_temp_packing_forms as $modification_packing_form )
                <tr>
                    <td>{{$row++}}</td>
                    <td>
                        @if($modification_packing_form->consumed_status_id != 6021103 && isset($allow_print) && $allow_print)
                            <a href="{{route($route_path."print_one_of_packing_form",[$machine,$modification_packing_form->packing_form->id,$modification_packing_form])}}"><i
                                        class="fa fa-print"></i></a>
                        @endif
                        @if(isset($allow_delete) && $allow_delete)
                            <a class="text-danger" onclick="return confirm('آیا از حذف بسته بندی اطمینان دارید؟')"
                               href="{{route($route_path."delete_one_of_packing_form",[$machine,$modification_packing_form->packing_form->id,$modification_packing_form])}}"><i
                                        class="fa fa-trash"></i></a>
                        @endif
                    </td>
                    <td>{{$modification_packing_form->packing_form->code}}
                    </td>
                    <td>{{$modification_packing_form->consumed_status->caption}}</td>
                    <td>{{$modification_packing_form->gross_weight}}</td>
                    <td>{{$modification_packing_form->weight}}</td>
                    <td>{{$modification_packing_form->sub_packing_form_number}}</td>
                    @if(isset($before_amount) && $before_amount )
                        <td>{{$modification_packing_form->before_gross_weight}}</td>
                        <td>{{$modification_packing_form->before_weight}}</td>
                        <td>{{$modification_packing_form->before_sub_packing_form_number}}</td>
                        <td>{{$modification_packing_form->before_amount}}</td>
                    @endif
                    @if(isset($show_product) && $show_product )
                        <td>{{$modification_packing_form->packing_form->items()->first()->product->fullCaption()}}</td>
                    @endif
                    <td> {{$modification_packing_form->consumed_amount}}</td>
                    @if(isset($allow_show_cost) and $allow_show_cost)
                    <td>
                        {{($modification_packing_form->material_cost)}}
                    </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="float-left">
            نمايش رکوردهای
            <b>{{$modification_temp_packing_forms->firstItem()}}</b>
            تا
            <b>{{$modification_temp_packing_forms->lastItem()}}</b>
            از
            <b>{{$modification_temp_packing_forms->total()}}</b>
            رکورد موجود


        </div>
        <div class="text-center">
            {{$modification_temp_packing_forms->links('pagination::bootstrap-4')}}
        </div>
    </div>
@endif
