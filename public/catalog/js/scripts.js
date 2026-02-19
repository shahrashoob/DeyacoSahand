(function ($) {
    "use strict";
    /*==================================================================
     [ Validate ]*/
    var name = $('.validate-input input[name="mobile"]');
    var email = $('.validate-input input[name="company"]');
    var subject = $('.validate-input input[name="fullname"]');

    $('.validate-form').on('submit',function(){
        var check = true;
        if($(name).val().trim() == ''){
            showValidate(name);
            check=false;
        }
        if($(subject).val().trim() == ''){
            showValidate(subject);
            check=false;
        }
        if($(email).val().trim() == ''){
            showValidate(email);
            check=false;
        }


        return check;
    });
    $('.validate-form .input1').each(function(){
        $(this).focus(function(){
            hideValidate(this);
        });
    });
    function showValidate(input) {
        var thisAlert = $(input).parent();
        $(thisAlert).addClass('alert-validate');
    }
    function hideValidate(input) {
        var thisAlert = $(input).parent();
        $(thisAlert).removeClass('alert-validate');
    }
})(jQuery);