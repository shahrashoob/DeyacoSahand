<div class="{{ isset($class_col) ? $class_col : 'col-md-6 offset-md-6' }}">
    <div class="form-group">
        <label>{{$lable??$label??""}}</label>
        @include('component.input._attention')
        <input
            id="{{ $id }}_value"
            name="{{ $id }}_value"
            readonly
            data-jdp
            data-jdp-miladi-input="{{ $id }}"
            {{--            data-jdp-min-date="today"--}}
            {{isset($min_date) ? "data-jdp-min-date=$min_date":""}}
            {{isset($max_date) ? "data-jdp-max-date=$max_date":""}}
            {{isset($hasTime) ? ($hasTime=="only-time"?"data-jdp-only-time='1'":""):"data-jdp-only-date"}}
            type="text"
            @if(isset($value) && $value!="")
                value="{{
                isset($hasTime) ?
                 ($hasTime=="only-time"?$value:jdate( \Carbon\Carbon::parse($value)->timestamp)->format('%Y/%m/%d H:i')):
                    jdate( \Carbon\Carbon::parse($value)->timestamp)->format('%Y/%m/%d')
                }}"
            @endif

            class="form-control jalali_datepicker_change"
        />

        <input type="hidden" id="{{ $id }}" name="{{ $id }}" value="{{$value??""}}">
    </div>
</div>


