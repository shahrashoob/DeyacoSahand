<div class="content">


    <table style="width: 100%">


        <tr>

            <td>
                COL NO:
                {{$packing_form->items->first()->product->getPropertyValue(220338,"value",false,false)}}


            </td>
        </tr>

        <tr>
            <td>
                ROLL NO:
                {{$packing_form->getCode()}}


            </td>
        </tr>

        <tr>
            <td>
                DSGN CODE:
                {{$packing_form->items->first()->product->getPropertyValue(220337,"value",false,false)}}


            </td>
        </tr>

        <tr>
            <td>
                YDS:
                {{round($packing_form->getFinalAmount() * 1.09361)}}




            </td>
        </tr>

        <tr>

            <td> MTRS: {{$packing_form->getFinalAmount()}}




            </td>
        </tr>

        <tr>
            <td> KGS: {{round($packing_form->gross_weight,2)}}




            </td>
        </tr>

        <tr>
            <td  >
                MADE IN CHINA


            </td>

        </tr>

    </table>
</div>
<div style="width: 100%; padding-top: 10px; text-align: center" >
    <div style="font-size: 0.02px">
        {!! $barcode !!}
    </div>
</div>
