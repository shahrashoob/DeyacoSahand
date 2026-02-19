<!--  -->
@if ($errors->any())

    @foreach ($errors->all() as $error)
        <div class="alert alert-danger ">{!!$error!!}</div>

    @endforeach

@endif
@if(isset($type) && $type='public_1')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }} <br/>
            @endforeach
        </div>
    @endif
@endif
<!-- / -->




@if($message = \Session::get('success'))
    <div class="alert alert-success ">{!!$message!!}</div>
@endif

@if($message = \Session::get('warning'))
    <div class="alert alert-warning ">{!!$message!!}</div>
@endif

@if($message = \Session::get('info'))
    <div class="alert alert-info ">{!!$message!!}</div>
@endif
