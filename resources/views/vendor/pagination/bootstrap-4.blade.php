@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif
            @php $has_docts=false; @endphp
            @foreach ($elements as $element)
                @if (is_string($element)) @php $has_docts=true;@endphp@endif
            @endforeach
            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true" onclick="x()"><a href="#"><span class="page-link ">{{ $element }}</span></a></li>
               <script>
                   function x(){
                       let custome_page = prompt("لطفا صفحه مورد نظر را انتخاب کنید.");
                       let text;
                       if (custome_page == null || custome_page == "") {

                       } else {

                           window.location.href="{{Request::url()}}?page="+custome_page;
                       }
                   }
               </script>
                @endif

                {{-- Array Of Links --}}
            @php $element_array=0; @endphp
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @php $element_array++; @endphp
                    @if($element_array > 3 && $has_docts==true) @continue @endif
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif

            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
