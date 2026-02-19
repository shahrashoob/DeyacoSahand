@if($form)

    @php $list_status=$form->getSoftwareTransferFormStatus($financial_software_trans_kind_type_id);@endphp

    @if(isset($list_status[ 5103300]))
{{--        <a href="{{route("wh.financial_software.log",[$form->id,$financial_software_trans_kind_type_id,$list->currentPage()])}}">--}}
    <span class="text-success">
         <i class="fa fa-check "></i>
        {{$list_status[ 5103300]->count}}
    ثبت موفق
    </span>
{{--        </a>--}}
        <br/>
    @endif
    @if(isset($list_status[ 5103800]))
{{--        <a href="{{route("wh.financial_software.log",[$form->id,$financial_software_trans_kind_type_id,$list->currentPage()])}}">--}}

    <span class="text-danger">
          <i class="fa fa-times "></i>
  {{$list_status[ 5103800]->count}}
        بررسی ناقص


        </span>
{{--        </a>--}}
        <br/>
    @endif
    @if(isset($list_status[ 5103900]))
{{--        <a href="{{route("wh.financial_software.log",[$form->id,$financial_software_trans_kind_type_id,$list->currentPage()])}}">--}}
    <span class="text-dark">
         <i class="fa  fa-redo "></i>
            {{$list_status[ 5103900]->count}}
            در حال بررسی مجدد
        </span>
{{--        </a>--}}
        <br/>
    @endif
{{--    @if(isset($list_status[ 5103100]))--}}
{{--        <a href="{{route("wh.financial_software.log",[$form->id,$financial_software_trans_kind_type_id,$list->currentPage()])}}">--}}
{{--    <span class="text-warning">--}}
{{--         <i class="fa fa-minus "></i>--}}


{{--    </span>--}}
{{--        </a>--}}
{{--        <br/>--}}
{{--    @endif--}}

@else

    @if($status_id== 5103300)
        <a href="{{route("wh.financial_software.log",[$financial_software_transfer_form_id,$financial_software_trans_kind_type_id,$page])}}">
    <span class="text-success">
    <i class="fa fa-check "></i> ثبت موفق
    </span>
        </a>
    @elseif($status_id ==5103800)
        <a href="{{route("wh.financial_software.log",[$financial_software_transfer_form_id,$financial_software_trans_kind_type_id,$page])}}">
    <span class="text-danger">
        <i class="fa fa-times "></i> بررسی ناقص
        </span>
        </a>
    @elseif($status_id ==5103900)
        <a href="{{route("wh.financial_software.log",[$financial_software_transfer_form_id,$financial_software_trans_kind_type_id,$page])}}">
    <span class="text-dark">
        <i class="fa  fa-redo "></i> در حال بررسی مجدد
        </span>
        </a>
    @else
        <a href="{{route("wh.financial_software.log",[$financial_software_transfer_form_id,$financial_software_trans_kind_type_id,$page])}}">
    <span class="text-warning">
    <i class="fa fa-minus "></i>
    </span>
        </a>
    @endif


@endif
