<html>

<table style=" ">
    <thead>
    <tr>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام ماشین</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">متراژ</th>


    </tr>

    </thead>
    <tbody>
    @foreach($list as $item)

        <tr>

            <td style="text-align:center; border:2px solid #000000;"> {{$item->caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{$item->amount??""}}</td>


        </tr>

    @endforeach
    </tbody>

</table>
</html>
