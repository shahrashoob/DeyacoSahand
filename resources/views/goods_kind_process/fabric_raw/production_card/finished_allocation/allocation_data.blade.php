@extends('layouts.admin._master',["no_persian"=>1])
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> اطلاعات سامانه در زمان تخصیص {{$allocation->id}}
                    </h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>تاریخ و ساعت</th>
                                <th>نوع اطلاعات</th>
                                <th>مقدار</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($current_input_list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->id}}</td>
                                    <td>{{$item->datetime()}} </td>
                                    <td>
                                        {{$item->allocation_data_type->caption??""}}
                                    </td>
                                    <td>{{$item->float_value??""}} , {{$item->string_value??""}}</td>


                                </tr>
                                @if($item->data )
                                    <tr>
                                        <td colspan="6">
                                            @if($item->allocation_data_type_id!=200)
                                            <div class="center">
                                                <a href="#!" data-toggle="collapse" data-target="#s{{$item->id}}"
                                                   aria-expanded="true" aria-controls="s{{$item->id}}" class="">
                                                    مشاهده جزئیات
                                                </a>
                                            </div>

                                            <div class="col-md-12 collapse " id="s{{$item->id}}">
                                                <pre id="pre{{$item->id}}"
                                                     style="font-size: 16px; text-align: left;direction: ltr; background: #bbb3b3"></pre>
                                                <script>
                                                    var data = @php echo ($item->data); @endphp;
                                                    document.getElementById("pre{{$item->id}}").innerHTML = JSON.stringify(data, null, 4);
                                                </script>
                                            </div>
                                            @else
                                                <div class="center">
                                                    <a target="_blank" href="{{route("utility.json_view.allocation_data_type_200",[0,0,$allocation->id])}}">مشاهده جزئیات</a>

                                                </div>



                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>

                        </table>
                    </div>


                </div>
                <div class="col-md-12 center">
                    <a href="{{URL::previous()}}" class="btn btn-outline-dark">بازگشت</a>

                </div>
            </div>
        </div>
    </div>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
