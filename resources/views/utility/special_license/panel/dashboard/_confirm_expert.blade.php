<div class="col-sm-12">

    <div class="card">
        <div class="card-header">
            <h5> تایید کننده ها </h5>
        </div>
        <div class="card-block">

            <div class="table-responsive">
                <table class="table table-styling center">
                    <thead>
                    <tr>
                        <th>اولویت</th>
                        <th>پست های سازمانی</th>
                        <th>کمیته ها</th>
                    </tr>

                    </thead>
                    <tbody>
                    @for($k=1;$k<=5;$k++)
                        @php
                            $post_captions=$special_license->getConfirmPostExpertCaption($k);
                            $committee_caption=$special_license->getConfirmCommitteeExpertCaptions($k);
                        @endphp

                        @if($post_captions!="" || $committee_caption!="" )
                            <tr @if($special_license->current_priority_number==$k) class="alert-primary" @endif>
                                <td>اولویت {{$k}}
                                    @if($special_license->current_priority_number==$k)(در انتظار تایید) @endif
                                </td>
                                <td>
                                     {{$post_captions}}
                                </td>
                                <td>
                                     {!! $committee_caption !!},

                                </td>
                            </tr>

                        @endif
                    @endfor
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>
