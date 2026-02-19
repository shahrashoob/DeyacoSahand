@extends('layouts.admin._master')

@section('page_header_title',"داشبورد ماشین آلات")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> درخواست مواد اولیه برای {{$machine->getCode()}} - {{$machine->caption}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route($route_path."submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="w-100"></div>

                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-styling" style="text-align: center!important;">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th></th>
                                        <th>شماره تخصیص</th>
                                        <th>شماره کارت تولید</th>
                                        <th>کالا</th>
                                        <th> مقدار</th>
                                        <th>تاریخ رزرو</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($reserve_allocation_list as $item)
                                        <tr>
                                            <td>{{$item->allocation->priority_number}}</td>
                                            <td>
                                                <input type="checkbox" name="allocation[{{$item->allocation_id}}]"
                                                       value="{{$item->allocation_id}}">
                                            </td>
                                            <td>
                                                {{$item->allocation_id}}
                                            </td>
                                            <td>
                                                {{$item->production->serial()}}
                                            </td>
                                            <td>
                                                {{$item->product->fullCaption()}}
                                            </td>
                                            <td>
                                                {{$item->production->get_allocation_amount($machine->id,1,$item->allocation_id,false,$item->allocation->allocation_unit_type_id)}} {{$item->allocation->allocation_unit_type($item->product)}}
                                            </td>
                                            <td>
                                                {{$item->get_datetime()}}
                                            </td>


                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <a href="{{route($dashboard_route."view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary">ثبت و ادامه</button>
                        </div>


                    </form>
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
                "allocation_id": "required",
            }
        });
    </script>
@endsection
