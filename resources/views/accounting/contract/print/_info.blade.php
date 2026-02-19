
@php $row1=0;@endphp
@php $row2=0;@endphp
@foreach($contract_clause_type_list as $clause_type_id=>$caption)
   <b style="font-size: 14px"> ماده {{++$row1}}) {{$caption}}</b>

    @if(isset($article_list[$clause_type_id]))

        @php $row2=0;@endphp
        @foreach( $article_list[$clause_type_id]  as $item)
            @php $clause_count = count($article_list[$clause_type_id]); @endphp
            @if($clause_count == 1)
                <p style="margin-right: 15px;font-size: 12px">  @include('accounting.contract.print._article',['article'=>$item ,'keys_id'=>$keys_id])</p>
            @else
                <p style="margin-right: 15px;font-size: 12px">{{$row1}}-{{++$row2}}) @include('accounting.contract.print._article',['article'=>$item ,'keys_id'=>$keys_id])  </p>
            @endif

        @endforeach
    @endif
@endforeach

