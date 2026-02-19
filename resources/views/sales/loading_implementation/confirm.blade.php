@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  مدیریت تامین کنندگان  ")

@section('content')

    <form id="form1" autocomplete="off" action="{{route("sales.loading_implementation.submit_confirm")}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">

            <div class="col-md-12" id="card-block">

                <div class="card">
                    <div class="card-header">
                        <h5> فرم ثبت تامین ویژه دوره پیاده سازی </h5>
                    </div>
                    <div class="card-block" id="card-block">

                        <div class="row">

                            <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th></th>
                                <th>نام مشتری </th>
                                <th>نام کالا</th>
                                <th>مقدار کل</th>
                                <th> مقدار فرعی</th>
                                <th>نوع انبارش کالا</th>
                                <th> نوع بسته بندی</th>
                                <th>درجه</th>
                                <th>لات</th>
                                <th>تعداد بسته بندی</th>
                               
                            </tr>
                            </thead>
                         @php $row=0;@endphp
                                     @foreach ($request_data as $product_id=>$request_item)
                                            <tr>

                                                <td>{{++$row}}</td>

                                                <td><a href="{{route("sales.loading_implementation.delete",$product_id)}}" class="text-danger"><i class="fa fa-trash"></i></a></td>
                                                <td>{{$customers[$product_id]->caption}} </td> 
                                                <td>{{$products[$product_id]->fullCaption()}}</td>
                                                <td>
                                                    {{$amounts[$product_id]." ".$products[$product_id]->unit->caption}}

                                                </td>


                                                <td>
                                                    @if($products[$product_id]->sub_unit)
                                                        {{ $sub_amounts[$product_id]." ".$products[$product_id]->sub_unit->caption }}
                                                    @endif
                                                </td>
                                                <td>{{$warehouse_storage_types[$product_id]->caption}}</td>  

                                                <td>
                                                    @if($warehouse_storage_types[$product_id]->id == 2) 
                                                    {{$packing_types[$product_id]->code}}
                                                    @endif
                                                </td>

                                                <td>
                                                    {{$degrees[$product_id]->caption}}   
                                                </td>




                                                <td>
                                                    {{ $lot_number_codes[$product_id] }}  
                                                </td>

                                                <td>
                                                    @if($warehouse_storage_types[$product_id]->id== 2)  
                                                     {{ $packing_form_numbers[$product_id] }}
                                                    @endif
                                                </td>

                                            </tr>
                                @endforeach
                             
                            <div class="w-100"></div>
                        </div>
                    </table>
              


                    </div>
                </div>
            </div>
            @if($customers[$product_id]->input_form_loading_require)
                <div class="col-md-12">
                    <h5> اطلاعات بارگیری</h5>
                </div>
                @include("utility.transport.public._create_transport_view")
            @endif

            <div class="col-md-12 center">
                <a href="{{route("sales.loading_implementation.index")}}"
                   class="btn btn-outline-dark">بازگشت</a>
                <button type="submit" class="btn btn-success"> تایید نهایی تامین</button>
            </div>


        </div>
    </form>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection

@section("scripts")
    @include("component.script_function.get_new_option")
    <script>


        $('#form1').validate({
            rules: {
                "product_id_auto": "required",
            }
        });
    </script>
@endsection