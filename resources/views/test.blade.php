@extends('layouts.admin._master')

@section('page_header_title'," ")

@section('content')
    <div class="row">

        <div class="col-sm-12" style="text-align: center">
            {{--       <img src="{{asset("assets/images/bg_2.png")}}" style="height:350px; margin-bottom: 30px" />--}}

            <br/>
            <br/>


        </div>
        <div class="col-md-12" >

            <div class="text" style="padding: 10px; font-size:16px;  ">

                <br/>
                بروز رسانی سامانه:
                <br/>
                <br/>
                با توجه به اینکه نسخه جدید از سامانه موجود می باشد، برای بروزرسانی سامانه به آخرین نسخه، درخواست بروز رسانی را ثبت نمایید.

                <form id="form1" action="{{route("submit_test")}}" method="post" novalidate="novalidate">
                    @csrf

                    <div class="row">
                        @include("component.input._lable",["id"=>"code","lable"=>" وضعیت پشتیبانی ","value"=>"فعال دارای قرارداد پشتیبانی"])
                        @include("component.input._lable",["id"=>"code","lable"=>" زمان بروز رسانی  ","value"=>"1403/05/29 ساعت 15 - 15:30"])
                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary">ثبت درخواست</button>

                    </div>
                </form>

                    </div>
        </div>
{{--        @include("component.pup_up.type1")--}}

    </div>

@endsection
@section("styles")
    <script>



    </script>
{{--    @include("component.input.datepicker._script")--}}
@endsection



{{--@extends('layouts.admin._master')--}}

{{--@section('page_header_title'," ")--}}

{{--@section('content')--}}
{{--    <pre id="myText" style="font-size: 16px" ></pre>--}}

{{--    <script>--}}
{{--        var data = { "کارت تولید جاری": null,"کارت های رزرو":{0:"1234465",1:"98745621"}, bar: "sample" };--}}

{{--        document.getElementById("myText").innerHTML = JSON.stringify(data, null, 4);--}}

{{--    </script>--}}
{{--    <div class="row">--}}

{{--        <div class="timeline">--}}
{{--            <div class="outer">--}}
{{--                <div class="card">--}}
{{--                    <div class="info">--}}
{{--                        <h3 class="title">Title 1</h3>--}}
{{--                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card">--}}
{{--                    <div class="info">--}}
{{--                        <h3 class="title">Title 2</h3>--}}
{{--                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card">--}}
{{--                    <div class="info">--}}
{{--                        <h3 class="title">Title 3</h3>--}}
{{--                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card">--}}
{{--                    <div class="info">--}}
{{--                        <h3 class="title">Title 4</h3>--}}
{{--                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="card">--}}
{{--                    <div class="info">--}}
{{--                        <h3 class="title">Title 5</h3>--}}
{{--                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}



{{--    </div>--}}

{{--    <div class="row">--}}

{{--        <div id="qr-reader" style="width:500px"></div>--}}
{{--        <div id="qr-reader-results"></div>--}}

{{--        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>--}}
{{--        <script>--}}

{{--            function docReady(fn) {--}}
{{--                // see if DOM is already available--}}
{{--                if (document.readyState === "complete"--}}
{{--                    || document.readyState === "interactive") {--}}
{{--                    // call on next available tick--}}
{{--                    setTimeout(fn, 1);--}}
{{--                } else {--}}
{{--                    document.addEventListener("DOMContentLoaded", fn);--}}
{{--                }--}}
{{--            }--}}

{{--            docReady(function () {--}}
{{--                var resultContainer = document.getElementById('qr-reader-results');--}}
{{--                var lastResult, countResults = 0;--}}
{{--                function onScanSuccess(decodedText, decodedResult) {--}}
{{--                    if (decodedText !== lastResult) {--}}
{{--                        ++countResults;--}}
{{--                        lastResult = decodedText;--}}
{{--                        // Handle on success condition with the decoded message.--}}
{{--                        alert(`Scan result ${decodedText}`, decodedResult);--}}
{{--                    }--}}
{{--                }--}}

{{--                var html5QrcodeScanner = new Html5QrcodeScanner(--}}
{{--                    "qr-reader", { fps: 10, qrbox: 250 });--}}
{{--                html5QrcodeScanner.render(onScanSuccess);--}}

{{--            });--}}
{{--        </script>--}}
{{--    </div>--}}
{{--@endsection--}}
{{--@section("styles")--}}

{{--    <style>--}}


{{--        @import "compass/css3";--}}

{{--        /* v2.0 */--}}
{{--        /* v2.0 */--}}
{{--        /* v2.0 */--}}
{{--        /* v2.0 */--}}
{{--        /* v2.0 */--}}
{{--        $text-color: rgb(77, 77, 77);--}}
{{--        $bg-entry: rgb(80,80,80);--}}

{{--        *,*:after,*:before{-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}--}}


{{--        .timeline {--}}
{{--            margin: 0px auto;--}}
{{--            max-width: 40em;--}}
{{--            overflow:hidden;--}}
{{--            height: auto;--}}
{{--            position: relative;--}}
{{--            padding:0px;--}}
{{--            list-style-type:none;--}}

{{--            /*thanks to--}}
{{--               https://twitter.com/JacoKoster--}}
{{--            */--}}
{{--            background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAEElEQVQIW2NMTEz8z8gABAAPKwIlXWq1kgAAAABJRU5ErkJggg==);--}}
{{--            background-repeat:repeat-y;--}}
{{--            background-position:50% 0;--}}



{{--        .year {--}}
{{--            background: $bg-entry;--}}
{{--            font-size: 3em;--}}
{{--            max-width: 4em;--}}
{{--            clear: both;--}}
{{--            margin: 1em auto 2em;--}}
{{--            color: white;--}}
{{--            border-radius: 30% / 100%;--}}
{{--            text-align: center;--}}
{{--            margin-top: 2em;--}}

{{--        &:first-of-type {--}}
{{--             margin-top: 0 !important;--}}
{{--         }--}}
{{--        }--}}

{{--        /*--}}
{{--          elements 1,(2),3,(4), etc.--}}
{{--          elements in brackets inherit these properties, some are overridden below (2n)--}}
{{--          beginning at 0--}}
{{--        */--}}
{{--        .event {--}}
{{--            position:relative;--}}
{{--            text-align:center;--}}
{{--            float: right;--}}
{{--            clear: right;--}}
{{--            width: 45%;--}}
{{--            margin: 1em 2.5%;--}}

{{--            border-radius: 5px;--}}
{{--            list-style-type: none;--}}
{{--            display: block;--}}
{{--            padding: .4em;--}}
{{--            background: white;--}}
{{--            z-index: 15;--}}

{{--            color: $text-color;--}}
{{--            border: 1px solid lighten($bg-entry,30%);--}}
{{--            text-decoration: none;--}}

{{--            -webkit-transition: background .15s linear;--}}
{{--            -moz-transition: background .15s linear;--}}
{{--            -ms-transition: background .15s linear;--}}
{{--            -o-transition: background .15s linear;--}}
{{--            transition: background .15s linear;--}}

{{--        &.featured {--}}
{{--             width: 95% !important;--}}
{{--             background: lighten(#ccc,11%);--}}

{{--        &:after, &:before {--}}
{{--                      display: none;--}}
{{--                  }--}}
{{--        }--}}

{{--        @media all and (max-width: 600px) {--}}
{{--            width: 85%;--}}
{{--        }--}}

{{--        &.offset-first {--}}
{{--             margin-top: -1.5em!important;--}}
{{--         }--}}

{{--        img {--}}
{{--            margin-top: 10px;--}}
{{--            max-width: 100%;--}}
{{--        }--}}

{{--        &:hover {--}}
{{--             background: lighten($bg-entry,60%);--}}

{{--        &:after {--}}
{{--             background: $bg-entry;--}}
{{--         }--}}
{{--        }--}}

{{--        &:nth-of-type(2n) {--}}
{{--             float: left;--}}
{{--             clear: left;--}}
{{--        @media all and(min-width:600px){--}}
{{--            margin-top:2em;--}}
{{--        }--}}
{{--        }--}}

{{--        &:after {--}}
{{--        @media all and (min-width: 650px) {--}}
{{--            display: block;--}}
{{--            content: ' ';--}}
{{--            height: 9px; width: 9px;--}}
{{--            background: lighten($bg-entry,30%);--}}
{{--            border-radius: 50%;--}}
{{--            position:absolute;--}}
{{--            left: -5%;--}}
{{--            top: 1.5em;--}}
{{--            border: 2px solid white;--}}
{{--        }--}}
{{--        }--}}

{{--        &:nth-child(2n):after {--}}
{{--             right: -5%;--}}
{{--             /* reset the standard declaration I defined before*/--}}
{{--             left: auto;--}}
{{--         }--}}
{{--        }--}}
{{--        }--}}


{{--    <!doctype html>--}}
{{--<html lang="fa" dir="rtl">--}}
{{--<head>--}}
{{--    <meta charset="utf-8">--}}
{{--    <title>Call AccXP Web Service methods from html/javascript</title>--}}
{{--    <script src="{{asset("AccXPSoapClt.js")}}" type="text/javascript"></script>--}}
{{--    <style>--}}
{{--        TABLE {width: 90%; table-layout: fixed}--}}
{{--        INPUT {width: 100%}--}}
{{--        INPUT[type="submit"] {width: auto}--}}
{{--        TEXTAREA {width: 100%}--}}
{{--        SELECT {width: 80%}--}}
{{--        TD {text-align: left}--}}
{{--        .rightAlign {text-align: right}--}}
{{--        .colTitles {width: 30mm}--}}
{{--        .colData {width: 60mm}--}}
{{--        .fullWidth {width: 100%}--}}
{{--        .halfWidth {width: 50%}--}}
{{--        .topBorderThick {border-Top: 1mm solid #000000}--}}
{{--        .btnAction {--}}
{{--            width: 150px;--}}
{{--            height:40px;--}}
{{--            background-color:black;--}}
{{--            color:white;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<script type="text/javascript">--}}
{{--    function Run()--}}
{{--    {--}}
{{--        var AParams = new Array();--}}

{{--        addParam(AParams, "ADBName", document.getElementById("edt_DBName").value);--}}

{{--        addParam(AParams, "AData", document.getElementById("edt_Data").value);--}}

{{--        sendSoapRequest(--}}
{{--            document.getElementById("edt_ServerURL").value,--}}
{{--            document.getElementById("edt_UserName").value,--}}
{{--            document.getElementById("edt_Password").value,--}}
{{--            document.getElementById("edt_Method").value,--}}
{{--            AParams, true,--}}
{{--            function (ARes) {--}}
{{--                document.getElementById("lbl_Result").value = ARes.replace(/</g, "\n<");--}}
{{--            });--}}
{{--    }--}}

{{--    window.onload = winLoaded;--}}

{{--</script>--}}
{{--<script type=”text/javascript” src=”https://www.google.com/jsapi”></script>--}}



{{--<body>--}}
{{--<script type=”text/javascript”>--}}
{{--    // Load the Google Transliterate API--}}
{{--    google.load(“elements”, “1”, {--}}
{{--    packages: “transliteration”--}}
{{--    });--}}

{{--    function onLoad() {--}}
{{--    var options = {--}}
{{--    sourceLanguage:--}}
{{--    google.elements.transliteration.LanguageCode.ENGLISH,--}}
{{--    destinationLanguage:--}}
{{--    [google.elements.transliteration.LanguageCode.PERSIAN],--}}
{{--    shortcutKey: ‘ctrl+g’,--}}
{{--    transliterationEnabled: true--}}
{{--    };--}}

{{--    // Create an instance on TransliterationControl with the required--}}
{{--    // options.--}}
{{--    var control =--}}
{{--    new google.elements.transliteration.TransliterationControl(options);--}}

{{--    // Enable transliteration in the textbox with id--}}
{{--    // ‘transliterateTextarea’.--}}
{{--    control.makeTransliteratable([‘transliterateTextarea’]);--}}
{{--    }--}}
{{--    google.setOnLoadCallback(onLoad);--}}
{{--</script>--}}
{{--<h2 class="rightAlign">اجرای متدهای AccXP Web Service از javascript</h2>--}}
{{--<table>--}}
{{--    <colgroup>--}}
{{--        <col class="colTitles"/>--}}
{{--        <col class="colData"/>--}}
{{--    </colgroup>--}}
{{--    <tr><td>آدرس (URL) سرور</td><td><input type="text" dir="ltr" id="edt_ServerURL"/></td></tr>--}}
{{--    <tr><td>نام کاربر</td><td><input type="text" dir="ltr" id="edt_UserName"/></td></tr>--}}
{{--    <tr><td>کلمه عبور</td><td><input type="password" dir="ltr" id="edt_Password"/></td></tr>--}}
{{--    <tr><td>نام پایگاه</td><td><input type="text" dir="ltr" id="edt_DBName" value="_AccXP_"/></td></tr>--}}
{{--    <tr><td>&nbsp;</td></tr>--}}
{{--    <tr>--}}
{{--        <td>متد</td>--}}
{{--        <td class="rightAlign">--}}
{{--            <select id="edt_Method">--}}
{{--                <option value="WS_AddDet">افزودن دفترتلفن، تفصیلی، مرکز</option>--}}
{{--                <option value="WS_AddArt">افزودن سند حسابداری</option>--}}
{{--                <option value="WS_AddInvPreArt">افزودن برگه درخواست کالا</option>--}}
{{--                <option value="WS_AddInvArt">افزودن برگه انبار</option>--}}
{{--                <option value="WS_AddSrvPreArt">افزودن برگه درخواست خدمات</option>--}}
{{--                <option value="WS_AddSrvArt">افزودن برگه انجام خدمات</option>--}}
{{--                <option value="WS_AddSalesArt">افزودن برگه فروش</option>--}}
{{--                <option value="WS_ModifySalesArtOpps">اصلاح طرف حساب‌های یک برگه فروش</option>--}}
{{--                <option value="WS_DelInvPreArt">حذف یک برگه درخواست کالا</option>--}}
{{--                <option value="WS_DelInvPreTrans">حذف سطرهایی از یک برگه درخواست کالا</option>--}}
{{--                <option value="WS_CancelInvPreTrans">لغو سطرهایی از یک برگه درخواست کالا</option>--}}
{{--                <option value="WS_DelSrvPreArt">حذف یک برگه درخواست خدمات</option>--}}
{{--                <option value="WS_DelSrvPreTrans">حذف سطرهایی از یک برگه درخواست خدمات</option>--}}
{{--                <option value="WS_CancelSrvPreTrans">لغو سطرهایی از یک برگه درخواست خدمات</option>--}}
{{--                <option value="WS_GetMatList">اخذ مشخصات کالاها و خدمات</option>--}}
{{--                <option value="WS_GetTariffList">اخد مشخصات تعرفه‌ها</option>--}}
{{--                <option value="WS_GetMatRemain">اخذ موجودی کالاها</option>--}}
{{--            </select>--}}
{{--        </td>--}}
{{--    </tr>--}}
{{--    <tr><td>&nbsp;</td></tr>--}}
{{--    <tr>--}}
{{--        <td>Data (XML)</td>--}}
{{--        <td>--}}
{{--            <textarea dir="ltr" wrap="off" id="edt_Data" Rows="5"></textarea>--}}
{{--        </td>--}}
{{--    </tr>--}}
{{--    <tr><td>&nbsp;</td></tr>--}}
{{--    <tr>--}}
{{--        <td>نتیجه اجرا: </td>--}}
{{--        <td>--}}
{{--            <textarea dir="ltr" wrap="off" id="lbl_Result" Rows="5"></textarea>--}}
{{--        </td>--}}
{{--    </tr>--}}
{{--    <tr><td>&nbsp;</td></tr>--}}
{{--    <tr><td></td><td><button class="btnAction" onclick="Run()">اجرا (افزودن)</button></td></tr>--}}
{{--</table>--}}
{{--</body>--}}
{{--</html>--}}













{{--        .padd {--}}
{{--            display:block;padding: 10px;--}}
{{--        &.center {--}}
{{--             text-align:center;--}}
{{--         }--}}
{{--        }--}}

{{--        .ad {--}}
{{--            display: block;--}}
{{--            background: rgba(50,50,50,0.5);--}}
{{--            color: white;--}}
{{--            text-decoration: none;--}}
{{--            text-align:center;--}}
{{--            padding: 15px;--}}
{{--            font-weight: 800;--}}
{{--            border-bottom: 2px solid black;--}}
{{--            border-top: 2px solid black;--}}
{{--        }--}}

{{--        a{--}}
{{--            text-decoration:none;--}}
{{--            color:black;--}}
{{--        }--}}

{{--    </style>--}}

{{--@endsection--}}
