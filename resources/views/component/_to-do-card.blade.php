<div class="col-xl-4 col-md-6">
    <div class="card to-do">
        <div class="card-header">
            <h5>{{$title}}</h5>
            <div class="card-header-right">
                <div class="btn-group card-option">
                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="feather icon-more-horizontal"></i>
                    </button>
                    <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                        <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> بزرگ نمایی</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                        <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> جمع شدن</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                        <li class="dropdown-item close-card"><a href="#!"><i class="feather icon-trash"></i> حذف</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-block">
            <div class="row">
                @foreach($list as $item)
                <div class="col-sm-12 m-b-30">
                    <div class="widget-todo">
                        <div class="media">
                            <div class="mr-3 photo-table">
                                <i class="fas fa-circle text-c-green f-10 mr-2"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="d-inline-block">{{$item["title"]}}</h6>
                                <p class="m-b-0 text-muted">{{$item["description"]}} </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="to-do-button">
                    <button class="btn btn-primary"><i class="fas fa-plus f-14 mr-0"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
