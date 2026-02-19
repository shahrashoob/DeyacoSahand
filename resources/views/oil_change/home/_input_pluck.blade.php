<div class="pluck-input">
    <table>
        <tr>
            <td id="pt_1" >
                @include("component.input._number",["id"=>"city_id",'label'=>" ایران ","value"=>$car->city_id??"","class_col"=>"col-md-12","autofocus"=>1])

            </td>
            <td  id="pt_2" >

                @include("component.input._number",["id"=>"number1",'label'=>" <br/> ","value"=>$car->number1??"","class_col"=>"col-md-12","autofocus"=>1])

            </td>
            <td  id="pt_3" >
                <div class="col-md-12">
                    <div class="form-group">
                        <label> <br> </label>
                        <select id="alphabet_id" name="alphabet_id" class="form-control">
                            {!! $alphabet_option !!}
                        </select></div>
                </div>

            </td>
            <td  id="pt_4">
                @include("component.input._number",["id"=>"number2",'label'=>" <br/> ","value"=>$car->number2??"","class_col"=>"col-md-12","autofocus"=>1])

            </td>
            <td  id="pt_5" >
            </td>
        </tr>
    </table>
</div>
