<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>
                    ثبت امتیاز کسب شده برای
                    {{$employment_selection->selection->caption}}
                </h5>

            </div>
            <div class="card-block">

                <p>
                    لطفا پس از انجام
                    <b>{{$employment_selection->selection->caption}}</b>
                    با
                    <b>{{$employment->worker->fullname("with_gender_2")}}</b>
                    نتیجه را در فرم زیر وارد نموده و تایید نمایید.
                </p>
                @include('component.input.datepicker._script')

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>ردیف</th>
                            <th> عنوان شاخص</th>
                            <th>نوع شاخص</th>
                            <th>وزن شاخص</th>
                            <th>حداقل امتیاز</th>
                            <th>ثبت امتیاز(0-100)</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @foreach($employment_selection->selection->selection_indicators as $item)
                            @php $row=0;@endphp
                            <tr>
                                <td>{{++$row}}</td>
                                <td>
                                    <a>{{$item->caption?? ""}}</a>
                                </td>
                                <td>{{$item->field_type->caption}}</td>
                                <td>{{$item->weight}}</td>
                                <td>{{$item->min_score}}</td>
                                <td>
                                    <input style="width: 60px" type="number" required="required" min="0"
                                           max="100" name="value_{{ $item->id }}" value="{{$item->value ??""}}">
                                </td>

                            </tr>

                        @endforeach
                        <tr>

                            <td colspan="6">
                                @include("component.input._textarea", ["id"=>"message", 'label'=>"توضیحات ","class_col"=>""])
                            </td>

                        </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>

</div>