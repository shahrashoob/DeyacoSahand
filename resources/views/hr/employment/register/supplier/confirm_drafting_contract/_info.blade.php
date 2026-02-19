@extends('hr.employment.register.personal_type._layout_pills')

@section("content_pill")

    <p class="alert alert-warning">{{$contract->guide ??""}}</p>


    @include('accounting.contract.print._header')
    @include('accounting.contract.print._info')
    @include('accounting.contract.print._footer')

    <div class="btn btn-outline-primary shadow-2 mb-4">
        <a  href="{{ route('hr.employment.register.supplier.confirm_drafting_contract.print',$employment->key) }}"> دانلود قرارداد</a>
    </div>
@endsection
