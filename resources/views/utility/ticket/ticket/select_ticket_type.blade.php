@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","کارتابل تیکت ")
@section("content")
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-primary" role="alert">
            <p> شما می توانید با انتخاب بخش مناسب درخواست خود را ارسال نمایید.</p>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>انتخاب بخش</h5>
            </div>
            @foreach($list as $item)
            <div class="card-body">
                <h3><a href="{{route("utility.ticket.create",$item->id)}}"><span class="fa fa-fas fa-envelope"></span> {{$item->caption}} </a> </h3>
                    {!! $item->description !!}

                    </div>
                @endforeach
        </div>
    </div>
</div>
@endsection
