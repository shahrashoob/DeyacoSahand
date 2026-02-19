<script>
    (function($){
        $.fn.simpleLightbox = function(options){
            const settings = $.extend({
                backdrop: 'rgba(255,255,255,.45)',
                closeText: 'بستن'
            }, options);

            // ایجاد ساختار لایت‌باکس در صورت عدم وجود
            if (!$('#lightbox').length) {
                $('body').append(`
        <dialog id="lightbox" aria-label="نمایش تصویر کالا">
          <div class="lb-wrap">
            <img id="lbImg" class="lb-img" alt="نمایش بزرگ تصویر" />
            <div class="lb-bar">
              <span id="lbTitle"></span>
              <button class="lb-close" id="lbClose" type="button">${settings.closeText}</button>
            </div>
          </div>
        </dialog>
      `);
            }

            const lightbox = document.getElementById('lightbox');

            // استایل پایه
            const style = `
      dialog#lightbox { width: min(92vw, 980px); border: none; padding: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,.25); }
      dialog::backdrop { background: ${settings.backdrop}; }
      .lb-wrap { position: relative; background: #000; }
      .lb-img { display: block; width: 100%; height: auto; max-height: 85vh; object-fit: contain; background: #F4F7FA; }
      .lb-bar { display: flex; justify-content: space-between; align-items: center; gap: 8px; padding: 10px 12px; background: #3F4D67; color: #fff; font-size: 14px; }
      .lb-close { appearance: none; border: 1px solid #04a9f5; background: #04a9f5; color: #fff; padding: 6px 10px; border-radius: 8px; cursor: pointer; }
    `;
            if (!$('#lightbox-style').length) {
                $('<style id="lightbox-style">').text(style).appendTo('head');
            }

            // رفتار کلیک روی المنت‌ها
            this.on('click', function(e){
                e.preventDefault();
                const src = $(this).data('img') || $(this).attr('href');
                const title = $(this).data('title') || '';

                if (typeof lightbox.showModal === 'function') {
                    $('#lbImg').attr({ src: src, alt: title });
                    $('#lbTitle').text(title);
                    lightbox.showModal();
                } else {
                    window.open(src, '_blank');
                }
            });

            // بستن لایت‌باکس
            $(document).on('click', '#lbClose', function(){ lightbox.close(); });
            $('#lightbox').on('click', function(e){
                const rect = $('#lbImg')[0].getBoundingClientRect();
                const inside = e.clientX >= rect.left && e.clientX <= rect.right && e.clientY >= rect.top && e.clientY <= rect.bottom;
                if (!inside) lightbox.close();
            });
            $(document).on('keydown', function(e){ if (e.key === 'Escape' && lightbox.open) lightbox.close(); });

            return this;
        };
    })(jQuery);


    $(function () {
        $('a.view-image').simpleLightbox();
    });

</script>
