<div id="production_info">
    @if(isset($production_list))
        <table class="table table-styling center">
            <tr>
                <th colspan="7">لیست کارت های تولید برای بسته بندی {{$packing_form->code}}</th>
            </tr>
            <tr>
                <th>سریال کارت تولید</th>
                <th>مقدار تخصیص</th>
                <th>مقدار<br/> کارت تولید</th>
                <th>مقدار <br/> تولید شده</th>
                <th>تعداد کل داف</th>
                <th>تعداد کل داف<br/> انجام شده</th>
                <th>وضعیت کارت تولید</th>
            </tr>

            @foreach($production_list as $item)
                <tr>
                    <td>{{$item->serial()}}</td>
                    <td>{{$item->number}}</td>
                    <td>{{$allocation_amount[$item->id]}}</td>
                    <td>{{$production_amount[$item->id]}}</td>
                    <td>{{$number_of_doffs_done[$item->id]}}</td>
                    <td>{{$number_of_doffs[$item->id]}}</td>
                    <th>{{$item->getStatus()}}</th>
                </tr>
            @endforeach


        </table>
    @endif
</div>

