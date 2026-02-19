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
                        <th>#</th>
                        <th>پست های سازمانی</th>
                        <th>کمیته ها</th>
                    </tr>

                    </thead>
                    <tbody>
                    @for($k=1;$k<=5;$k++)
                        @php
                            $post_captions=$special_license_type->getPostCaptions($k);
                            $committee_caption=$special_license_type->getCommitteeCaptions($k);
                            $floating_post_type_caption=$special_license_type->getFloatingPostTypeCaptions($k);
                        @endphp

                        @if($post_captions!="" || $committee_caption!="" || $floating_post_type_caption!="")
                            <tr>
                                <td>اولویت {{$k}}</td>
                                <td>
                                    {{$post_captions}} , {{$floating_post_type_caption}}
                                </td>
                                <td>
                                   {{$committee_caption}}

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
