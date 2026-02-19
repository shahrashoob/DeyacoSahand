<div class="table-responsive" style="max-width: 600px; margin: auto">
    <table class="table table-styling center" style="border: 3px solid #efefef">
        <thead>
        <tr>
            <th colspan="2">شماره بسته بندی حمل و نقل {{$transport_item->code()}}</th>
        </tr>
        <tr>
            <th style="width: 50px">#</th>
            <th style="width: 150px">کد بسته بندی</th>
            <th style="width: 120px"> {{$transport_item->transport_packing_list()->first()? $transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("unit","measurement"):""}}</th>
            <th style="width: 120px"> {{$transport_item->transport_packing_list()->first()? $transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("sub_unit","measurement"):""}}</th>
            <th style="width: 30px;"></th>
        </tr>

        </thead>
        <tbody>

        @php $row=0;@endphp
        @foreach($transport_item->transport_packing_list as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>
                    {{$item->packing_form->getCode()}}

                </td>
                <td>
                    {{$item->packing_form->getAllAmount("final_amount")}}
                </td>
                <td>
                    {{$item->packing_form->getAllAmount("sub_amount")}}
                </td>
                <td>
                    @if($transport_item->status_id!= 6010002)
                        <a href="{{route("wh.transport.dashboard.delete_packing_form",[$transport_item,$item])}}"
                           onclick="return confirm('آیا از حذف اطمینان دارید؟')">
                            <i class="text-danger fa fa-trash"></i>
                        </a>
                    @endif
                </td>

            </tr>
        @endforeach
        @if($transport_item->status_id!= 6010002)
            <tr>
                <td colspan="5">
                    <form id="form1"
                          method="post" autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        @if(isset($message) && $message!="")
                            <div class="alert alert-danger" style="white-space: normal;
                                                                  word-wrap: break-word;
                                                                  word-break: break-word;">
                                {!! $message !!}
                                <script>
                                    var snd = new Audio("{{asset("assets/voice/alarm.mp3")}}");
                                    snd.play();
                                </script>
                            </div>
                        @endif
                        @if(isset($success_message) && $success_message!="")
                            <div class="alert alert-warning" style="white-space: normal;
                                                                  word-wrap: break-word;
                                                                  word-break: break-word;">
                                {!! $success_message !!}
                                <script>
                                    var snd = new Audio("{{asset("assets/voice/warning.mp3")}}");
                                    snd.play();
                                </script>
                            </div>
                        @endif
                        <div class="center col-md-12">
                            @if($transport_item->product_request_form->warehouse->allow_entry_with_pin )
                                کد پین (شماره پالت)
                                <input type="text" value="" id="packing_form_code" autofocus style="width: 80px">
                            @else
                                شماره فرم بسته بندی (شماره پالت)
                                <input type="text" value="" id="packing_form_code" autofocus style="width: 80px">
                                /DCPK
                            @endif
                            <input type="hidden" value="{{$transport_item->product_request_form_id}}"
                                   id="product_request_form_id">
                            <input type="hidden" value="{{$transport_item->id}}" id="transport_item_id">
                            <input type="hidden" value="{{$transport_item->product_request_form->user_id}}"
                                   id="user_id">
                            <input type="hidden" value="{{$page}}" id="page">
                            <br/>
                            <br/>
                            <button type="submit" class="btn btn-primary" style="width: 120px">بررسی و ثبت
                            </button>

                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                    aria-haspopup="true" style="width: 140px"
                                    aria-expanded="false">ثبت موقت
                            </button>
                            <div class="dropdown-menu" style="text-align: center">
                                <a class="dropdown-item"
                                   href="{{route("wh.transport.dashboard.transport_confirm",[$transport_item,"confirm_print_new"])}}"

                                >ثبت موقت، چاپ و ایجاد بسته جدید</a>

                                <a class="dropdown-item"
                                   href="{{route("wh.transport.dashboard.transport_confirm",[$transport_item,"confirm_new",$page,$dashboard_type])}}"

                                >ثبت موقت و ایجاد بسته جدید</a>

                                <a class="dropdown-item"
                                   href="{{route("wh.transport.dashboard.transport_confirm",[$transport_item,"confirm_print_back",$page,$dashboard_type])}}"

                                >ثبت موقت، چاپ و بازگشت</a>

                                <a class="dropdown-item"
                                   href="{{route("wh.transport.dashboard.transport_confirm",[$transport_item,"confirm_back",$page,$dashboard_type])}}"

                                >ثبت موقت و بازگشت</a>


                            </div>
                            <a href="{{route("wh.transport.dashboard.transport_final_confirm",[$transport_item,"confirm_back",$page,$dashboard_type])}}"
                               class="btn btn-primary" style="width: 120px"
                               onclick="return confirm('آیا از ثبت نهایی اطمینان دارید');"
                            >
                                ثبت نهایی
                            </a>

                            <a href="{{route("wh.transport.dashboard.index",[$transport_item->product_request_form_id,$page,$dashboard_type])}}"
                               class="btn btn-outline-dark">بازگشت</a>
                        </div>


                    </form>
                </td>
            </tr>

        @else
            <tr>
                <td colspan="5">
                    <br/>

                    <a href="{{route("wh.transport.dashboard.index",[$transport_item->product_request_form_id,$page,$dashboard_type])}}"
                       class="btn btn-outline-dark">بازگشت</a>
                </td>
            </tr>
        @endif
        </tbody>

    </table>
</div>
<script>

    $("#form1").submit(function () {

        $packing_form_code = $("#packing_form_code").val();
        $("#packing_form_code").prop("disabled", true);
        request = $.ajax({
            url: "{{url("api/other/add_packing_form_to_transport_item_product_request_form")}}",
            type: "post",
            data: {
                "product_request_form_id": $("#product_request_form_id").val(),
                "transport_item_id": $("#transport_item_id").val(),
                "user_id": $("#user_id").val(),
                "page": $("#page").val(),
                "dashboard_type": "{{$dashboard_type}}",
                "packing_form_code": $packing_form_code,

            }
        });
        request.done(function (response, textStatus, jqXHR) {

            $("#card_packing").html(response);
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
        return false;
    })
    $("#packing_form_code").focus();
</script>
