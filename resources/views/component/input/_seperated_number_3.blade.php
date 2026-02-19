<script>
    /**
     * ووردی باید حتما به صورت text باشد و در زمان دریافت آن توسط سرور باید ، حذف گردد.
     */
    $(document).ready(function () {
        // تابع قالب‌بندی عدد با جداکننده هزارگان
        function formatNumberInput($input) {
            let val = $input.val().replace(/,/g, ""); // حذف کاماهای قبلی
            if (val === "" || isNaN(val)) return; // اگر مقدار خالی یا نامعتبر است، کاری نکن
            let parts = val.split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $input.val(parts.join("."));
        }

        // هنگام تایپ (برای تغییرات لحظه‌ای)
        $("input.numeric").on("input", function () {
            formatNumberInput($(this));
        });

        // هنگام لود صفحه (برای مقدارهای اولیه)
        $("input.numeric").each(function () {
            formatNumberInput($(this));
        });
    });

</script>