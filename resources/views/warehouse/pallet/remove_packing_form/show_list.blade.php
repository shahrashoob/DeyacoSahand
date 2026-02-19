@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بسته بندی")

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> حذف بسته بندی های زیر از پالت {{$pallet->code}}   </h5>
                </div>
                <div class="card-block">
                    @include("component.input._lable",["id"=>"","lable"=>"تعداد بسته بندی موجود در پالت","value"=>$pallet->items()->count() ." عدد","class_col"=>"col-md-12"])
                    @include("component.input._lable",["id"=>"","lable"=>" تعداد بسته بندی  خوانده شده جهت خروج از پالت","value"=>count($packing_forms) ." عدد","class_col"=>"col-md-12"])

                    <form id="form1"
                          action="{{route("wh.pallet.remove_packing_form.confirm",[$pallet])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        @include("component.input._hidden",["id"=>"type_of_remove","value"=>0])

                        <div class="table-responsive">
                            <table class="table table-styling" style="text-align: center!important;">
                                <thead>
                                <tr>

                                    <th>ردیف</th>

                                    <th> شماره بسته بندی</th>
                                    <th> نوع بسته بندی</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($packing_forms as $item)
                                    <tr @if(isset($repetitive_packing_forms[$item->id])) class="alert-warning" @endif>
                                        <td>{{$row++}}</td>

                                        <td>
                                            {{$item->getCode()}}

                                        </td>
                                        <td>
                                            {{$item->packing_type->caption??"---"}}
                                        </td>


                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <div class="col-md-12">
                            <a href="{{route("wh.pallet.dashboard.view",$pallet)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button class="btn btn-danger dropdown-toggle dropdown-toggle" type="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-trash"></i>
                                حذف بسته بندی ها از پالت
                            </button>
                            <div class="dropdown-menu" x-placement="bottom-start"
                                 style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                                <button type="submit" onclick="return remove_function(1)" class="dropdown-item" href="#"
                                > حذف بسته بندی از پالت و انتقال به انبار
                                </button>
                                <button type="submit" onclick="return remove_function(2)" class="dropdown-item" href="#"
                                > حذف و افزودن به پالت جدید
                                </button>

                            </div>
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
                "packing_type_id": "required",

            }
        });

        function remove_function(type) {
            $("#type_of_remove").val(type);
            return 1;
        }
    </script>
@endsection
