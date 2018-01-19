/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(function () {

    $(document).on('click', '.calendar-dates .deliver', function (e) {
        e.preventDefault();
        // if deliver set to false
        $(this).removeClass('deliver').addClass('nodeliver');
        updateDate($(this).attr('id'), 0);
    });

    $(document).on('click', '.calendar-dates .nodeliver', function (e) {
        e.preventDefault();
        // if no deliver set to true
        $(this).removeClass('nodeliver').addClass('deliver');
        updateDate($(this).attr('id'), 1);
    });


    function updateDate(datum, isdeliver) {
        var url = $('#admin-deliver-update').data('url') + datum + '/' + isdeliver + '/';
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            type: 'get',
            success: function (data) {
                $('#bodyoverlay').fadeOut();
            }
        });
    }

});

