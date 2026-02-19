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
        <form id="form1" method="get" action="./page23" autocomplete="false">
            @csrf
            <div class="card">
                <div class="row no-gutters">
                    <div class="col-md-12">
                        {{--                        <h6 style="text-align: center; font-weight: bold;--}}
                        {{--    margin-top: 50px;--}}
                        {{--    margin-bottom: 10px; " class="mb-4">  {{$setting["software_name"]->string_value??""}}</h6>--}}

                    </div>
                    <div class="col-md-12 col-lg-12">
                        <div class="card-body ">
                            <div class="row justify-content-center">
                                <div class="col-sm-12">
                                    <h4 style="text-align: center; font-weight: bold; margin-bottom: 10px; "
                                        class="mb-4">
                                        تعریف الگوریتم بارکد برای تامین کننده
                                    </h4>
                                    <div class="text-center">
                                        <img src="../assets/images/barcode.png" style="width: 200px">
                                    </div>


                                    <table>
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>

                                            <td>
                                                <span style="color: #481f5c;font-size: 18px">تعداد اعداد بارکد:  </span>
                                            </td>

                                            <td><input type="text" autofocus class="form-control"
                                                       autocomplete="new-password"
                                                       name="email" pattern="[0-9]{3}" placeholder=""></td>

                                        </tr>
                                        <tr>

                                            <td>
                                                <span style="color: #481f5c;font-size: 18px">اعداد وزن:  </span>
                                            </td>

                                            <td>
                                                <span style="color: #481f5c;font-size: 18px"> از عدد</span>
                                                <input type="text" autofocus class="form-control"
                                                       autocomplete="new-password"
                                                       name="email" pattern="[0-9]{3}" placeholder=""></td>
                                            <td style="width: 5px"></td>

                                            <td>
                                                <span style="color: #481f5c;font-size: 18px">تا عدد</span>
                                                <input type="text" autofocus class="form-control"
                                                       autocomplete="new-password"
                                                       name="email" pattern="[0-9]{3}" placeholder=""></td>

                                        </tr>
                                        <tr>

                                            <td>
                                                <span style="color: #481f5c;font-size: 18px">اعداد کالا:  </span>
                                            </td>

                                            <td>
                                                <span style="color: #481f5c;font-size: 18px"> از عدد</span>
                                                <input type="text" autofocus class="form-control"
                                                       autocomplete="new-password"
                                                       name="email" pattern="[0-9]{3}" placeholder=""></td>

                                            <td style="width: 5px"></td>
                                            <td>
                                                <span style="color: #481f5c;font-size: 18px">تا عدد</span>
                                                <input type="text" autofocus class="form-control"
                                                       autocomplete="new-password"
                                                       name="email" pattern="[0-9]{3}" placeholder=""></td>

                                        </tr>
                                        <tr>

                                            <td>
                                                <span style="color: #481f5c;font-size: 18px">اعداد نوع بسته بندی:  </span>
                                            </td>

                                            <td>
                                                <span style="color: #481f5c;font-size: 18px"> از عدد</span>
                                                <input type="text" autofocus class="form-control"
                                                       autocomplete="new-password"
                                                       name="email" pattern="[0-9]{3}" placeholder=""></td>

                                            <td style="width: 5px"></td>
                                            <td>
                                                <span style="color: #481f5c;font-size: 18px">تا عدد</span>
                                                <input type="text" autofocus class="form-control"
                                                       autocomplete="new-password"
                                                       name="email" pattern="[0-9]{3}" placeholder=""></td>

                                        </tr>
                                        </tbody>
                                    </table>
                                    <br/>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-lg btn-primary shadow-2 mb-4"
                                        >ایجاد الگوریتم جدید
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </form>
    </div>
</div>

@include("layouts._footer")
</body>


</html>
