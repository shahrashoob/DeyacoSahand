<div class="row">
        <div class="col-sm-12">
            <h5> تنظیمات پیش فرض کالای برای رسته {{$goods_kind->caption}}</h5>

            <hr>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{$tab=="edit"?"active":""}}  text-uppercase" id="home-tab" data-toggle="tab"
                       href="#home"
                       role="tab" aria-controls="home" aria-selected="false">اطلاعات پایه</a>
                </li>



                    <li class="nav-item">
                        <a class="nav-link {{$tab=="consumed"?"active":""}} text-uppercase" id="consumed-tab"
                           data-toggle="tab" href="#consumed"
                           role="tab"
                           aria-controls="route" aria-selected="false">کالاهای مصرفی</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{$tab=="product_route"?"active":""}} text-uppercase" id="route-tab"
                           data-toggle="tab" href="#route"
                           role="tab"
                           aria-controls="route" aria-selected="false">مسیر محصول</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{$tab=="product_bom"?"active":""}}  text-uppercase" id="channel-tab"
                           data-toggle="tab" href="#channel"
                           role="tab"
                           aria-controls="channel" aria-selected="false">BOM</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{$tab=="algorithm"?"active":""}}  text-uppercase" id="algorithm-tab"
                           data-toggle="tab" href="#algorithm"
                           role="tab"
                           aria-controls="algorithm" aria-selected="false">الگوریتم ها</a>
                    </li>

            </ul>
            <div class="tab-content " id="myTabContent">
                <div class="tab-pane fade {{$tab=="edit"?"show active ":""}}  " id="home" role="tabpanel"
                     aria-labelledby="home-tab">

                        @include("line_product_station.goods_kind.setting._init_info")

                </div>

                <div class="tab-pane fade  {{$tab=="consumed"?"show active ":""}}" id="consumed" role="tabpanel"
                     aria-labelledby="consumed-tab">
                        @include("line_product_station.goods_kind.setting._consumed")

                </div>
                <div class="tab-pane fade  {{$tab=="product_route"?"show active ":""}}" id="route" role="tabpanel"
                     aria-labelledby="route-tab">
                        @include("line_product_station.goods_kind.setting._product_route")
                </div>


                <div class="tab-pane fade {{$tab=="product_bom"?"show active ":""}}" id="channel" role="tabpanel"
                     aria-labelledby="channel-tab">

                        @include("line_product_station.goods_kind.setting._bom")

                </div>

                <div class="tab-pane fade {{$tab=="algorithm"?"show active ":""}}" id="algorithm" role="tabpanel"
                     aria-labelledby="algorithm-tab">

                        @include("line_product_station.goods_kind.setting._algorithm")

                </div>


            </div>
        </div>
</div>
