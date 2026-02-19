{{--<div class="col-md-12">--}}
{{--    <b>  مدارک اولیه مورد نیاز جهت همکاری:</b>--}}
{{--    <br/>--}}
{{--    @foreach($document_type_list as $document_type)--}}

{{--        <input type="checkbox" name="befor_post_document_types[{{$document_type->id}}]"--}}
{{--                {{in_array($document_type->id,$befor_post_document_type_ids)?"checked='checked'":""}}--}}
{{--        > {{$document_type->caption}} &nbsp;&nbsp;--}}

{{--    @endforeach--}}
{{--    <br/>--}}
{{--    <br/>--}}
{{--</div>--}}
{{--<div class="col-md-12">--}}
{{--    <b>مدارک نهایی مورد نیاز جهت همکاری:</b>--}}
{{--    <br/>--}}
{{--    @foreach($document_type_list as $document_type)--}}


{{--        <input type="checkbox" name="after_post_document_types[{{$document_type->id}}]"--}}
{{--                {{in_array($document_type->id,$after_post_document_type_ids)?"checked='checked'":""}}--}}
{{--        > {{$document_type->caption}} &nbsp;&nbsp;--}}

{{--    @endforeach--}}
{{--    <br/>--}}
{{--    <br/>--}}
{{--</div>--}}
<div class="card-block">
    <div class="table-responsive">
        <table class="table table-styling">
            <thead>
            <tr>
                <th>ردیف</th>
                <th> اطلاعات مورد نیاز</th>
                <th>تایید اطلاعات در مرحله اول</th>
                <th>تایید اطلاعات در مرحله دوم</th>
                <th>عدم تایید</th>
                <th>تحویل به بایگانی</th>

            </tr>

            </thead>
            <tbody>
            @php $row=0;@endphp
            @foreach($document_receive_steps as $item)
                <tr>
                    <td>{{++$row}}</td>
                    <td>{{$item->caption}}</td>
                    <td>
                        <input type="radio" name="confirm_type_{{$item->id}}" value="1"
                               @if($confirm_type &&isset($confirm_type[$item->id])&& $confirm_type[$item->id] == 1) checked
                               @endif required>

                    </td>
                    <td>
                        <input type="radio" name="confirm_type_{{$item->id}}" value="2"
                               @if( $confirm_type && isset($confirm_type[$item->id])&& $confirm_type[$item->id] == 2) checked
                               @endif required>
                    </td>
                    <td>
                        <input type="radio" name="confirm_type_{{$item->id}}" value="3"
                               @if( $confirm_type &&  isset($confirm_type[$item->id])&& $confirm_type[$item->id] == 3) checked
                               @endif required>

                    </td>
                    <td>
                        <input type="checkbox" name="is_necessary_to_deliver_document_to_archive[{{$item->id}}]"
                                {{($is_necessary_to_deliver_document_to_archive && isset($is_necessary_to_deliver_document_to_archive[$item->id])&& $is_necessary_to_deliver_document_to_archive[$item->id]==1)?"checked='checked'":""}}
                        >
                    </td>

                </tr>
            @endforeach
            </tbody>

        </table>
    </div>
</div>