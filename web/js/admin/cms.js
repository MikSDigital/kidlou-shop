/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(function () {

    var rootObj;
    var rootTyp;
    var catTyp;
    $('span.ausklappen').next().hide();
    $("span.ausklappen").before("<span>+ </span>");
    $("span.ausklappen").css("cursor", "pointer");
    $(document).on('click', 'span.ausklappen', function (e) {
        e.preventDefault();
        $(this).next().slideToggle("slow");
        if ($(this).prev(this).text() == "+ ")
            $(this).prev(this).replaceWith("<span>- </span>");
        else if ($(this).prev(this).text() == "- ")
            $(this).prev(this).replaceWith("<span>+ </span>");
    });


    $(document).on('click', 'ul.admin-navigation li a', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            /*            data: form_data, */
            type: 'get',
            success: function (data) {
                data = jQuery.parseJSON(data);
                $('.cms-content').html(data.html);
                $('#bodyoverlay').fadeOut();
            }
        });
    });

    $(document).on('click', '.admin-add-content', function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            /*            data: form_data, */
            type: 'get',
            success: function (data) {
                data = jQuery.parseJSON(data);
                $('.cms-content').html(data.html);
                $('.admin-save-content').show();
                $('.admin-save-content-lang').show();
                $('#bodyoverlay').fadeOut();
            }
        });
    });
    $(document).on('click', '.admin-save-content', function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var catid = $(this).data('catid');
        var langid = $('.admin-save-content-lang').val();
        var form_data = new FormData();
        $('.admin-content').find('[data-field]').each(function () {
            var field = $(this).data('field');
            var id = $(this).data('id');
            if ($(this).is("textarea")) {
                var content = tinyMCE.get($(this).attr('id')).getContent();
            } else if ($(this).is(":checkbox")) {
                var content = $(this).prop("checked") ? 1 : 0;
            } else if ($(this).prop('disabled')) {
                var content = $(this).data('value');
            } else if ($(this).is(":file")) {
                var file = $(this).prop('files')[0];
            } else {
                var content = $(this).val();
            }
            form_data.append('langid', langid);
            form_data.append('catid', catid);
            if (field != 'image') {
                form_data.append('content[' + id + '][' + field + ']', content);
            }
            if (file != undefined) {
                form_data.append('image[' + id + '][' + field + ']', file);
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
                $('.cms-content').html(data.html);
                $('#bodyoverlay').fadeOut();
            }
        });
    });

    $(document).on('click', '.delete-content', function (e) {
        e.preventDefault();
        var res = confirm($(this).data('message'));
        if (!res) {
            return false;
        }
        var url = $(this).data('url');
        var form_data = new FormData();
        form_data.append('id', $(this).data('id'));
        form_data.append('catid', $(this).data('catid'));
        form_data.append('langid', $(this).data('lang'));
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
                $('.cms-content').html(data.html);
                $('#bodyoverlay').fadeOut();
            }
        });
    });

    $(document).on('click', '.delete-content-image', function (e) {
        e.preventDefault();
        var res = confirm($(this).data('message'));
        if (!res) {
            return false;
        }
        var url = $(this).data('url');
        var form_data = new FormData();
        form_data.append('id', $(this).data('id'));
        form_data.append('catid', $(this).data('catid'));
        form_data.append('langid', $(this).data('lang'));
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
                $('.cms-content').html(data.html);
                $('#bodyoverlay').fadeOut();
            }
        });


    });


    $(document).on('change', '.admin-save-content-lang', function (e) {
        e.preventDefault();
        var lang = $(this).val();
        var url = $(this).data('url') + lang + '/';
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            /*            data: form_data, */
            type: 'get',
            success: function (data) {
                data = jQuery.parseJSON(data);
                $('.cms-content').html(data.html);
                $('#bodyoverlay').fadeOut();
            }
        });
    });
    $(document).on('click', '.admin-save-site', function (e) {
        e.preventDefault();

        var cat_typ = $('.select-category-typ').val();
        if (cat_typ == 0) {
            alert($(this).data('message-nok'));
            return false;
        }

        // prüfe of url key anders ist als current und wenn ja danach prüfe os sonst noc vorhanden
        var url_key = jQuery.trim($('.url_key').val());
        var arr_url_key = $('.url_key').data('url-keys');
        var url_key_current = $('.url_key').data('url-key-current');

        var isUrl = false;
        if (url_key != url_key_current) {
            $.each(arr_url_key, function (i, val) {
                if (url_key == val) {
                    alert($('.url_key').data('message-nok'));
                    isUrl = true;
                }
            });
        }
        if (isUrl) {
            $('.url_key').val(url_key_current);
            return false;
        }

        var is_active = 0;
        if ($('.is_active').prop('checked')) {
            is_active = 1;
        } else {
            is_active = 0;
        }
        var form_data = new FormData();
        form_data.append('id', $(this).data('catid'));
        form_data.append('cat_typ_id', cat_typ);
        form_data.append('is_active', is_active);
        form_data.append('url_key', url_key);
        $('.category-label').each(function (i, val) {
            form_data.append('labels[' + $(this).data('labelid') + ']', $(this).val());
        });

        var file_data = $('.image').prop('files')[0];
        if (file_data != undefined) {
            form_data.append('image', file_data);
        }

        var delete_image = 0;
        if ($('.delete_image').prop('checked')) {
            delete_image = 1;
        }
        form_data.append('delete_image', delete_image);

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
                $('.cms-content').html(data.html);
                $('.admin-navigation li').each(function () {
                    if ($(this).data('catid') == data.catlabel.id) {
                        $(this).children().html(data.catlabel.name);
                    }
                });
                $('#bodyoverlay').fadeOut();
            }
        });
    });
    $(document).on('change', 'select.select-category-typ', function (e) {
        catTyp = $(this).val();
    });
    $(document).on('click', 'button.button-category-continue', function (e) {
        if (catTyp == undefined) {
            catTyp = 1;
        }

        $('#bodyoverlay-box').removeAttr('style').html('');
        $('#bodyoverlay').hide();
        createEventContextMenu(rootTyp, rootObj);
    });
    $.contextMenu({
        selector: 'ul.admin-navigation li, div.admin-navigation-root',
        callback: function (key, options) {
            // category
            if (isRootCategoryTyp(key, $(this))) {
                rootObj = $(this);
                rootTyp = key;
                getSelectCategoryTyp($(this));
            } else {
                createEventContextMenu(key, $(this));
            }

        },
        items: {
            "add": {name: "Add", icon: "add"},
            "edit": {name: "Edit", icon: "edit"},
//            "cut": {name: "Cut", icon: "cut"},
//            "copy": {name: "Copy", icon: "copy"},
//            "paste": {name: "Paste", icon: "paste"},
            "delete": {name: "Delete", icon: "delete"},
            "sep1": "---------",
            "quit": {name: "Quit", icon: function () {
                    return 'context-menu-icon context-menu-icon-quit';
                }}
        }
    });
    $(".sortable").sortable({
        update: function (event, ui) {
            var objParent = ui.item.parent();
//            if (objParent.is('ul')) {
//                var parent_catid = objParent.parent().data('catid');
//                if (!parent_catid) {
//                    parent_catid = 1;
//                }
//            }
            var form_data = new FormData();
            objParent.children('li').each(function () {
                form_data.append('catids[]', $(this).data('catid'));
            });
            var url = $('.admin-navigation').data('url-order-menu');
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
        }
    });

    function isRootCategoryTyp(typ, obj) {
        if (typ == 'add') {
            if (obj.data('level') == 0 && obj.data('catid') == 1) {
                return true;
            }
        }
        return false;
    }


    function getSelectCategoryTyp(obj) {
        $('#bodyoverlay').fadeIn();
        $('#bodyoverlay-box').css({'width': '20%', 'height': '150px', 'padding': '10px', 'display': 'block'});
        $('#bodyoverlay-box').html($('.select-category-typ').html());
    }


    function createEventContextMenu(typ, obj) {
        if (typ == 'add') {
            var name = prompt("Please enter name : ");
            if (name == '') {
                alert('Please enter name!');
                return false;
            } else if (name === null) {
                return false;
            }
            sendAddNewSite(obj, name);
        } else if (typ == 'edit') {
//            var name = prompt("Please change name : ");
//            if (name == '') {
//                alert('Please enter name!');
//                return false;
//            } else if (name === null) {
//                return false;
//            }
            sendEditSite(obj, name);
        } else if (typ == 'delete') {
            sendDeleteSite(obj);
        }
    }


    function sendAddNewSite(obj, name) {
        var form_data = new FormData();
        form_data.append('parent_level', obj.data('level'));
        form_data.append('parent_catid', obj.data('catid'));
        if (catTyp != undefined) {
            form_data.append('parent_cattyp', catTyp);
        } else {
            form_data.append('parent_cattyp', obj.data('cattyp'));
        }
        form_data.append('new_catname', name);
        var url = $('.admin-navigation').data('url-add-new-site');
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
                // prüfe ob es bereits untermenu gibt
                if (obj.find('ul').length > 0) {
                    var html = getHtmlSameLevel(data);
                    var objLi;
                    obj.find('ul li').each(function () {
                        objLi = $(this);
                    });
                    objLi.after(html);
                } else {
                    // create new menu
                    var html = getHtmlNewLevel(obj, data);
                    obj.replaceWith(html);
                }
                $('#bodyoverlay').fadeOut();
            }
        });
    }


    function getHtmlSameLevel(response) {
        var html = '<li data-level="' + response.level + '" data-catid="' + response.catid + '" data-cattyp="' + response.cattyp + '">';
        html += '<a href="' + response.url_key + '">' + response.name + '</a>';
        html += '</li>';
        return html;
    }



    function getHtmlNewLevel(obj, response) {
        var html = '<li class="admin-navi-status" data-level="' + obj.data('level') + '" data-catid="' + obj.data('catid') + '" data-cattyp="' + response.cattyp + '"><span class="admin-navi-status">- </span>';
        html += '<span class="ausklappen" style="cursor: pointer;" data-url="' + response.url_key + '">' + obj.text() + '</span>';
        html += '<ul class="sortable" style = "display: block;" >';
        html += '<li data-level="' + response.level + '" data-catid="' + response.catid + '"  data-cattyp="' + response.cattyp + '" class="admin-navi-status">';
        html += '<a href="' + response.url_key + '">' + response.name + '</a>';
        html += '</li>';
        html += '</ul>';
        html += '</li>';
        return html;
    }


    function sendEditSite(obj, name) {

        if (obj.children().attr('href') === undefined) {
            var url = obj.children().next().data('url');
        } else {
            var url = obj.children().attr('href');
        }

        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            /*            data: form_data, */
            type: 'get',
            success: function (data) {
                data = jQuery.parseJSON(data);
                $('.cms-content').html(data.html);
                $('#bodyoverlay').fadeOut();
            }
        });


//        var form_data = new FormData();
//        form_data.append('catid', obj.data('catid'));
//        form_data.append('catname', name);
//        var url = $('.admin-navigation').data('url-edit-site');
//        $('#bodyoverlay').fadeIn();
//        $.ajax({
//            url: url, // point to server-side PHP script
//            dataType: 'text', // what to expect back from the PHP script, if anything
//            cache: false,
//            contentType: false,
//            processData: false,
//            data: form_data,
//            type: 'post',
//            success: function (data) {
//                data = jQuery.parseJSON(data);
//                // prüfe ob es bereits untermenu gibt
//                if (obj.find('ul').length > 0) {
//                    obj.find('ul').prev().html(data.name);
//                } else {
//                    obj.children().html(data.name);
//                    obj.children().attr('href', data.url_key);
//                }
//                $('#bodyoverlay').fadeOut();
//            }
//        });
    }


    function sendDeleteSite(obj) {
        var form_data = new FormData();
        form_data.append('catid', obj.data('catid'));
        var url = $('.admin-navigation').data('url-delete-site');
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
                // prüfe ob es bereits untermenu gibt
                if (obj.parent().is('ul') && obj.parent().prev().is('span')) {
                    if (obj.prev().is('li')) {
                        obj.remove();
                    } else {
                        var text = obj.parent().prev().text();
                        var url = obj.parent().prev().data('url');
                        obj.parent().parent().html('<a href="' + url + '">' + text + '</a>');
                    }
                } else {
                    obj.remove();
                }
                $('#bodyoverlay').fadeOut();
            }
        });
    }

});

