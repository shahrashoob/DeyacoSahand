
        <button  class="btn btn-info" data-toggle="collapse" data-target="#{{$id??"collapseExample"}}"
           aria-expanded="false" aria-controls="{{$id??"collapseExample"}}">
            <span class="fa fa-eye"> مشاهده
             @if(isset($caption))
                    {{$caption}}
                @else
                    مشخصات کالا
                @endif

            </span>
        </button>




<div class="collapse col-md-12" id="{{$id??"collapseExample"}}">


    <div class="alert alert-primary ">

        <div class="row">

            @foreach($product->property_value as $item)

                <span class="col-md-2">
                                        {{$item->property->caption??""}}:
                                        <b>
                                            {{$item->getValue()}}
                                        </b>
                                    </span>
            @endforeach
        </div>
    </div>

</div>

