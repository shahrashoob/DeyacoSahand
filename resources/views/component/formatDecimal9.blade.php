@php
    if (!function_exists('formatDecimal9')) {
        function formatDecimal9($value, $exactNine = false)
        {
            if (!is_numeric($value)) {
                return (string)$value;
            }

            // اطمینان از این‌که PHP آن را علمی چاپ نکند
            // sprintf همیشه عدد را به صورت اعشاری می‌دهد
            $formatted = sprintf('%.9f', $value);

            if ($exactNine) {
                return $formatted; // دقیقاً 9 رقم اعشار
            }

            // اگر نیاز به حذف صفرهای اضافی بود
            return rtrim(rtrim($formatted, '0'), '.');
        }
        }
@endphp