<div class="card-block">

    <div class="row">


        <div class="col-md-12">
            <div class="row">
                @include("component.input._lable",["id"=>"military_information_id","label"=>"وضعیت سربازی","value"=> $employment->worker->military_information->caption??""])
            </div>
        </div>


    </div>
</div>
