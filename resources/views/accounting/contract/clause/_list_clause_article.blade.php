@if($cluase_articles->count()>0)
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست بند های ماده</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th> ردیف</th>
                                <th>محتوا</th>
                                <th>توکن 1</th>
                                <th>توکن 2</th>
                                <th>توکن 3</th>
                                <th>توکن 4</th>
                                <th>توکن 5</th>
                                <th>توکن 6</th>
                                <th>توکن 7</th>
                                <th>توکن 8</th>
                                <th>توکن 9</th>
                                <th>توکن 10</th>


                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($cluase_articles  as $item)
                                <tr>
                                    <td>{{++$row}}</td>

                                    <td>{{$item->caption}}</td>
                                    <td>{{$item->token1->caption ??""}}</td>
                                    <td>{{$item->token2->caption ??""}}</td>
                                    <td>{{$item->token3->caption ??""}}</td>
                                    <td>{{$item->token4->caption ??""}}</td>
                                    <td>{{$item->token5->caption ??""}}</td>
                                    <td>{{$item->token6->caption ??""}}</td>
                                    <td>{{$item->token7->caption ??""}}</td>
                                    <td>{{$item->token8->caption ??""}}</td>
                                    <td>{{$item->token9->caption ??""}}</td>
                                    <td>{{$item->token10->caption ??""}}</td>
                                    <td>
                                        <a href="{{route("accounting.contract.clause.destroy_clause_article",$item->id)}}"
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