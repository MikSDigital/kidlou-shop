var pay_actions;
var pay_isEmpty = true;
var isOrderSave = false;

$(document).ready(function () {
    var height = $(document).outerHeight();
    var footer = $('.ma-footer-container').outerHeight();
    var header_navigation = $('.header-navigation').outerHeight();

    var custom_header = $('.custom-header').outerHeight();
    var input_search = $('#search').outerHeight();
    var input_top = (custom_header - input_search) / 2;
    if (height < 1000) {
        $('.main').css({'min-height': (height - (footer + custom_header + header_navigation + 30)) + 'px'});
    }


    $(window).resize(function () {
        if ($(window).width() > 1000) {
            $('.navigation-responsive').removeClass('in');
        }
    });
    $(window).scroll(function () {
        if ($(document).scrollTop() > custom_header) {
            $('.header-navigation').addClass('navbar-fixed-top');
        } else {
            $('.header-navigation').removeClass('navbar-fixed-top');
        }
    });
    $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).parent().siblings().removeClass('open');
        $(this).parent().toggleClass('open');
    });
    $('.parentMenu span.submenu').parent().mouseenter(function () {
        var width = $(this).children().outerWidth();
        //if (!$(this).children().next().is(':visible')) {
        $(this).children().next().css({'min-width': width + 'px'});
        $(this).children().next().show();
    }).mouseleave(function () {
        $(this).children().next().hide();
    });
    $('.title-vmega-menu').click(function () {
        if (!$('.category-vmega_toggle').is(':visible')) {
            $('.category-vmega_toggle').show();
        } else {
            $('.category-vmega_toggle').hide();
        }
    });
    $('.category-vmega_toggle > .pt_menu.hasMenu').hover(function () {
        $(this).children().next().show();
        var width = $('.category-vmega_toggle').width();
        $(this).children().next().animate({"left": width + "px"}, "fast");
    }, function () {
        $(this).children().next().hide();
        $(this).children().next().css({'left': '110%'});
    });
    $('.tab_categorys > li').click(function () {
        $('.tabs-category_tabs > li').each(function () {
            $(this).removeClass();
        });
        $(this).addClass('active');
        categoryCarousel();
    });

    function categoryCarousel() {
        $('.tabs-category_tabs > li').each(function () {
            var name = $(this).children().attr('class');
            if ($(this).hasClass('active')) {
                $('#' + name).fadeIn();
            } else {
                $('#' + name).hide();
            }
        });
    }

    categoryCarousel();


    $(document).on('focusout', '.send-order input', function (el) {
        el.preventDefault();
        if (isPaypalButton()) {
            // check if all inputs are empty
            if (!isInputFieldsEmptyForPaypal(el) && !isOrderSave) {
                saveOrderForPayPal(true);
            }
        }
    });


    $(document).on('click', '.send-order .save_new_user', function (el) {
        if (isPaypalButton()) {
            // check if all inputs are empty
            if (!isCheckboxEmptyForPaypal(pay_isEmpty) && !isOrderSave) {
                saveOrderForPayPal(true);
            }
        }
    });

    $(document).on('click', '.img-product-thumbs', function (el) {
        $(".img-product-big").attr('src', $(this).attr('src')).fadeIn();
    });

    // checkout payment method click
    $('.payment-methods .radio input').click(function (el) {
        // unshow all others elements
        $('.payment-methods .payment-infos').each(function () {
            if ($(this).children().is(':visible')) {
                $(this).children().hide();
            }
        });
        // show element
        $(this).parent().parent().next().next().children().show();
        var typ = $(this).data('short-name');
        var url = $(this).data('cash-url');
        if (typ != 'pp') {
            $('.paypal-button').hide();
            $('.reserved-order').show();
        } else {
            $('.paypal-button').show();
            $('.reserved-order').hide();
        }
        sendPaymentCash(typ, url, el);
        if (!isInputFieldsEmptyForPaypal(el) && !isOrderSave && typ == 'pp') {
            saveOrderForPayPal(false);
        }
    });

    function sendPaymentCash(typ, url, el) {
        $('#bodyoverlay-content').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            type: 'get',
            success: function (data) {
                data = jQuery.parseJSON(data);
                if ($('#cash-cost').length > 0) {
                    $('#cash-cost').remove();
                }

                if (data.html_cash_cost != '') {
                    $(data.html_cash_cost).insertAfter('#livraison-cost');
                }
                $('#txt-total-price').children().html(data.txt_total_price);
                if (typ != 'pp') {
                    $('#bodyoverlay-content').fadeOut();
                } else {
                    if (isInputFieldsEmptyForPaypal(el)) {
                        $('#bodyoverlay-content').fadeOut();
                    }
                }
            }
        });
    }


    $('#language-currency').hover(function () {
        $(this).children().next().show()
    }, function () {
        $(this).children().next().hide();
    });
    $('.top-cart-wrapper').hover(function () {
        $(this).find('.top-cart-content').show();
    }, function () {
        $(this).find('.top-cart-content').hide();
    });
    $('.top-link').hover(function () {
        $(this).find('.toplink-inner').show();
    }, function () {
        $(this).find('.toplink-inner').hide();
    });

    $(document).on('click', '.input-error-field', function (e) {
        if ($(this).children().is(':radio')) {
            $('.payment-methods .form-must-field').each(function () {
                $(this).parent().removeClass('input-error-field');
            });
        }
    });

    $(document).on('click', '.payment-methods input', function (e) {
        $('.payment-methods h1').removeClass('input-error-field');
    });

    $(document).on('change', '.input-error-field', function (e) {
        $(this).removeClass('input-error-field');
    });

    $(document).on('keydown', '.input-error-field', function (e) {
        $(this).removeClass('input-error-field');
    });

    $('.reserved-order').click(function () {
        // inital
        $(".send-order input[name^='billing'").each(function () {
            $(this).removeClass('input-error-field');
        });
        $(".send-order input[name^='shipping'").each(function () {
            $(this).removeClass('input-error-field');
        });
        $(".send-order select[name^='shipping'").each(function () {
            $(this).removeClass('input-error-field');
        });
        $("input[name=paymenttyp]").each(function () {
            $(this).parent().removeClass('input-error-field');
        });
        $('.billing_email_message').hide();
        var isSend = true;
        var id = '';
        // check must fields billing , shipping
        var arr_typs = ['billing', 'shipping'];
        $.each(arr_typs, function (i, typ) {
            $(".send-order input[name^='" + typ + "'").each(function () {
                if ($(this).hasClass('form-must-field')) {
                    if ($(this).attr('id') == "billing_email") {
                        if (!validateEmail($(this).val())) {
                            isSend = false;
                            $(this).addClass('input-error-field');
                            $('.billing_email_message').show();
                        } else {
                            $('.billing_email_message').hide();
                        }
                    }

                    if ($(this).val() == '') {
                        isSend = false;
                        $(this).addClass('input-error-field');
                        id = $(this).attr('id');
                    }
                }
            });

            $(".send-order select[name^='" + typ + "'").each(function () {
                if ($(this).hasClass('form-must-field')) {
                    console.log($(this).val());
                    if ($(this).val() == '') {
                        isSend = false;
                        $(this).addClass('input-error-field');
                        id = $(this).attr('id');
                    }
                }
            });
        });

        // mode payment
        var isChecked = false;
        $("input[name=paymenttyp]").each(function () {
            if ($(this).hasClass('form-must-field')) {
                if ($(this).is(':checked')) {
                    isChecked = true;
                }
            }
        });

        if (!isChecked) {
            $('.payment-methods h1').addClass('input-error-field');
        }

        // springt zu den input fields, wenn Fehler
        if (id != '') {
            $('html, body').animate({
                scrollTop: $("#" + id).offset().top - $('.header-navigation').height() - 25
            }, 200);
            return false;
        }

        // springt zu den input radiobuttons, wenn Fehler
        if (!isChecked) {
            $('html, body').animate({
                scrollTop: $(".payment-methods").offset().top - 25
            }, 200);
            return false;
        }

        // is new user
        $(".send-order input[name^='billing'").each(function () {
            if ($(this).attr('name') == 'billing[user_new]') {
                if ($(this).is(':checked')) {
                    if ($("input[name='billing[password1]'").val().length >= 6 && $("input[name='billing[password1]'").val().length <= 8) {
                        if ($("input[name='billing[password1]'").val() != $("input[name='billing[password2]'").val()) {
                            isSend = false;
                            alert($("input[name='billing[password1]'").data('password-equal'));
                        }
                    } else {
                        isSend = false;
                        alert($("input[name='billing[password1]'").data('password-length'));
                    }
                }
            }
        });
        if (!isChecked) {
            isSend = false;
            $("input[name=paymenttyp]").each(function () {
                $(this).parent().addClass('input-error-field');
            });
        }


        if (isSend) {
            $('#bodyoverlay').fadeIn();
            var message = $('#bodyoverlay').data('iframe-message');
            $('#overlay-iframe').contents().find('body').html('<div style="text-align:center;">' + message + ' <img src="/media/icons/ajax-loader-16x16.gif"></div>');
            $('#overlay-iframe').css({'width': '80%', 'height': '60%', 'margin-left': 'auto', 'margin-right': 'auto', 'text-align': 'center', 'padding-top': '20px'});
            $('#bodyoverlay').css({'background-image': 'none'});
            $('.send-order').submit();
        }
    });

    // In iframe wenn weiter geklickt wird
    var css = '<link href="/css/main.css" rel="stylesheet" />';
    $('#overlay-iframe').load(function () {
        var overlay_iframe = $('#overlay-iframe').contents();
        overlay_iframe.find("head").append(css);
        overlay_iframe.find('.order-success').click(function () {
            setLocation($(this).data('url'));
        });
    });
    // login form
    $(document).on('click', '.user-login', function (e) {
        e.preventDefault();
        $('#bodyoverlay-login').css({'background-image': 'none'});
        $('#bodyoverlay-login').fadeIn();
    });
    // login form
    $(document).on('click', '.close', function (e) {
        e.preventDefault();
        //$('#bodyoverlay-login').css({'background-image': 'none'});
        $('#bodyoverlay-login').fadeOut();
    });
    $(document).on('click', '.save_new_user', function (e) {
        if ($(this).is(':checked')) {
            $(this).prop('checked', true);
            $('.new_password').show();
            $('#billing_password1').addClass('form-must-field');
            $('#billing_password2').addClass('form-must-field');
        } else {
            $(this).prop('checked', false);
            $('.new_password').hide();
            $('#billing_password1').removeClass('form-must-field');
            $('#billing_password2').removeClass('form-must-field');
        }

    });

    $(document).on('click', '.login-closed', function (e) {
        $('#bodyoverlay-login').hide();
    });

    $(document).on('change', '.pagelimit-select', function (e) {
        var url = window.location.href;
        var pos = url.indexOf('?page=');
        var url_new = url.substring(0, pos);
        var url_new1 = url.substring(pos);
        var pos1 = url_new1.indexOf('&limit=');
        url_new1 = url_new1.substring(0, pos1);
        if (pos != -1) {
            url = url_new + url_new1 + '&limit=' + $(this).val();
        } else {
            url += '?page=1&limit=' + $(this).val();
        }
        setLocation(url);
    });

    $(document).on('focusout', '#billing_email', function (e) {
        if (!validateEmail($(this).val())) {
            $(this).addClass('input-error-field');
            $('.billing_email_message').show();
        } else {
            $('.billing_email_message').hide();
        }
    });

});

function isPaypalButton() {
    if ($('.paypal-button').is(':visible')) {
        return true;
    }
    return false;
}

function isInputFieldsEmptyForPaypal(el) {
    var isEmpty = false;
    var arr_typs = ['billing', 'shipping'];
    $.each(arr_typs, function (i, typ) {
        // check must fields billing
        $(".send-order input[name^='" + typ + "'").each(function () {
            if ($(this).hasClass('form-must-field')) {
                if ($(this).attr('id') != el.id) {
                    if ($(this).val() == '') {
                        isEmpty = true;
                    }
                } else {
                    if (el.value == '') {
                        isEmpty = true;
                    }
                }
                // check email
                if ($(this).attr('id') == 'billing_email') {
                    if (!validateEmail($(this).val())) {
                        isEmpty = true;
                    }
                }
            }
        });
    });
    // is not must field if is checked then must show the password are equal
    isEmpty = isCheckboxEmptyForPaypal(isEmpty);
    return isEmpty;
}


function isCheckboxEmptyForPaypal(isEmpty) {
    // if checked fields
    if ($('.send-order .save_new_user').is(':checked')) {
        if (!checkPassword()) {
            isEmpty = true;
        }
    }
    return isEmpty;
}


function setErrorFieldsForPaypal() {
    var arr_typs = ['billing', 'shipping'];
    $.each(arr_typs, function (i, typ) {
        $(".send-order input[name^='" + typ + "'").each(function () {
            if ($(this).hasClass('form-must-field')) {
                if ($(this).attr('id') == "billing_email") {
                    if (!validateEmail($(this).val())) {
                        $(this).addClass('input-error-field');
                        $('.billing_email_message').show();
                    } else {
                        $('.billing_email_message').hide();
                    }
                }
                if ($(this).val() == '') {
                    $(this).addClass('input-error-field');
                }
            }
            // password handling if user will create user
            if ($(this).attr('name') == 'billing[user_new]') {
                if ($(this).is(':checked')) {
                    if ($("input[name='billing[password1]'").val().length >= 6 && $("input[name='billing[password1]'").val().length <= 8) {
                        if ($("input[name='billing[password1]'").val() != $("input[name='billing[password2]'").val()) {
                            alert($("input[name='billing[password1]'").data('password-equal'));
                        }
                    } else {
                        alert($("input[name='billing[password1]'").data('password-length'));
                    }
                }
            }

        });
    });
}

function saveOrderForPayPal(isOverlay) {
    isOrderSave = true;
    var order_url = $('#paypal-button').data('save-order-url');
    if (isOverlay) {
        $('#bodyoverlay-content').fadeIn();
    }
    $.ajax({
        url: order_url, // point to server-side PHP script
        type: 'POST',
        data: $('.send-order').serialize(),
        success: function (data) {
            $('#bodyoverlay-content').fadeOut();
            pay_actions.enable();
            pay_isEmpty = false;
            if (!isOverlay) {
                $('#bodyoverlay-content').fadeOut();
            }
        }
    });
}


function getPaypalCancel(cancelUrl, paymentID) {
    var url = cancelUrl + '&paymentID=' + paymentID;
    window.location.href = url;
}

function getPaypalReturn(returnUrl, paymentID) {
    var url = returnUrl + '&paymentID=' + paymentID;
    window.location.href = url;
}


function checkPassword() {
    var isPassword = true;
    if ($("input[name='billing[password1]'").val().length >= 6 && $("input[name='billing[password1]'").val().length <= 8) {
        if ($("input[name='billing[password1]'").val() != $("input[name='billing[password2]'").val()) {
            isPassword = false;
        }
    } else {
        isPassword = false;
    }
    return isPassword;
}

function validateEmail(sEmail) {
    var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
    if (filter.test(sEmail)) {
        return true;
    } else {
        return false;
    }
}

function setLocation(href) {
    window.location.href = href;
}
