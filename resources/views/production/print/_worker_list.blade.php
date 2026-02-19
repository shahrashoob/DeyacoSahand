<table class="table table-styling">
    <tr>
        <td></td>
        <td>تاریخ</td>
        <td>ساعت شروع</td>
        <td>ساعت پایان</td>
        <td>زمان شیفت </td>
        @if(isset($has_sub_index))
        <td>شاخص خرد</td>
        @endif
       
    </tr>
    @php $i=1; @endphp
    @foreach($production->datetimes as $datetime)

    <tr>
        <td style="font-weight: bold">
            شیفت {{$i++}}
        </td>
        <td>
            {{jdate( \Carbon\Carbon::parse($datetime->start_datetime)
            ->timestamp)->format('Y/n/j')}}
        </td>
       <td>
        {{jdate( \Carbon\Carbon::parse($datetime->start_datetime)
            ->timestamp)->format('H:i:s')}}
       </td>
       <td>
        {{jdate( \Carbon\Carbon::parse($datetime->end_datetime)
            ->timestamp)->format('H:i:s')}}  
       </td>
       <td>
        {{floor($datetime->shift_time/60).":".$datetime->shift_time% 60}}
       </td>
       
       @if(isset($has_sub_index))
            <td>
                {{$datetime->sub_productivity_index}}
            </td>
        @endif
        
    </tr>

    <tr>
        <td colspan="{{isset($has_sub_index)?6:5}}" style="text-align: right">

            <b>شاغلین:</b> 
            @foreach($datetime->production_worker as $pworker )
            {{$pworker->worker->fullname()." (".$pworker->post_name.")"}} , 
           
            @endforeach
         </td>
    </tr>
                
    @endforeach
</table>