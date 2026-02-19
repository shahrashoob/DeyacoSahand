<!DOCTYPE html>
<html lang="en">
<head>
    @include("layouts._head")

    <style>
        body {
            font-family: IRANSans !important;
        }

        .mb-4, .my-4 {

        }
    </style>
</head>
<body>
<div class="auth-wrapper">

    <div class="auth-content subscribe">
        @include("layouts._messages",["type"=>'public_1'])
        @yield("content")
    </div>
</div>

@include("layouts._footer")
@yield("scripts")
</body>


</html>
