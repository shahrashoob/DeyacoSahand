<div class="table-responsive" style="margin: auto; ">
    <table class="table table-styling center">
        <tr>
            <td colspan="3"> وضعیت بسته بندی ها</td>
        </tr>
        <tr>
            <th></th>
            <th>تعداد کل</th>
            <th></th>

        </tr>
        <tr>
            <th> کل بسته بندی ها</th>
            <td>{{$count_all_packing_form}}</td>
            <td></td>
        </tr>
        @if(isset($status_list["524000401"]))
            <tr class="alert-success">
                <th>
                    <a href="{{route("wh.warehouse_handling.dashboard.packing_form_list",[$warehouse_handling,524000401])}}">بسته
                        بندی های خوانده شده (داخل انبار)</a></th>
                <td>{{$status_list["524000401"]}}</td>
                <td>

                </td>
            </tr>
        @endif
        @if(isset($status_list["524000402"]))
            <tr class="alert-danger">
                <th>
                    <a href="{{route("wh.warehouse_handling.dashboard.packing_form_list",[$warehouse_handling,524000402])}}">بسته
                        بندی های خوانده شده (خارج از انبار)</a></th>
                <td>{{$status_list["524000402"]}}</td>
                <td>
                    @if(isset($warehouse_handling_form->input_form->code))
                        <a target="_blank"
                           href="{{route("wh.dashboard.show_form",$warehouse_handling_form->input_form)}}">{{$warehouse_handling_form->input_form->code??""}}</a>
                    @endif

                </td>
            </tr>
        @endif
        @if(isset($status_list["524000403"]))
            <tr class="alert-danger">
                <th>
                    <a href="{{route("wh.warehouse_handling.dashboard.packing_form_list",[$warehouse_handling,524000403])}}">بسته
                        بندی های خوانده نشده (داخل انبار)</a></th>
                <td>{{$status_list["524000403"]}}</td>
                <td>
                    @if(isset($warehouse_handling_form->output_form->code))
                        <a target="_blank"
                           href="{{route("DCEF_QR",[$warehouse_handling_form->output_form,$warehouse_handling_form->output_form->random])}}">{{$warehouse_handling_form->output_form->code??""}}</a>
                    @endif
                </td>
            </tr>
        @endif
        @if(isset($status_list["524000404"]))
            <tr class="alert-danger">
                <th>
                    <a href="{{route("wh.warehouse_handling.dashboard.packing_form_list",[$warehouse_handling,524000404])}}">بسته
                        بندی های خوانده شده (داخل انبار دیگر)</a></th>
                <td>{{$status_list["524000404"]}}</td>
                <td>
                </td>
            </tr>
        @endif
        @if(isset($status_list["524000405"]))
            <tr class="alert-danger">
                <th>
                    <a href="{{route("wh.warehouse_handling.dashboard.packing_form_list",[$warehouse_handling,524000405])}}">
                        بسته بندی های خوانده شده (کد نامعتبر)</a></th>
                <td>{{$status_list["524000405"]}}</td>
                <td></td>
            </tr>
        @endif
        @if(isset($status_list["524000406"]))
            <tr class="alert-danger">
                <th>
                    <a href="{{route("wh.warehouse_handling.dashboard.packing_form_list",[$warehouse_handling,524000406])}}">
                        بسته بندی های خوانده شده (وضعیت نامعتبر)</a></th>
                <td>{{$status_list["524000406"]}}</td>
                <td></td>
            </tr>
        @endif

        @if(isset($status_list["524000407"]))
            <tr class="alert-danger">
                <th>
                    <a href="{{route("wh.warehouse_handling.dashboard.packing_form_list",[$warehouse_handling,524000407])}}">بسته
                        بندی های خوانده نشده (وضعیت نامعتبر)</a></th>
                <td>{{$status_list["524000407"]}}</td>
                <td></td>
            </tr>
        @endif

        @if(isset($status_list["524000408"]))
            <tr class="alert-danger">
                <th>
                    <a href="{{route("wh.warehouse_handling.dashboard.packing_form_list",[$warehouse_handling,524000408])}}">بسته
                        بندی های خوانده شده (در انتظار تایید انبار)</a></th>
                <td>{{$status_list["524000408"]}}</td>
                <td></td>
            </tr>
        @endif


    </table>
    {{--    @if(isset($error_insert) && $error_insert)--}}
    {{--        <div class="alert alert-danger">--}}
    {{--            خطا--}}
    {{--        </div>--}}
    {{--    @endif--}}
    @if(isset($warehouse_handling_packing_form) && $warehouse_handling_packing_form)
        <table class="table table-styling center">
            <tr>
                <th>کد بسته بندی</th>
                <th>انبار</th>
                <th>مقدار کل</th>
                @if($warehouse_handling->check_diff_in_amount)
                    <th>مقدار کل (انبارگردانی) </th>
                @endif
                <th>وضعیت بسته بندی</th>
                <th>وضعیت انبار گردانی</th>
                <th></th>
            </tr>
            <tr>
                <td>
                    {{$warehouse_handling_packing_form->packing_form->code??($warehouse_handling_packing_form->packing_form_id+1000)}}
                </td>
                <td>{{$warehouse_handling_packing_form->packing_form->warehouse->caption??""}}</td>

                <td>{{$warehouse_handling_packing_form->packing_form?$warehouse_handling_packing_form->packing_form->getFinalAmount():""}}</td>
                @if($warehouse_handling->check_diff_in_amount)
                    <td>{{$warehouse_handling_packing_form->final_amount}} </td>
                @endif
                <td>{{$warehouse_handling_packing_form->packing_form->status->caption??""}}</td>
                <td>{{$warehouse_handling_packing_form->status->caption??""}}</td>
                <td>
                    <a href="{{route("fabric_raw.packing_form.print_qr.index",[$warehouse_handling_packing_form->packing_form_id,"wh.warehouse_handling.dashboard.view",$warehouse_handling->id])}}" ><i class="fa fa-print"></i></a>
                </td>

            </tr>
        </table>
    @endif
    @if(isset($packing_form_reading) && $packing_form_reading)
        <table class="table table-styling center alert-danger">
            <tr>
                <th>کد بسته بندی</th>
                <th>مقدار کل</th>
                <th>انبار</th>
                <th>وضعیت بسته بندی</th>
                <th>وضعیت انبار گردانی</th>
                <th></th>
            </tr>
            <tr>
                <td>
                    <a href="{{route("fabric_raw.packing_form.view",$packing_form_reading)}}"
                    target="_blank"
                    >
                        {{$packing_form_reading->code}}
                    </a>

                </td>
                <td>{{$packing_form_reading->getFinalAmount()}}</td>
                <td>{{$packing_form_reading->warehouse->caption??""}}</td>
                <td>{{$packing_form_reading->getStatus()}}</td>
                <td>وضعیت نامعتبر</td>
                <td>
                    @switch($packing_form_reading->status_id)

                        @case(7007007)


                        @break
                    @endswitch
                </td>

            </tr>
        </table>
    @endif
</div>
