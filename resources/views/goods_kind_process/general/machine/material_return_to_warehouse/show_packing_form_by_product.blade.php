@extends('layouts.admin._master') @section('page_header_title',"داشبورد  تولید")
@section('content')
    <div class="row">

@if(isset($master_packing_form_list))
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> لیست همه بسته بندی های
                            {{$product->fullCaption()}}
                            جهت برگشت به انبار</h5>
                    </div>
                    <div class="card-block" style="overflow: auto">


            <table class="table table-styling center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>کد بسته بندی</th>

                </tr>

                </thead>
                <tbody>
                @php $row=0;@endphp
                @foreach($master_packing_form_list as $packing_form )
                    <tr>
                        <td>{{++$row}}</td>
                        <td>{{$packing_form->code}}
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>

                    </div>
                </div>
            </div>
        @endif


    @if(isset($modification_packing_forms))
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست همه بسته بندی های
                        {{$status->caption}}
                        {{$product->fullCaption()}}
                        </h5>
                </div>
                <div class="card-block" style="overflow: auto">


                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>کد بسته بندی</th>
                            <th>وضعیت</th>
                            <th>وزن ناخالص <br/>(باقی مانده)</th>
                            <th>وزن خالص <br/>(باقی مانده)</th>
                            <th> تعداد بسته بندی فرعی <br/>(باقی مانده)</th>

                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($modification_packing_forms as $modification_packing_form )
                            <tr>
                                <td>{{++$row}}</td>
                                <td>{{$modification_packing_form->packing_form->code}}
                                </td>
                                <td>{{$modification_packing_form->consumed_status->caption}}</td>
                                <td>{{$modification_packing_form->gross_weight}}</td>
                                <td>{{$modification_packing_form->weight}}</td>
                                <td>{{$modification_packing_form->sub_packing_form_number}}</td>


                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    @endif

        <div class="col-md-12 center">
            <a href="{{ URL::previous() }}" class="btn btn-outline-dark">بازگشت</a>
        </div>

    </div>

@endsection
