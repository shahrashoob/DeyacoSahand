<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">

    </div>
    <table style="">
        <tr>
            <td colspan="3">
                {{$software_name}}
            </td>
        </tr>
        <tr>
            <td colspan="3" >
                ماشین: {{$machine->caption}}
            </td>
        </tr>
        <tr>
            <td colspan="3">
                سریال کارت تولید: {{$production->serial()}}
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-size: 14px">


            </td>
        </tr>


        @foreach($current_input_list as $item)
            <tr>
                <td style="width: 30px">
                     {{$item->input_line_code}}
                </td>
                <td colspan="2">
                    {{$item->material->caption??"***"}}
                </td>


            </tr>
        @endforeach

        <tr>
            <td>
                متراژ(ها)
            </td>
            <td colspan="2">

                @for($k=0; $k<$machine_allocation->max_number_of_doffs;$k++)

<input type="checkbox" style="font-size: 14px"> {{$machine_allocation->amount_of_each_doffs}} متر


                @endfor
            </td>
        </tr>


        <tr>
            <td colspan="3">
                سازمان دیجیتال دیاکو
            </td>
        </tr>
    </table>


</div>
