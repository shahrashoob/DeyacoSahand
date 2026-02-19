<div class="row">

    <div class="col-sm-12">
        <br/>
        <a href="{{route("customer_group.tmp.product_creation.create")}}">
            <i class="fa fa-plus"></i>
            {{__("menu.add product design request")}} </a>
<br/>
<br/>
        <div class="card">
            <div class="card-header">
                <h5> {{__("message.product design requests")}}</h5>

            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{__("table.code")}}</th>
                            <th> {{__("table.suggested product name")}}</th>
                            <th>{{__("table.status")}}</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>
                                    <a href="{{route("customer_group.tmp.product_creation.view",$item)}}">
                                        {{$item->getCode()}}
                                    </a>
                                </td>
                                <td>{{$item->caption??""}}</td>
                                <td>
                                    <a href="{{route("customer_group.tmp.product_creation.log",$item)}}">
                                        {{__("option/status.".($item->status->id??""))}}
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
                <div class="float-left">
                   {{__("table.show records")}}
                    <b>{{$list->firstItem()}}</b>
                    {{__("table.up")}}
                    <b>{{$list->lastItem()}}</b>
                    {{__("table.from")}}
                    <b>{{$list->total()}}</b>
                    {{__("table.available records")}}
                </div>
            </div>
            <div class="text-center">
                {{$list->links('pagination::bootstrap-4')}}
            </div>
        </div>
    </div>

</div>

