@php
    $m=isset($m)?(int)$m:-1;
    $h=isset($h)?(int)$h:-1;
@endphp

<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">


   <table>
    <tr>
        <td rowspan="2" style="vertical-align:bottom">{{$lable??$label}}</td>
        <td>دقیقه</td>
        <td>ساعت</td>

       </tr>
       <tr>

           <td >
            <select name="{{$id}}_m" style="width: 50px;text-align: center" id="{{$id}}_h">

                @for ($i = 0; $i < 10; $i++)
                <option @if($i==$m) selected @endif value="0{{$i}}">
                    0{{$i}}
                </option>
                @endfor
                @for ($i = 10; $i < 61; $i++)
                <option @if($i==$m) selected @endif  value="{{$i}}">
                    {{$i}}
                </option>
                @endfor

            </select>
           </td>
           <td>
            <select name="{{$id}}_h" style="width: 50px;text-align: center" id="{{$id}}_h">
                <option></option>
                @for ($i = 0; $i < 10; $i++)
                <option @if($i==$h) selected @endif  value="0{{$i}}">
                    0{{$i}}
                </option>
                @endfor
                @for ($i = 10; $i < 24; $i++)
                <option @if($i==$h) selected @endif  value="{{$i}}">
                    {{$i}}
                </option>
                @endfor

            </select>
        </td>
       </tr>

   </table>

<br/>

</div>
