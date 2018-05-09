/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(function () {

    // new Product
    $(document).on('click', '.new-product', function (e) {
        e.preventDefault();
        var buttonEle = $(this);
        var isSend = true;
        var form_data = new FormData();
        $('input, select').each(
                function (index) {
                    var input = $(this);
                    if (input.next().hasClass("error-message")) {
                        input.next().remove();
                    }
                    if (!$(input).is("select")) {
                        if (input.val() == '') {
                            input.after('<div class="error-message">' + buttonEle.data('error-message-input') + '</div>').fadeIn();
                            isSend = false;
                        } else {
                            // check if is price
                            if (input.attr('name').indexOf('sale') != -1 || input.attr('name').indexOf('price') != -1) {
                                if (!$.isNumeric(input.val())) {
                                    input.after('<div class="error-message">' + buttonEle.data('error-message-input-price') + '</div>').fadeIn();
                                    isSend = false;
                                }
                            }
                        }
                    } else {
                        if (input.val() == null) {
                            input.after('<div class="error-message">' + buttonEle.data('error-message-select') + '</div>').fadeIn();
                            isSend = false;
                        }
                    }
                    form_data.append(input.attr('name'), input.val());
                }
        );

        if (!isSend) {
            form_data = new FormData();
            return false;
        }

        var url = $(this).data('url');
        var url_redirect = $(this).data('url-redirect');
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (data) {
                data = jQuery.parseJSON(data);
                if (data.status == false) {
                    $('.sku').after('<div class="error-message">' + buttonEle.data('error-message-send') + '</div>').fadeIn();
                } else {
                    location.href = url_redirect + data.product_id + '/';
                }
                $('#bodyoverlay').fadeOut();
            }
        });

    });




    // Mutation category
    $(document).on('click', '.mutation-category', function (e) {
        e.preventDefault();
        var form_data = new FormData();
        form_data.append('product_id', $('input[name=product_id]').val());
        form_data.append('category_ids', $('select.category').val());
        var url = $(this).data('url');
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (data) {
                data = jQuery.parseJSON(data);
                $('#bodyoverlay').fadeOut();
            }
        });
    });
    // Mutation status
    $(document).on('click', '.mutation-status', function (e) {
        e.preventDefault();
        var answer = confirm('Wollen Sie den Status ändern?');
        if (answer) {
            var url = $(this).data('url');
            var form_data = new FormData();
            form_data.append('product_id', $('input[name=product_id]').val());
            $('#bodyoverlay').fadeIn();
            $.ajax({
                url: url, // point to server-side PHP script
                dataType: 'text', // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (data) {
                    data = jQuery.parseJSON(data);
                    var txtStatus = '';
                    if (data.status) {
                        txtStatus = $('.mutation-status').data('txt-status-ok');
                        $('.mutation-status').prop('checked', true);
                    } else {
                        txtStatus = $('.mutation-status').data('txt-status-nok');
                        $('.mutation-status').prop('checked', false);
                    }
                    $('.txt-status').html(txtStatus);
                    $('#bodyoverlay').fadeOut();
                }
            });
        }
    });


// Mutation common fields
    $(document).on('click', '.mutation-price-text', function (e) {
        e.preventDefault();
        var form_data = new FormData();
        var fields = $(this).data('fields');
        var url = $(this).data('url');
        var desc = 'tinyme';
        //alert(tinymce.get('longtext').getContent());
        form_data.append('product_id', $('input[name=product_id]').val());
        form_data.append('lang', $('input[name=lang]').val());
        fields = fields.split('-');
        $.each(fields, function (i, field) {
            if (field.indexOf(desc) != -1) {
                var strData = jQuery.trim(field.substr(desc.length));
                form_data.append(field, tinymce.get(strData).getContent());
            } else {
                form_data.append(field, $('.' + field).val());
            }
        });
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (data) {
                data = jQuery.parseJSON(data);
                $('#bodyoverlay').fadeOut();
            }
        });
    });
    /**
     *
     * Add Image Element
     */
    var count_image_element = 0;
    var count_element = 0;
    $(document).on('click', '.newimage', function (e) {
        e.preventDefault();
        var html = '<div class="row">';
        html += '<div class="col-md-11" style="padding-bottom:10px;"><input type="file" class="new-image" name="product[insert][image][insert][]" /></div>';
        html += '<div class="col-md-1" style="padding-bottom:10px;"><a href="#"><span class="admin-element-right glyphicon glyphicon-remove remove-input-file" aria-hidden="true"></span></a></div>';
        html += '</div>';
        $(this).parent().parent().parent().find('.row').each(function () {
            if ($(this).children().children().hasClass('newimage')) {
                $(this).prev().before(html);
                $('.admin-rowsendimage').show();
                count_image_element++;
            }
        });
    });
    /**
     * Remove image
     */
    $(document).on('click', 'a .remove-input-file', function (e) {
        e.preventDefault();
        $(this).parent().parent().parent().remove();
        count_image_element--;
        if (!count_image_element) {
            $('.admin-rowsendimage').hide();
        }
    });
    /**
     * Send image
     */
    $(document).on('click', '.sendimage', function (e) {
        e.preventDefault();
        var form_data = new FormData();
        var isAllowed = true;
        $.each($('.new-image'), function (i, val) {
            var file_data = $(this).prop('files')[0];
            if (file_data != undefined) {
                form_data.append('sendimages[]', file_data);
            } else {
                isAllowed = false;
            }
        });
        if (!isAllowed) {
            return false;
        }
        form_data.append('product_id', $('input[name=product_id]').val());
        form_data.append('lang', $('input[name=lang]').val());
        var url = $(this).data('urlimage');
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (data) {
                data = jQuery.parseJSON(data);
                var html = '';
                var newNodes = 0;
                $.each(data, function (i, val) {
                    if (i == 'html') {
                        html = val;
                    }
                });
                var obj = $('.productimages');
                $('.productimages').each(function () {
                    if ($(this).find('.image').length) {
                        obj = $(this);
                    }
                });
                obj.after(html);
                $('.productimages').each(function () {
                    if ($(this).find('.image').length) {
                        obj = $(this);
                    } else {
                        $(this).remove();
                    }
                });
                var oObj;
                obj = obj.next();
                while (!obj.hasClass('admin-rowsendimage')) {
                    oObj = obj.next();
                    obj.remove();
                    obj = oObj;
                }
                obj.hide();
                $('#bodyoverlay').fadeOut();
            }
        });
    });
    /**
     * Remove image
     */
    $(document).on('click', '.image-remove', function (e) {
        e.preventDefault();
        var product_id = $('input[name=product_id]').val();
        var image_id = $(this).data('image-remove');
        var lang = $('input[name=lang]').val();
        var answer = confirm('Wollen Sie das Bild löschen?');
        var url = $('.sendimage').data('urlimageremove');
        var obj = $(this);
        if (answer) {
            var form_data = new FormData();
            form_data.append('product_id', product_id);
            form_data.append('image_id', image_id);
            form_data.append('lang', lang);
            $('#bodyoverlay').fadeIn();
            $.ajax({
                url: url, // point to server-side PHP script
                dataType: 'text', // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (data) {
                    var objb = obj.parent().parent().prev();
                    obj.parent().parent().remove();
                    if (!objb.hasClass("productimages")) {
                        html = '<div class="row productimages">';
                        html += '<div class="col-md-2 col-image"><img src="/media/placeholder/placeholder80.jpg" title="" class="img-responsive"/></div>';
                        html += '<div class="col-md-2"><input type="radio" name="product[insert][image][default][]" checked="checked" /></div>';
                        html += '<div class="col-md-4">Placeholder</div>';
                        html += '<div class="col-md-3"></div>';
                        html += '<div class="col-md-1"></div>';
                        html += '</div>';
                        objb.after(html);
                    }
                    $('#bodyoverlay').fadeOut();
                }
            });
        }
    });
    /**
     * Add new additional Elements
     */
    $(document).on('click', '.newadditionalelement', function (e) {
        e.preventDefault();
        var html = getAdditionalHtml();
        $(this).parent().parent().parent().find('.row').each(function () {
            if ($(this).children().children().hasClass('newadditionalelement')) {
                $(this).prev().before(html);
                $('.admin-rowsenadditionalelement').show();
                count_element++;
            }
        });
    });
    /**
     * Remove Additional Elements
     */
    $(document).on('click', 'a .remove-input-element', function (e) {
        e.preventDefault();
        var obj = $(this).parent().parent().parent();
        obj.next().remove(); // sku remove

        var calendartyplist = $('input[name=calendartyplist]').val();
        calendartyplist = jQuery.parseJSON(calendartyplist);
        // Price remove
        for (i = 0; i < Object.keys(calendartyplist).length; i++) {
            obj.next().remove();
        }

// Textfelder language remove
        $('select.language option').each(function (i, val) {
            obj.next().remove();
        });
        // File remove
        $(this).parent().parent().parent().remove();
        //$(this).parent().parent().parent().next().remove();
        count_element--;
        if (!count_element) {
            $('.admin-rowsenadditionalelement').hide();
        }
    });
    /**
     * Send image
     */
    $(document).on('click', '.mutation-pdf-accessoires', function (e) {
        e.preventDefault();
        var form_data = new FormData();
        var isAllowed = true;
        var file_data = $('.pdf_accessoires').prop('files')[0];
        if (file_data != undefined) {
            if (file_data['type'] == 'application/pdf') {
                form_data.append('pdf', file_data);
            } else {
                alert('Ist kein PDF File!');
                isAllowed = false;
            }
        } else {
            alert('Bitte File hochladen!');
            isAllowed = false;
        }
        if (!isAllowed) {
            return false;
        }

        form_data.append('product_id', $('input[name=product_id]').val());
        form_data.append('lang', $('input[name=lang]').val());
        var url = $(this).data('url');
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (data) {
                data = jQuery.parseJSON(data);
                var html = '';
                $.each(data, function (i, val) {
                    if (i == 'html') {
                        html = val;
                    }
                });
                $('.pdf_icon_accessoires').html(html);
                $('.pdf_accessoires').val('');
                $('#bodyoverlay').fadeOut();
            }
        });
    });
    /**
     * Send additional Elements
     */
    $(document).on('click', '.sendadditionalelement', function (e) {
        e.preventDefault();
        var countLang = 0;
        $('select.language option').each(function (i, val) {
            countLang++;
        });
        var form_data = new FormData();
        var arr_sku = new Object();
        $.each($('.new-additional-element-sku'), function (i, val) {
            var sku = $(this).val();
            if (sku != '') {
                arr_sku[i] = sku;
                form_data.append('sku[]', $(this).val());
            } else {
                alert('Bitte Sku ausfüllen');
                arr_sku = new Object();
                return false;
            }
        });
        //$.each(arr_sku, function (isku, valsku) {
        var isku = 0;
        $.each($('.new-additional-element-descriptionname'), function (i, val) {
            if (i > 0 && (i % countLang) == 0) {
                isku++;
            }
            var lang_id = $(this).data('lang-id');
            var description = $(this).val();
            if (description != '') {
                form_data.append('sendadditionaldescription[' + arr_sku[isku] + '][' + lang_id + ']', description);
            } else {
                alert('Bitte alle Beschreibungsfelder ausfüllen');
                return false;
            }
        });
        //});
        $.each($('.new-additional-element-price'), function (i, val) {

            var price = $(this).val();
            if (price != '') {
                form_data.append('price[' + arr_sku[i] + ']', price);
            } else {
                alert('Bitte Preis ausfüllen');
                return false;
            }
        });
        $.each($('.new-additional-element-image'), function (i, val) {
            var file_data = $(this).prop('files')[0];
            if (file_data != undefined) {
                form_data.append('sendadditionalimages[' + arr_sku[i] + '][]', file_data);
            }

        });
        form_data.append('product_id', $('input[name=product_id]').val());
        form_data.append('lang', $('input[name=lang]').val());
        var url = $(this).data('urlelement');
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'html', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            //async: false,
            success: function (data) {
                data = jQuery.parseJSON(data);
                var html = '';
                var isOk = true;
                $.each(data, function (i, val) {
                    if (i == 'html') {
                        html = val;
                    }
                    if (i == 'error') {
                        alert(val);
                        isOk = false;
                    }
                });
                if (!isOk) {
                    $('#bodyoverlay').fadeOut();
                    return false;
                }
                var obj;
                $('.additionalelement').each(function () {
                    obj = $(this);
                });
                obj.after(html);
                $('.additionalelement').each(function () {
                    obj = $(this);
                });
                var oObj;
                obj = obj.next();
                while (!obj.hasClass('admin-rowsenadditionalelement')) {
                    oObj = obj.next();
                    obj.remove();
                    obj = oObj;
                }
                obj.hide();
                $('#bodyoverlay').fadeOut();
            }
        });
    });
    /**
     * Remove additional Elements
     */
    $(document).on('click', '.additional-remove', function (e) {
        e.preventDefault();
        var product_id = $('input[name=product_id]').val();
        var children_id = $(this).data('additional-remove');
        var lang = $('input[name=lang]').val();
        var answer = confirm('Wollen Sie das Element löschen?');
        var url = $('.sendadditionalelement').data('urlelementremove');
        var obj = $(this);
        if (answer) {
            var form_data = new FormData();
            form_data.append('product_id', product_id);
            form_data.append('children_id', children_id);
            form_data.append('lang', lang);
            $('#bodyoverlay').fadeIn();
            $.ajax({
                url: url, // point to server-side PHP script
                dataType: 'text', // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (data) {
                    obj.parent().parent().next().remove();
                    obj.parent().parent().next().remove();
                    obj.parent().parent().remove();
                    $('#bodyoverlay').fadeOut();
                }
            });
        }
    });
    // set image default
    $(document).on('click', '.image-default', function (e) {
        e.preventDefault();
        var obj = $(this);
        var product_id = $('input[name=product_id]').val();
        var image_id = $(this).data('image-id');
        var url = $('.mutationimage').data('url-image-default');
        var form_data = new FormData();
        form_data.append('product_id', product_id);
        form_data.append('image_id', image_id);
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (data) {
                $('#bodyoverlay').fadeOut();
                $('.image-default').each(function (i, val) {
                    $(this).prop('checked', false);
                });
                obj.prop('checked', true);
            }
        });
    });
    // Mutation image
    $(document).on('click', '.mutationimage', function (e) {
        e.preventDefault();
        var form_data = new FormData();
        var isAllowed = false;
        var url = $('.mutationimage').data('url-image-update');
        var product_id = $('input[name=product_id]').val();
        var lang = $('input[name=lang]').val();
        form_data.append('lang', lang);
        form_data.append('product_id', product_id);
        $.each($('.productimages .image'), function (i, val) {
            var file_data = $(this).prop('files')[0];
            if (file_data != undefined) {
                var image_id = $(this).data('image-id');
                form_data.append('sendimages[' + image_id + ']', file_data);
                isAllowed = true;
            }
        });
        if (!isAllowed) {
            return false;
        }
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (data) {
                data = jQuery.parseJSON(data);
                var arr_html = new Object();
                $.each(data, function (i, val) {
                    if (i == 'arr_html') {
                        arr_html = val;
                    }
                });
                $('.productimages .image-remove').each(function (i, val) {
                    var current_imageid = $(this).data('image-remove');
                    var current_obj = $(this);
                    $.each(arr_html, function (i_image, html) {
                        if (current_imageid == i_image) {
                            $(current_obj).parent().parent().replaceWith(html);
                        }
                    });
                });
                $('#bodyoverlay').fadeOut();
            }
        });
    });
    // Mutation element
    $(document).on('click', '.mutationelement', function (e) {
        e.preventDefault();
        var form_data = new FormData();
        var isAllowed = false;
        var child_id = 0;
        $('.additionalelement').each(function (i, val) {
            $.each($(this).find('.form-control'), function () {
                if ($(this).attr('data-child-id')) {
                    child_id = $(this).data('child-id');
                }

                if ($(this).attr('data-element-description-id')) {
                    form_data.append('productdescription[' + child_id + '][' + $(this).data('element-description-id') + ']', $(this).val());
                }

                if ($(this).attr('data-element-price-id')) {
                    form_data.append('productprice[' + child_id + '][' + $(this).data('element-price-id') + ']', $(this).val());
                }
            });
            if ($(this).find('.image').length) {
                var image_id = $(this).find('img').data('element-image-id');
                var file_data = $(this).find('.image').prop('files')[0];
                if (file_data != undefined) {
                    form_data.append('sendimages[' + child_id + '][' + image_id + ']', file_data);
                }
            }

        });
        var product_id = $('input[name=product_id]').val();
        var lang = $('input[name=lang]').val();
        var url = $(this).data('url-element-update');
        form_data.append('product_id', product_id);
        form_data.append('lang', lang);
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (data) {
                data = jQuery.parseJSON(data);
                var objPrev = $('.additionalelement').prev();
                $.each($('.additionalelement'), function () {
                    $(this).remove();
                });
                objPrev.after(data.html);
                $('#bodyoverlay').fadeOut();
            }
        });
    });

    var import_url_file = '';
    var import_url_nofile = '';
    $(document).on('click', '.admin-import', function (e) {
        e.preventDefault();
        import_url_file = $(this).data('import-url-file');
        import_url_nofile = $(this).data('import-url-nofile');
        $('#bodyoverlay').fadeIn();
        var windowWidth = $(window).width();
        var windowHeight = $(window).height();
        var dialogBoxWidth = $('#bodyoverlay-dialogbox').width();
        var dialogBoxHeight = $('#bodyoverlay-dialogbox').height();
        var winLeft = (windowWidth / 2) - (dialogBoxWidth / 2);
        var winTop = (windowHeight / 2) - (dialogBoxHeight / 2);
        $('#bodyoverlay-dialogbox').css({'left': winLeft + 'px', 'top': winTop + 'px'});
    });

    $(document).on('click', '.import-ja', function (e) {
        e.preventDefault();
        window.location.href = import_url_file;
        $('#bodyoverlay-dialogbox').hide();
    });

    $(document).on('click', '.import-nein', function (e) {
        e.preventDefault();
        window.location.href = import_url_nofile;
        $('#bodyoverlay-dialogbox').hide();
    });

    $(document).on('click', '.dialogbox-close', function (e) {
        e.preventDefault();
        $('#bodyoverlay').hide();
    });

    $(document).on('click', '.admin-upload-new', function (e) {
        e.preventDefault();
        window.location.href = $(this).data('new-url');
    });

    $(document).on('click', '.admin-import-start', function (e) {
        e.preventDefault();
        var url = '';
        $('.list-import input').each(function (i, val) {
            if ($(this).prop('checked')) {
                url = $(this).data('import-url');
            }
        });
        if (url != '') {
            window.location.href = url;
        }
    });

    $(document).on('click', '.admin-import-delete', function (e) {
        e.preventDefault();
        var url = $(this).data('delete-url');
        var isDelete = confirm($(this).data('delete-text'));
        if (isDelete) {
            window.location.href = url;
        }
    });

    $(document).on('change', '.label-language', function (e) {
        e.preventDefault();
        var url = $(this).data('url') + $(this).val() + '/';
        window.location.href = url;
    });

    // inbox new
    $(document).on('click', '.new-emails-button', function (e) {
        e.preventDefault();
        $('.email-response-box-new').show();
        $('.email-list').hide();
    });

    // inbox cancel
    $(document).on('click', '.new-message-cancel', function (e) {
        e.preventDefault();
        $('.email-response-box-new').hide();
        $('.email-list').show();
    });

    // inbox delete
    $(document).on('click', '.delete-emails-button', function (e) {
        e.preventDefault();
        var isChecked = false;

        $('.inbox-delete').each(function () {
            if ($(this).is(':checked')) {
                isChecked = true;
            }
        });

        if (isChecked) {
            var answer = confirm($(this).data('delete-message'));
            if (answer) {
                $('#delete-in-box').submit();
            }
        }
    });

    $(document).on('click', '.resp-emails-antworten', function (e) {
        e.preventDefault();
        $('.email-response-box').show();
        $('.email-text-box').hide();
    });

    $(document).on('click', '.message-cancel', function (e) {
        e.preventDefault();
        $('.email-response-box').hide();
        $('.email-text-box').show();
    });

    $(document).on('click', '.message-send, .new-message-send', function (e) {
        e.preventDefault();
        $('#email-response').submit();
    });

    $(document).on('click', '.send-order', function (e) {
        e.preventDefault();
        location.href = $(this).data('url');
    });

    // Product Delete
    $(document).on('click', '.delete-product', function (e) {
        e.preventDefault();
        location.href = $(this).data('url');
    });

    function getAdditionalHtml() {
        var html = '<div class="row">';
        html += '<div class="col-md-11" style="padding-bottom:10px;"><input type="file" class="new-additional-element-image" name="additional[insert][additionalelement][file][]" /></div>';
        html += '<div class="col-md-1" style="padding-bottom:10px;"><a href="#"><span class="glyphicon glyphicon-remove remove-input-element admin-element-right" aria-hidden="true"></span></a></div>';
        html += '</div>';
        html += '<div class="row">';
        html += '<div class="col-md-9">Sku</div>';
        html += '<div class="col-md-3"><input class="form-control new-additional-element-sku" name="additional[insert][additionalelement][sku][]" type="text"></div>';
        html += '</div>';
        html += '<div class="row">';
        html += '<div class="col-md-9">Price per day</div>';
        html += '<div class="col-md-3"><input data-price-calendar-typ-id="" class="form-control new-additional-element-price" name="additional[insert][additionalelement][price][]"  type="text"></div>';
        html += '</div>';
        $('select.language option').each(function (i, val) {
            html += '<div class="row">';
            html += '<div class="col-md-9">' + $(this).data('lang-name') + '</div>';
            html += '<div class="col-md-3"><input data-lang-id="' + $(this).data('lang-id') + '" class="form-control new-additional-element-descriptionname" name="additional[insert][additionalelement][descriptionname][]" type="text"></div>';
            html += '</div>';
        });
        return html;
    }


});

