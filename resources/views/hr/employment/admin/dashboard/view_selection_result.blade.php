@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        مشاهده جزییات امتیاز کسب شده
                        در
                        {{$employment_selection->selection->caption}}
                    </h5>

                </div>
                <div class="card-block">
                    <p>
                        نتیجه ارزیابی
                        <b>{{$employment->worker->fullname("with_gender_2")}}</b>
                        در
                        <b>{{$employment_selection->selection->caption}}</b>
                        با
                        <b>{{$employment_selection->worker->fullname("with_gender_2")}}</b>
                        ،
                        <b>{{$employment_selection->score_obtained_to_confirm_selection}}</b>
                        می باشد.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th> عنوان شاخص</th>
                                <th>نوع شاخص</th>
                                <th>وزن شاخص</th>
                                <th>ثبت امتیاز(0-100)</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp

                            @foreach($employment_selection->employment_selection_indicator_values as $item)

                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a>{{$item->selection_indicator->caption?? ""}}</a>
                                    </td>
                                    <td>{{$item->selection_indicator->field_type->caption?? ""}}</td>
                                    <td>{{$item->weight}}</td>
                                    <td>{{$item->value}}</td>

                                </tr>

                            @endforeach
                            <tr>
                                @if($employment_log)
                                    <td colspan="6">
                                        @include("component.input._lable", ["id"=>"message", 'label'=>"توضیحات ","value"=> $employment_log->message->text??"","class_col"=>""])
                                    </td>
                                @endif
                            </tr>
                            </tbody>

                        </table>
                    </div>
                    <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}" class="btn btn-outline-dark">بازگشت</a>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection


