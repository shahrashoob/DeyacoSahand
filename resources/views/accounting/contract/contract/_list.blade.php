@if($contract_clause_types->count()>0)
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست ماده های قراداد برای
                        {{$contract->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th> ردیف</th>
                                <th>اولویت</th>
                                <th>عنوان ماده</th>
                                <th> عنوان بند</th>
                                <th></th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($contract_clause_types  as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->priority_number}}</td>
                                    <td>{{$item->clause_type->caption}}</td>
                                    <td>
                                         {{$item->clause_article->caption ??""}}
                                    </td>
                                    <td>
                                        <a href="{{route("accounting.contract.contract.destroy_contract_clause_type",[$item->contract_id,$item])}}"
                                           onclick="return confirm('آیا از حذف ماده قراداد اطمینان دارید؟')"><i
                                                    class="fa fa-trash text-danger"></i> </a>
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endif