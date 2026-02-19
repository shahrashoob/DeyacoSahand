@extends('layouts.admin._master') @section('page_header_title',"داشبورد  بافندگی")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5>اعلام نقص
                        برای ماشین {{$machine->caption??""}}</h5></div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route($route_path."submit",$machine)}}"
                          method="post" autocomplete="off" novalidate="novalidate"> @csrf
                        <div class="row">
                            <div class="w-25"></div>

                           <div class="col-md-6 center">
                               <table class="table">
                                   <tr class="col-md-12 center">
                                       <th>
                                       </th>
                                       <th>
                                           لیست نقص های احتمالی ماشین
                                       </th>
                                   </tr>
                                   @foreach($product_fault_sings as $item)
                                       <tr class="col-md-12 center">
                                           <td>
                                               <input type="checkbox"
                                                      name="product_fault_sings[{{$item->id}}]">
                                           </td>
                                           <td>{{$item->caption}}
                                           </td>
                                       </tr>
                                   @endforeach

                                   @foreach($machine_type_product_fault_sing as $item)
                                       <tr class="col-md-12 center">
                                           <td>
                                               <input type="checkbox"
                                                      name="machine_fault_sings[{{$item->id}}]">
                                           </td>
                                           <td>{{$item->caption}}
                                           </td>
                                       </tr>
                                   @endforeach

                               </table>
                           </div>
                            <div class="col-md-12 center">

                                <a href="{{route($dashboard_route."view",$machine)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-danger">
                                    ثبت نقص
                                </button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
    </div> @endsection @section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/> @endsection @section("scripts")
    <script> $('#form1').validate({
            rules: {
                "description": "required",
            }
        }); </script> @endsection
