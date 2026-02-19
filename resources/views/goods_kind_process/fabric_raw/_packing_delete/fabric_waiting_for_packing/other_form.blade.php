@extends('layouts.admin._master')

@section('page_header_title',"داشبورد بسته بندی  ")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success">
                اطلاعات بسته بندی با موفقیت ثبت گردید، شما می توانید تکه پارچه های پیشنهادی زیر را نیز بر روی حامل قرار دهید.
            </div>
        </div>


        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>تکه پارچه های مشابه که می توانید در حامل
                        {{$fabric_raw_grading->packing_form_item->packing_form->carrier->getCaption()}}

                        قرار بگیرند</h5>
                </div>
                <div class="card-block">
                    <div class="row">


                        <div class="table-responsive">
                            <table class="table table-styling" style="text-align: center!important;">
                                <thead>
                                <tr>

                                    <th>#</th>
                                    <th>شماره ردیف</th>
                                    <td>وضعیت</td>
                                    <td>متراژ سیستم</td>
                                    <td>متراژ کنترل کیفیت</td>

                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            <a href="{{route("fabric_raw.packing.fabric_waiting_for_packing.view_other_form",[$item,$fabric_raw_grading->packing_form_item->packing_form])}}">
                                                {{$item->code}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$item->status->caption??""}}
                                        </td>
                                        <td>
                                            {{$item->amount}}
                                        </td>
                                        <td>
                                            {{$item->amount_after_control}}
                                        </td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "packing_type_id_auto": "required",
                "carrier_id": "required",
                "final_amount": "required",
            }
        })
    </script>
@endsection
