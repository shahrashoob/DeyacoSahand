<!DOCTYPE html>
<html lang="en">
<head>
    @include("layouts._head")
    @yield("styles")
    <style>
        body {
            font-family: IRANSans !important;
        }
    </style>

</head>
<body>

<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>

@include("layouts.admin._navbar")

@include("layouts._header")

<section class="header-user-list">
    <div class="h-list-header">
        <div class="input-group">
            <input type="text" id="search-friends" class="form-control" placeholder="Search Friend . . .">
        </div>
    </div>
    <div class="h-list-body">
        <a href="#!" class="h-close-text"><i class="feather icon-chevrons-right"></i></a>
        <div class="main-friend-cont scroll-div">
            <div class="main-friend-list">
                <div class="media userlist-box" data-id="1" data-status="online" data-username="Josephin Doe">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-1.jpg"
                            alt="Generic placeholder image ">
                        <div class="live-status">3</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Josephin Doe<small class="d-block text-c-green">Typing . . </small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="2" data-status="online" data-username="Lary Doe">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-2.jpg"
                            alt="Generic placeholder image">
                        <div class="live-status">1</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Lary Doe<small class="d-block text-c-green">online</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="3" data-status="online" data-username="Alice">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-3.jpg"
                            alt="Generic placeholder image">
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Alice<small class="d-block text-c-green">online</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="4" data-status="offline" data-username="Alia">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-1.jpg"
                            alt="Generic placeholder image">
                        <div class="live-status">1</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Alia<small class="d-block text-muted">10 min ago</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="5" data-status="offline" data-username="Suzen">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-4.jpg"
                            alt="Generic placeholder image">
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Suzen<small class="d-block text-muted">15 min ago</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="1" data-status="online" data-username="Josephin Doe">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-1.jpg"
                            alt="Generic placeholder image ">
                        <div class="live-status">3</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Josephin Doe<small class="d-block text-c-green">Typing . . </small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="2" data-status="online" data-username="Lary Doe">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-2.jpg"
                            alt="Generic placeholder image">
                        <div class="live-status">1</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Lary Doe<small class="d-block text-c-green">online</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="3" data-status="online" data-username="Alice">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-3.jpg"
                            alt="Generic placeholder image">
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Alice<small class="d-block text-c-green">online</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="4" data-status="offline" data-username="Alia">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-1.jpg"
                            alt="Generic placeholder image">
                        <div class="live-status">1</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Alia<small class="d-block text-muted">10 min ago</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="5" data-status="offline" data-username="Suzen">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-4.jpg"
                            alt="Generic placeholder image">
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Suzen<small class="d-block text-muted">15 min ago</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="1" data-status="online" data-username="Josephin Doe">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-1.jpg"
                            alt="Generic placeholder image ">
                        <div class="live-status">3</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Josephin Doe<small class="d-block text-c-green">Typing . . </small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="2" data-status="online" data-username="Lary Doe">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-2.jpg"
                            alt="Generic placeholder image">
                        <div class="live-status">1</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Lary Doe<small class="d-block text-c-green">online</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="3" data-status="online" data-username="Alice">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-3.jpg"
                            alt="Generic placeholder image">
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Alice<small class="d-block text-c-green">online</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="4" data-status="offline" data-username="Alia">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-1.jpg"
                            alt="Generic placeholder image">
                        <div class="live-status">1</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Alia<small class="d-block text-muted">10 min ago</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="5" data-status="offline" data-username="Suzen">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-4.jpg"
                            alt="Generic placeholder image">
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Suzen<small class="d-block text-muted">15 min ago</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="1" data-status="online" data-username="Josephin Doe">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-1.jpg"
                            alt="Generic placeholder image ">
                        <div class="live-status">3</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Josephin Doe<small class="d-block text-c-green">Typing . . </small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="2" data-status="online" data-username="Lary Doe">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-2.jpg"
                            alt="Generic placeholder image">
                        <div class="live-status">1</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Lary Doe<small class="d-block text-c-green">online</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="3" data-status="online" data-username="Alice">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-3.jpg"
                            alt="Generic placeholder image">
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Alice<small class="d-block text-c-green">online</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="4" data-status="offline" data-username="Alia">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-1.jpg"
                            alt="Generic placeholder image">
                        <div class="live-status">1</div>
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Alia<small class="d-block text-muted">10 min ago</small></h6>
                    </div>
                </div>
                <div class="media userlist-box" data-id="5" data-status="offline" data-username="Suzen">
                    <a class="media-left" href="#!">
                        <hr class="media-object hr-radius" src="../assets/images/user/avatar-4.jpg"
                            alt="Generic placeholder image">
                    </a>
                    <div class="media-body">
                        <h6 class="chat-header">Suzen<small class="d-block text-muted">15 min ago</small></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="header-chat">
    <div class="h-list-header">
        <h6>Josephin Doe</h6>
        <a href="#!" class="h-back-user-list"><i class="feather icon-chevron-left"></i></a>
    </div>
    <div class="h-list-body">
        <div class="main-chat-cont scroll-div">
            <div class="main-friend-chat">
                <div class="media chat-messages">
                    <a class="media-left photo-table" href="#!">
                        <hr class="media-object hr-radius hr-radius m-t-5" src="../assets/images/user/avatar-2.jpg"
                            alt="Generic placeholder image">
                    </a>
                    <div class="media-body chat-menu-content">
                        <div class="">
                            <p class="chat-cont">hello Datta! Will you tell me something</p>
                            <p class="chat-cont">about yourself?</p>
                        </div>
                        <p class="chat-time">8:20 a.m.</p>
                    </div>
                </div>
                <div class="media chat-messages">
                    <div class="media-body chat-menu-reply">
                        <div class="">
                            <p class="chat-cont">Ohh! very nice</p>
                        </div>
                        <p class="chat-time">8:22 a.m.</p>
                    </div>
                </div>
                <div class="media chat-messages">
                    <a class="media-left photo-table" href="#!">
                        <hr class="media-object hr-radius hr-radius m-t-5" src="../assets/images/user/avatar-2.jpg"
                            alt="Generic placeholder image">
                    </a>
                    <div class="media-body chat-menu-content">
                        <div class="">
                            <p class="chat-cont">can you help me?</p>
                        </div>
                        <p class="chat-time">8:20 a.m.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-list-footer">
        <div class="input-group">
            <input type="file" class="chat-attach" style="display:none">
            <a href="#!" class="input-group-prepend btn btn-success btn-attach">
                <i class="feather icon-paperclip"></i>
            </a>
            <input type="text" name="h-chat-text" class="form-control h-send-chat" placeholder="Write hear . . ">
            <button type="submit" class="input-group-append btn-send btn btn-primary">
                <i class="feather icon-message-circle"></i>
            </button>
        </div>
    </div>
</section>
<section class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">

                @include("layouts._page-header")


                <div class="main-body">

                    <div class="page-wrapper">
                        <div class="">

                            @include("layouts._messages")


                        </div>
                        @yield("content")

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="md-overlay ">
        <div class="" style="text-align: center; font-size: 11px">طراحی و تولید شرکت صنعتی دیاکو مهن آریا
            - V {{$setting["version"]->string_value??""}}
            <br>
            Copyright © 2015 - @php echo date('Y');@endphp

        </div>
    </div>
</section>

@yield("modals")

@include("layouts._footer")

@include("layouts._notification")
@yield("scripts")
</body>

</html>

