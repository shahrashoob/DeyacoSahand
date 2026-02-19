@include('accounting.contract.print._header')
@include('accounting.contract.print._info')
@if(in_array($employment->cooperation_type_id,[2,21]) )
<table>
    <tr>
        <th></th>
        <th></th>
    </tr>
    <tr>

        <td style="padding: 15%; text-align:center;width: 50%">محل امضا و اثر انگشت<br/>
            {{ $employment->worker->fullname()}}
        </td>
        <td style=" padding: 15%;text-align:center;width: 50%">محل مهر و امضای<br/>
            {{ $post_user->post->caption}}
        </td>
    </tr>
</table>
@endif
@include('accounting.contract.print._footer')

