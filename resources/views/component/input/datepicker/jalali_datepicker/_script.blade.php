{{--<script src="{{asset('assets/plugins/datepicker/js/jquery-1.10.1.min.js')}}" ></script>--}}

<script type="text/javascript"
        src="{{asset('assets/plugins/jalali_datepicker/dist/jalalidatepicker.min.js')}}"></script>

{{--https://github.com/majidh1/JalaliDatePicker?tab=readme-ov-file--}}
<script type="text/javascript">
    jalaliDatepicker.startWatch({
        time: true,
        hasSecond: {{$hasSecond??0}},
        minDate: "attr",
        maxDate: "attr",
        showCloseBtn:true
    });

    $(".jalali_datepicker_change").change(function (e) {

        var miladiInput = document.getElementById(this.getAttribute("data-jdp-miladi-input"));
        var only_time= this.getAttribute("data-jdp-only-time");

        if (!this.value) {
            miladiInput.value = "";
            return;
        }
        // فقط زمان
       if (only_time){
           miladiInput.value = this.value;
           return;
       }

        var jalaliDateTime = extractJalaliDateTime(this.value);
        var miladiDate = jalali_to_gregorian(jalaliDateTime.year, jalaliDateTime.month, jalaliDateTime.day).join("/");
        var miladiTime = jalaliDateTime.hour + ":" + jalaliDateTime.minute + ":" + jalaliDateTime.second;
        var miladiDateTime = miladiDate + " " + miladiTime;
        miladiInput.value = miladiDateTime

    });

    function jalali_to_gregorian(jy, jm, jd) {
        jy = Number(jy);
        jm = Number(jm);
        jd = Number(jd);
        var gy = (jy <= 979) ? 621 : 1600;
        jy -= (jy <= 979) ? 0 : 979;
        var days = (365 * jy) + ((parseInt(jy / 33)) * 8) + (parseInt(((jy % 33) + 3) / 4))
            + 78 + jd + ((jm < 7) ? (jm - 1) * 31 : ((jm - 7) * 30) + 186);
        gy += 400 * (parseInt(days / 146097));
        days %= 146097;
        if (days > 36524) {
            gy += 100 * (parseInt(--days / 36524));
            days %= 36524;
            if (days >= 365) days++;
        }
        gy += 4 * (parseInt((days) / 1461));
        days %= 1461;
        gy += parseInt((days - 1) / 365);
        if (days > 365) days = (days - 1) % 365;
        var gd = days + 1;
        var sal_a = [0, 31, ((gy % 4 == 0 && gy % 100 != 0) || (gy % 400 == 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        var gm
        for (gm = 0; gm < 13; gm++) {
            var v = sal_a[gm];
            if (gd <= v) break;
            gd -= v;
        }
        return [gy, gm, gd];
    }

    function extractJalaliDateTime(jalaliDateTimeString) {
        var parts = jalaliDateTimeString.split(" ");
        var jalaliDate = parts[0];
        var jalaliTime = parts[1];

        var dateParts = jalaliDate.split("/");
        var jalaliYear = parseInt(dateParts[0]);
        var jalaliMonth = parseInt(dateParts[1]);
        var jalaliDay = parseInt(dateParts[2]);


        var jalaliHour = 0;
        var jalaliMinute = 0;
        var jalaliSecond = 0;
        if (jalaliTime) {
            var timeParts = jalaliTime.split(":");
            jalaliHour = parseInt(timeParts[0]);
            jalaliMinute = parseInt(timeParts[1]);
            jalaliSecond = {{$hasSecond??0}}? parseInt(timeParts[2]): 0;
        }
        return {
            year: jalaliYear,
            month: jalaliMonth,
            day: jalaliDay,
            hour: jalaliHour,
            minute: jalaliMinute,
            second: jalaliSecond
        };
    }
</script>
