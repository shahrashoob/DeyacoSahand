@extends('layouts.admin._master')
@section("page_header_title","داشبورد ".($machine_allocation->machine?"ماشین آلات ":"پیمانکاران")."-  ".
($machine_allocation->machine?$machine_allocation->machine->fullCaption():$machine_allocation->contractor->caption)
)
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  {{$machine_allocation->machine?"کارت تولید ":"دستور پیمان"}} {{$machine_allocation->production->serial()}}</h5>
                </div>
                <div class="card-block">


                    <div id="panel_packing_item">
                        @include("goods_kind_process.fabric_raw.packing_form._info_small",["packing_form"=>$packing_form])

                        <form id="form1"
                              action="{{route("production.public_module.register_production.submit_copy_packing_form",[$machine_allocation,$packing_form])}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate"
                              style="display: inline"
                        >
                            @csrf

                            <div class="col-md-12">
                                به تعداد


                                @include("component.input._number_sample",["id"=>"number_of_copy"])
                                بسته بندی از روی بسته بندی {{$packing_form->code}} کپی شود و در فرم تولید ثبت گردد.
                                <br/>
                                <br/>
                            </div>

                            <div class="col-md-12">
                                <a href="{{route('production.public_module.register_production.index',$machine_allocation)}}"
                                   class="btn btn-outline-dark">بازگشت</a>
                                <button type="submit"
                                        class="btn_action btn btn-primary"
                                        onclick="return confirm('آیا از کپی بسته بندی اطمینان دارید؟')"
                                >
                                    ثبت درخواست کپی
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>


    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>

@endsection
@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                "number_of_copy": {required: true, "min": 1, "max": {{$max_of_copy_packing_form}} },

            }
        });
    </script>
@endsection
