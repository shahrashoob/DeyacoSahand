<div class="table-responsive" style="width: 600px; margin: auto">
    <table class="table table-styling center" style="border: 3px solid #efefef">
        <thead>
        <tr>
            <th colspan="2">شماره بسته بندی بارگیری {{$transport_item->code()}}</th>
        </tr>
        <tr>
            <th style="width: 50px">#</th>
            <th style="width: 150px"> کد بسته بندی</th>
            <th style="width: 120px"> {{$transport_item->transport_packing_list()->first()? $transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("unit","measurement"):""}}</th>
            <th style="width: 120px"> {{$transport_item->transport_packing_list()->first()? $transport_item->transport_packing_list()->first()->packing_form->getUnitCaption("sub_unit","measurement"):""}}</th>
            <th style="width: 30px;">وضعیت بسته بندی</th>
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
                        <a href="{{route("utility.transport.dashboard.delete_packing_form",[$transport_item,$item])}}"
                           onclick="return confirm('آیا از حذف اطمینان دارید؟')">
                            <i class="text-danger fa fa-trash"></i>
                        </a>
                    @endif
                </td>
                <td>
                    {{$item->packing_form->status->caption}}
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
                            <div class="alert alert-danger">
                                {{$message}}
                                <script>
                                    var snd = new Audio("{{asset("assets/voice/alarm.mp3?id=545")}}");
                                    snd.play();
                                </script>
                            </div>
                        @endif
                        <div class="center">
                            شماره فرم بسته بندی :
                            <input type="number" value="" id="packing_form_code" autofocus style="width: 80px">
                            /DCPK
                            <input type="hidden" value="{{$transport_item->transport_id}}" id="transport_id" autofocus>
                            <input type="hidden" value="{{$transport_item->id}}" id="transport_item_id" autofocus>
                            <input type="hidden" value="{{$transport_item->transport->user_id}}" id="user_id" autofocus>
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
                                   href="{{route("utility.transport.dashboard.transport_confirm",[$transport_item,"confirm_print_new"])}}"

                                >ثبت موقت، چاپ و ایجاد بسته جدید</a>

                                <a class="dropdown-item"
                                   href="{{route("utility.transport.dashboard.transport_confirm",[$transport_item,"confirm_new"])}}"

                                >ثبت موقت و ایجاد بسته جدید</a>

                                <a class="dropdown-item"
                                   href="{{route("utility.transport.dashboard.transport_confirm",[$transport_item,"confirm_print_back"])}}"

                                >ثبت موقت، چاپ و بازگشت</a>

                                <a class="dropdown-item"
                                   href="{{route("utility.transport.dashboard.transport_confirm",[$transport_item,"confirm_back"])}}"

                                >ثبت موقت و بازگشت</a>


                            </div>
                            <a href="{{route("utility.transport.dashboard.transport_final_confirm",[$transport_item,"confirm_back"])}}" class="btn btn-primary" style="width: 120px"
                               onclick="return confirm('آیا از ثبت نهایی اطمینان دارید');"
                            >
                           ثبت نهایی
                            </a>

                            <a href="{{route("utility.transport.dashboard.transport_item",$transport_item->transport_id)}}"
                               class="btn btn-outline-dark">بازگشت</a>
                        </div>


                    </form>
                </td>
            </tr>

        @else
            <tr>
                <td colspan="5">
                    <br/>
                    <a href="{{route("utility.transport.dashboard.transport_item",$transport_item->transport_id)}}"
                       class="btn btn-outline-dark">بازگشت</a>
                </td>
            </tr>
            @endif
        </tbody>

    </table>
</div>
<script>

    $("#form1").submit(function () {

        request = $.ajax({
            url: "{{url("api/other/add_packing_form_to_transport_item")}}",
            type: "post",
            data: {
                "transport_id": $("#transport_id").val(),
                "transport_item_id": $("#transport_item_id").val(),
                "user_id": $("#user_id").val(),
                "packing_form_code": $("#packing_form_code").val(),

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
