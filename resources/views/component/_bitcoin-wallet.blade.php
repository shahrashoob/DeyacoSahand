<div class="col-md-6 col-xl-4">
    <div class="card {{isset($theme)?$theme:'theme-bg'}} bitcoin-wallet">
        <div class="card-block">
            <h5 class="text-white mb-2">{{$title}}</h5>
            <h2 class="text-white mb-2 f-w-300">{{$value}}</h2>
            <span class="text-white d-block">{{$description}}</span>
            <i class="fab {{$icon}} f-70 text-white"></i>
        </div>
    </div>
</div>
