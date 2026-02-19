
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت عیب پارچه  </h5>
                </div>

                <div class="card-body">

                    <h4>ساختار URL</h4>
                    <pre
                        class="highlight">{{url("")}}/{API-KEY}/Fabric_Raw/API/QualityControl/NewDefect/
                                    {device_code}/{form_code}/{defect_code}/{meter_amount}/{start_or_end}
                    </pre>

                    <h4>پارامترهای ورودی</h4>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>پارامتر</th>
                            <th>الزامی/اختیاری</th>
                            <th>نوع</th>
                            <th>توضیح</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>device_code</td>
                            <td>اجباری</td>
                            <td>integer</td>
                            <td>کد دستگاه کنترل کیفیت</td>
                        </tr>
                        <tr>
                            <td>form_code</td>
                            <td>اجباری</td>
                            <td>string</td>
                            <td>شماره فرم</td>
                        </tr>
                        <tr>
                            <td>defect_code</td>
                            <td>اجباری</td>
                            <td>integer</td>
                            <td>کد خطا</td>
                        </tr>
                        <tr>
                            <td>meter_amount</td>
                            <td>اجباری</td>
                            <td>float</td>
                            <td>متراژی که خطا در آن رخ داده</td>
                        </tr>
                        <tr>
                            <td>start_or_end</td>
                            <td>اختیاری</td>
                            <td>integer</td>
                            <td>
                                1: شروع | 2: پایان
                                <br/>
                                    در صورتی که نوع عیب بازه ای باشد،
                                     اجباری است.
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <h4>ساختار خروجی </h4>
                    <div class="highlight">
                            <pre>
{
  "result": {
    "status": "200",
    "message": "عملیات موفق"
  },
  "data": {
    "form_code": 100,
    "device_code": 1,
    "meter_amount": 120
  }
}
                            </pre>
                    </div>



                    <p>
                        <strong>Status :</strong>
                        کد حاصل از اجرای متد که نشان دهنده اجرای موفق یا ناموفق آن است. در صورتی که مقدار آن
                        <strong>200</strong>
                        باشد به معنای اجرای درست متد است
                        و در غیر اینصورت باید به لیست خطاهای احتمالی در هنگام اجرای متد
                        مراجعه نمائید.
                        <br>
                        <strong>Message :</strong>
                        توضیح مربوط به کد می‌باشد.
                    </p>

                </div>

            </div>
        </div>
    </div>


