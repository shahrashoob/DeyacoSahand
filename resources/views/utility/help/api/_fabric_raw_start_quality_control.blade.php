
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>شروع عملیاتی کنترل خام </h5>
                </div>

                <div class="card-body">

                    <h4>ساختار URL</h4>
                    <pre
                        class="highlight">{{url("")}}/{API-KEY}/Fabric_Raw/API/QualityControl/Start/{device_code}/{form_code}</pre>

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
    "device_code": 1
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


