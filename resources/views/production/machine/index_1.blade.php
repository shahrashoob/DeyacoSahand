@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("line_product_station.machine.public._search_view",["route"=>"production.machine.index"])
        </div>
        @php $row=0;@endphp
        @foreach($list as $item)
            <div class="col-sm-6  col-lg-4">

                <div class="card">
                    <div class="card-header">
                        <h5>
                            <a href="{{route("production.machine.view",$item)}}">{{$item->code}}
                                - {{$item->caption}}</a>
                            <a href="{{route("production.machine.short_link",$item)}}"><span
                                        class="fas fa-chess-board"></span> </a>
                        </h5>
                        <div style="float: left; left: 10px">
                            {{$item->production_status?$item->production_status->getCaption():"نامشخص"}}
                        </div>
                    </div>
                    <div class="card-block">

                        @php $allocation_checkbox=[]; @endphp
                        <form id="form{{$item->id}}"
                              action="{{route("production.dashboard.change_allocation",[16,$item])}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf

                            <input type="hidden" id="change_type_{{$item->id}}" name="change_type_{{$item->id}}"
                                   value="0">

                            <input type="hidden" id="one_allocation_{{$item->id}}" name="one_allocation_{{$item->id}}"
                                   value="0">

                            <input type="hidden" id="other_machine_{{$item->id}}" name="other_machine_{{$item->id}}"
                                   value="0">
                            <div class="table-responsive" style="overflow: hidden">
                                <table class="table table-hover">
                                    <tbody>

                                    @foreach( $machine_allocations as $reserve_allocation)
                                        @if($reserve_allocation->machine_id != $item->id)
                                            @continue
                                        @endif
                                        <tr class="unread">

                                            <td>

                                                @if(isset($production_channel_color[$item->id]))

                                                    @include("component.input._color_label",["color"=>$production_channel_color[$item->id]])
                                                @endif
                                                @if($property1_show_in_production_dashboard || $property2_show_in_production_dashboard)
                                                    @include("production.dashboard._property",["item"=>$reserve_allocation])
                                                @endif
                                            </td>
                                            <td>


                                                <h6 class="mb-1">
                                                    <input type="checkbox"
                                                           name="allocation_priority[{{$reserve_allocation->allocation->priority_number}}]">
                                                    @if(!isset($allocation_checkbox[$reserve_allocation->allocation->priority_number]))



{{--                                                        {{$reserve_allocation->id}}--}}
                                                        @php $allocation_checkbox[$reserve_allocation->allocation->priority_number]=1; @endphp
                                                    @endif

                                                    @if($reserve_allocation->status_id == 5310040)
                                                        <a
                                                                onclick="return confirm('آیا از کنسل کردن تخصیص اطمینان دارید؟')"
                                                                href="{{route("production.dashboard.allocation_cancel",[$reserve_allocation->allocation_id,$reserve_allocation->production_id])}}">
                                                            <i
                                                                    class="fa fa-trash text-c-red  m-r-15"></i>
                                                        </a>
                                                    @else
                                                        <i
                                                                class="fas fa-angle-double-left   m-r-15"></i>
                                                    @endif
                                                    @if($allow_show_production)
                                                        <a href="{{route("production.dashboard.view_card",[$reserve_allocation->production,"machine_index"])}}">
                                                            {{$reserve_allocation->production->serial()}}
                                                        </a>
                                                    @else
                                                        {{$reserve_allocation->production->serial()}}
                                                    @endif
                                                </h6>
                                                <p class="m-0">{{$reserve_allocation->product->caption}}  </p>
                                                {{$reserve_allocation->allocation_amount}}
                                                {{$reserve_allocation->product->unit->caption}}

                                                (@foreach($reserve_allocation->production->packing_types as $item_packing)
                                                    {{$item_packing->packing_type->caption}}
                                                @endforeach)
                                                <br/>
                                                @if($reserve_allocation->allocation->status_id == 5310010)
                                                    جاری: تخصیص {{$reserve_allocation->allocation->id}}

                                                    &nbsp;
                                                    &nbsp;
                                                    <a onclick="return set_change_type({{$item->id}},{{$reserve_allocation->id}}, 211   )">
                                                        <i class="fas fa-angle-double-down   m-r-15"></i>
                                                    </a>

                                                    <a onclick="return set_change_type({{$item->id}},{{$reserve_allocation->id}}, 201   )">
                                                        <i class="fas fa-angle-down   m-r-15"></i>
                                                    </a>

                                                @else
                                                    رزور
                                                    {{$reserve_allocation->allocation->priority_number}}
                                                    : تخصیص
                                                    {{$reserve_allocation->allocation_id}}
                                                    &nbsp;
                                                    &nbsp;
                                                    <a onclick="return set_change_type({{$item->id}},{{$reserve_allocation->id}}, 211   )">
                                                        <i class="fas fa-angle-double-down   m-r-15"></i>
                                                    </a>

                                                    <a onclick="return set_change_type({{$item->id}},{{$reserve_allocation->id}}, 201   )">
                                                        <i class="fas fa-angle-down   m-r-15"></i>
                                                    </a>

                                                    <a onclick="return set_change_type({{$item->id}},{{$reserve_allocation->id}}, 101   )">
                                                        <i class="fas fa-angle-up   m-r-15"></i>
                                                    </a>

                                                @endif
                                                <i class="fas fa-angle-left"></i>
                                                <select id="select_{{$item->id}}" style="font-size: 10px"
                                                        data-machine_id="{{$item->id}}"
                                                        data-allocation_id="{{$reserve_allocation->id}}"
                                                        class="select_machine"

                                                >
                                                    <option></option>
                                                    @foreach($machine_list[$item->machine_type_id] as $item_machine)
                                                        <option value="{{$item_machine["id"]}}">{{$item_machine["caption"]}}</option>
                                                    @endforeach

                                                </select>


                                                <button type="submit"
                                                        onclick="return set_change_type({{$item->id}},{{$reserve_allocation->id}}, 301)"
                                                        class="btn btn-primary btn-sm btn_submit">جابجایی ماشین
                                                </button>


                                            </td>


                                        </tr>

                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </form>
                    </div>
                    <div class="text-center">
                        {{$list->links('pagination::bootstrap-4')}}
                    </div>
                </div>

            </div>

        @endforeach
    </div>
    @if($allow_show_production)
        <div class="row">
            <div class="col-md-12">

                @include("production.dashboard._list",["list"=>$list_production,"allow_allocation_view"=>1,"back_url_type"=>"machine_index"])
            </div>
        </div>
    @endif
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        .btn_submit {
            padding: 1px;
            margin: 0px;
            font-size: 12px;
        }
    </style>
@endsection


@section("scripts")
    <script>
        function set_change_type(machine_id,allocation_id, type) {

            $("#one_allocation_" + machine_id).val(allocation_id);
            $("#change_type_" + machine_id).val(type);
            $("#form" + machine_id).submit();
        }

        $(".select_machine").change(function () {

            var machine_id = $(this).data("machine_id");
            var allocation_id = $(this).data("allocation_id");

//           //  $("#other_machine_" + machine_id).val(100);
            $("#other_machine_" + machine_id).val($(this).val());
            $("#one_allocation_" + machine_id).val(allocation_id);
        })

    </script>

@endsection
