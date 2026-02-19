@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@include("component.formatDecimal9")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> مشاهده مقادیر مورد نیاز (فرم شاهد) برای تولید کالا - {{$machine->fullCaption()}}
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric.finishing_machine.machine.control_sample.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf


                        <div class="content">


                            <div class="w-100 center" >
                                <h5>لیست مواد اولیه</h5>
                            </div>

                            <table class="table table-styling">

                                <tr>

<th>ردیف</th>
                                    <th >
                                        کد ماده اولیه

                                    </th>
                                    <th >
                                        نام ماده اولیه

                                    </th>
                                    <th >
                                        مقدار مورد نیاز

                                    </th>

                                    <th >
                                        واحد کالا

                                    </th>

                                </tr>

                                @php $row=0; @endphp
                                @foreach($current_input_list as $item)
                                    <tr>

                                        <td >
                                            {{++$row}}
                                        </td>
                                        <td >
                                            {{$item->material->code??"***"}}
                                        </td>
                                        <td >
                                            {{$item->material->caption??"***"}}
                                        </td>

                                        <td >

                                            {{formatDecimal9($item->amount_required)}}
                                        </td>

                                        <td >
                                            {{$item->material->unit->caption??"***"}}
                                        </td>

                                    </tr>
                                @endforeach


                            </table>




                        </div>


                        <br/>

                        <div class="col-md-12">

                            <a href="{{route("fabric.finishing_machine.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>
                                <div class="btn-group mb-2 mr-2">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">مشاهده مقادیر مورد نیاز (فرم شاهد)
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start"
                                         style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">

                                            <a class="dropdown-item"
                                               href="{{route("fabric.finishing_machine.machine.control_sample.print_download_form",[$machine,"download"])}}">دانلود</a>

                                        <a class="dropdown-item"
                                           href="{{route("fabric.finishing_machine.machine.control_sample.print_download_form",[$machine,"print"])}}">پرینت</a>

                                    </div>
                                </div>
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
    <style>
        .option{
            width: 150px !important;
            text-align: center;
            padding: 2px 5px
        }
    </style>
@endsection


@section("scripts")

    <script>

        $('#form1').validate({
            rules: {
                firstname: "required",

            }
        });

    </script>
@endsection
