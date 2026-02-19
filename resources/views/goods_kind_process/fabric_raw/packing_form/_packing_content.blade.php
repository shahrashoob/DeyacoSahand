<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>لیست بسته بندی فرعی
            </h5>
        </div>
        <div class="card-block">

            <div class="table-responsive">
                <table class="table table-styling" style="text-align: center!important;">
                    <thead>
                    <tr>

                        <th>#</th>
                        <th> شماره بسته بندی</th>
                        <th>نوع بسته بندی</th>
                        <th> شماره حامل</th>
                        <th>مقدار کل</th>
                        <th>درصد جمع شدگی</th>
                        <th>وضعیت</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=$packing_form_contents_list->firstItem();@endphp
                    @foreach($packing_form_contents_list as $item)
                        <tr>
                            <td>{{$row++}}</td>

                            <td>
                                <a id="dcpk{{$item->id}}"
                                   href="{{route("fabric_raw.packing_form.view",$item)}}">
                                    {{$item->getCode()}}
                                </a>


                            </td>
                            <td>
                                {{$item->packing_type->caption??"---"}} - {{$item->reality_type->caption??"---"}}
                            </td>
                            <td>
                                {{$item->carrier?$item->carrier->getCaption():""}}
                            </td>
                            <td>
                                {{$item->getAllAmount("final_amount",4)}}
                            </td>
                            <td>
                                {{$item->final_shrinkage_percent()." %"}}
                            </td>
                            <td>{{$item->getStatus()}}</td>


                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>
            <div class="float-left">
                نمايش رکوردهای
                <b>{{$packing_form_contents_list->firstItem()}}</b>
                تا
                <b>{{$packing_form_contents_list->lastItem()}}</b>
                از
                <b>{{$packing_form_contents_list->total()}}</b>
                رکورد موجود
            </div>
        </div>
        <div class="text-center">
            {{$packing_form_contents_list->links('pagination::bootstrap-4')}}
        </div>
    </div>
</div>
@if(count($removed_sub_packing) > 0)
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>لیست بسته بندی فرعی جدا شده از بسته بندی
                </h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center!important;">
                        <thead>
                        <tr>

                            <th>#</th>
                            <th> شماره بسته بندی</th>
                            <th>نوع بسته بندی</th>
                            <th> شماره حامل</th>
                            <th>مقدار کل</th>
                            <th>درصد جمع شدگی</th>
                            <th>وضعیت</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=$removed_sub_packing->firstItem();@endphp
                        @foreach($removed_sub_packing as $item)
                            <tr class="alert alert-danger">
                                <td>{{$row++}}</td>

                                <td>
                                    <a id="dcpk{{$item->packing_form->id}}" target="_blank"
                                       href="{{route("fabric_raw.packing_form.view",$item->packing_form)}}">
                                        {{$item->packing_form->getCode()}}
                                    </a>


                                </td>
                                <td>
                                    {{$item->packing_form->packing_type->caption??"---"}} - {{$item->packing_form->reality_type->caption??"---"}}
                                </td>
                                <td>
                                    {{$item->packing_form->carrier?$item->carrier->getCaption():""}}
                                </td>
                                <td>
                                    {{$item->packing_form->getAllAmount("final_amount")}}
                                </td>
                                <td>
                                    {{$item->packing_form->final_shrinkage_percent()." %"}}
                                </td>
                                <td>{{$item->packing_form->getStatus()}}</td>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
                <div class="float-left">
                    نمايش رکوردهای
                    <b>{{$removed_sub_packing->firstItem()}}</b>
                    تا
                    <b>{{$removed_sub_packing->lastItem()}}</b>
                    از
                    <b>{{$removed_sub_packing->total()}}</b>
                    رکورد موجود
                </div>
            </div>
            <div class="text-center">
                {{$removed_sub_packing->links('pagination::bootstrap-4')}}
            </div>
        </div>
    </div>
@endif
