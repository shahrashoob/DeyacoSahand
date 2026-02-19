<!-- pcoded-navbar  -->
@php $user=\Auth::user();  @endphp

<nav class="pcoded-navbar navbar-blue">
    <div class="navbar-wrapper">
        <div class="navbar-brand header-logo">
            <a href="#" class="b-brand">
                <div class="b-bg">

                </div>
                <span class="b-title"></span>
            </a>
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
        </div>
        <div class="navbar-content scroll-div">
            <ul class="nav pcoded-inner-navbar">


                @include("layouts._nav_icon",["icon"=>"fa fa-home","caption"=>"صفحه اصلی","link"=>""])

                @include("layouts._nav_hasmenu",["icon"=>"fa fa-crosshairs ","caption"=>"   تولید ",
                "submenu"=>[
                        ["link"=>route("production.list"),"caption"=>"کارتابل جاری  تولید "],

                        ]
                    ])

                @include("layouts._nav_hasmenu",["icon"=>"fa fa-shopping-cart  ","caption"=>"بازرگانی ",
                "submenu"=>[
                        ["link"=>route("purchase.purchase.list"),"caption"=>"کارتابل جاری  بازرگانی "],

                        ]
                    ])


            @include("layouts._nav_hasmenu",["icon"=>"fa fa-crosshairs ","caption"=>"فروش ",
            "submenu"=>[
                    ["link"=>route("sales.dashboard.list"),"caption"=>"کارتابل جاری  فروش "],

                    ]
                ])

                @include("layouts._nav_hasmenu",["icon"=>"fa fa-warehouse ","caption"=>"   انبار ",
                "submenu"=>[
                        ["link"=>route("wh.cd.list"),"caption"=>"کارتابل جاری  انبار مواد اولیه "],
                        ["link"=>route("wh.material.list"),"caption"=>"کارتابل جاری انبار محصول    "],
                        ["link"=>route("wh.send_to_nosa"),"caption"=>"انتقال به نوسا- مواد اولیه  "],
                        ["link"=>route("wh.send_to_nosa_products"),"caption"=>"انتقال به نوسا - محصولات"],
                        ["link"=>route("wh.print.palet_sheet"),"caption"=>"برگ پالت "],


                        ]
                    ])


                @include("layouts._nav_hasmenu",["icon"=>"fa fa-users ","color"=>"text-danger","caption"=>"   منابع انسانی ",
                "submenu"=>[
                        ["link"=>"","caption"=>"کارتابل جاری منابع انسانی "],                        ]
                    ])

                @include("layouts._nav_hasmenu",["icon"=>"fa fa-flask ","color"=>"text-danger","caption"=>"   کنترل کیفیت ",
                "submenu"=>[
                        ["link"=>"","caption"=>"کارتابل جاری کنترل کیفیت "],                        ]
                    ])

                @include("layouts._nav_hasmenu",["icon"=>"fa fa-car ","color"=>"text-danger","caption"=>"   پایانه بار  ",
                "submenu"=>[
                        ["link"=>"","caption"=>"کارتابل جاری پایانه بار  "],                        ]
                    ])

                @include("layouts._nav_hasmenu",["icon"=>"fa fa-crosshairs ","color"=>"text-danger","caption"=>"   تامین کنندگان  ",
                "submenu"=>[
                        ["link"=>"","caption"=>"کارتابل جاری  "],                        ]
                    ])


                @include("layouts._nav_hasmenu",["icon"=>"fa fa-info ","caption"=>"اطلاعات پایه  ",
                    "submenu"=>[
                            ["link"=>route("import.product.index"),"caption"=>"آپلود لیست محصولات   "],
                            ["link"=>route("import.line.index"),"caption"=>"آپلود لیست خط های تولید   "],
                            ["link"=>route("import.line_product.index"),"caption"=>"آپلود لیست خط - محصول   "],
                            ["link"=>route("import.bom.index"),"caption"=>"آپلود BOM "],
                            ["link"=>route("import.customer.index"),"caption"=>"آپلود لیست مشتریان "],
                            ["link"=>route("import.worker.index"),"caption"=>"آپلود لیست کارکنان "],


                    ]
                ])



@include("layouts._nav_icon",["icon"=>"fa fa-bolt ","caption"=>"فراخوانی ","link"=>route("call.index",1)])

                @include("layouts._nav_icon",["icon"=>"feather icon-log-out","caption"=>"خروج","link"=>"logout"])


            </ul>

        </div>
    </div>
</nav>
<!-- /pcoded-navbar -->
