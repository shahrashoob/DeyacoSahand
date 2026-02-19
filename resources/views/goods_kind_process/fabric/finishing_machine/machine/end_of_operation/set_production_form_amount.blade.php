@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> پایان عملیات {{$machine->fullCaption()}}
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric.finishing_machine.machine.end_of_operation.confirm_production_form_amount",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                            <div class="table-responsive">
                                <div class="center"><h4> ثبت مقدار نهایی فرم تولید</h4></div>
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <th>ردیف</th>
                                        <th>شماره ردیف فرم</th>

                                        <th>کارت تولید</th>
                                        <th>نام مواد اولیه</th>
                                        <th>کد مواد اولیه</th>
                                        <th>مقدار فرم تولید</th>

                                        <th>مقدار نهایی</th>

                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($production_form->items as $production_form_item)
                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>{{$production_form_item->getCode()}}</td>
                                            <td>{{$production_form_item->product->code}}</td>
                                            <td>{{$production_form_item->product->caption}}</td>
                                            <td>{{$production_form_item->production->serial}}</td>
                                            <td>{{$production_form_item->final_amount}}
                                                {{$production_form_item->product->unit->caption}}
                                            </td>
                                            <td>
                                            <input type="number" style="width: 60px; " name="production_form_item[{{$production_form_item->id}}]" value="">

                                                {{$production_form_item->product->unit->caption}}
                                            </td>


                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endforeach

                                    </tbody>

                                </table>
                            </div>


                        <div class="w-100"></div>





                        <div class="col-md-12">

                            <a href="{{route("fabric.finishing_machine.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>
                            <button type="submit" class="btn btn-success">تایید</button>
                        </div>


                    </form>
                </div>
            </div>

        </div>
        <div class="col-md-12 center">


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
                "var": "required",
            }
        });
    </script>
@endsection
