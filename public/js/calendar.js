$(document).ready(function () {
    var days = 0;
    var arr_additionals = new Object();
    var typ_date = '';


    $.each($("input[name='additionalproduct[]']"), function (i, id) {
        if ($(this).is(':checked')) {
            arr_additionals[$(this).val()] = $(this).val();
        }
    });


    $(document).on('click', '.calendar-url-next-before', function (e) {
        e.preventDefault();
        var calendarurl = $(this).attr('href');
        var leftval = $('.calendar-dates').css('left');
        var date = '';
        var date_from_to = '';
        if (typ_date != 'date-to') {
            date = splitDateInput($('#date-from').val());
            date_from_to = splitDateInput($('#date-to').val());
        } else {
            date = splitDateInput($('#date-to').val());
            date_from_to = splitDateInput($('#date-from').val());
        }
        getCalendarUrl(calendarurl, leftval, date, date_from_to, typ_date);
    });

    $(document).on('keypress', '#date-from', function (e) {
        return false;
    });

    $(document).on('click', '#date-from', function (e) {
        e.preventDefault();
        if ($('.calendar-result').length > 0) {
            $('.calendar-result').hide();
        }

        typ_date = $(this).attr('id');
        // if date-from is not empty, wieder zurück zum alten stand
        if ($(this).val() != '') {
            $('.calendar-dates').fadeIn();
            // setzte left auf 0
            $('.calendar-dates').css({'left': '0'});
            // wert von input feld einlesen
            var arr_date = $(this).val().split('.');

            var product_id = $('.product-grid-detail').data('product-id');
            // standard url von von calendar einlesen, ohne monat und jahr wird neu gesetzt von input field
            var calendarurl_index = $('.calendar-dates .calendar-kidlou-table').data('calendar-url') + arr_date[2] + '/' + arr_date[1] + '/' + product_id + '/';

            var date_to = splitDateInput($('#date-to').val());
            var date = splitDateInput($(this).val());
            // url wird aufgerufen, hier muss noche beachtet werden das das die gleichen einstellungen wie am anfag sind. werd von input field und
            getCalendarUrl(calendarurl_index, 0, date, date_to, typ_date);
        } else {
            if ($('.calendar-dates').length > 0) {
                $('.calendar-dates').hide();
            }
            $('.calendar-dates').fadeIn();
            $('.calendar-dates').css({'left': '0'});
        }
    });

    $(document).on('keypress', '#date-to', function (e) {
        return false;
    });


    $(document).on('click', '#date-to', function (e) {
        e.preventDefault();
        if ($('.calendar-result').length > 0) {
            $('.calendar-result').hide();
        }

        typ_date = $(this).attr('id');

        if ($("#date-from").val() == '') {
            $("#date-from").trigger("click");
            return;
        }
        var date_from = splitDateInput($('#date-from').val());
        if ($(this).val() != '') {
            $('.calendar-dates').fadeIn();
            // wert von input feld einlesen
            var leftval = $('#date-to').offset().left - $('#date-from').offset().left;
            if ($(document).width() < 500) {
                leftval = 0;
            }
            $('.calendar-dates').css({'left': leftval});
            var arr_date = $(this).val().split('.');
            var product_id = $('.product-grid-detail').data('product-id');
            // standard url von von calendar einlesen, ohne monat und jahr wird neu gesetzt von input field
            var calendarurl_index = $('.calendar-dates .calendar-kidlou-table').data('calendar-url') + arr_date[2] + '/' + arr_date[1] + '/' + product_id + '/';
            //var date = arr_date[2] + '-' + arr_date[1] + '-' + arr_date[0];
            var date = splitDateInput($(this).val());
            getCalendarUrl(calendarurl_index, leftval, date, date_from, typ_date);

        } else {
            if ($('.calendar-dates').length > 0) {
                $('.calendar-dates').hide();
            }
            var leftval = $('#date-to').offset().left - $('#date-from').offset().left;
            if ($(document).width() < 500) {
                leftval = 0;
            }
            $('.calendar-dates').fadeIn();
            $('.calendar-dates').css({'left': leftval + 'px'});
        }


    });

    $(document).on('click', '.calendar-dates .deliver', function (e) {
        e.preventDefault();

        if ($(this).hasClass('calendar-reserved-from')) {
            return;
        }
        $('.calendar-dates .calendar-kidlou-table tbody tr td.deliver').removeClass('calendar-reserved');
        $(this).addClass('calendar-reserved');
        var url = window.location.pathname;
        if (typ_date == 'date-from') {
            if (getUrlParameter('additionals') !== undefined) {
                url += '?additionals=' + getUrlParameter('additionals') + '&';
            } else {
                url += '?';
            }
            url += 'date_from=' + $(this).attr('id');
            if ($('#date-to').val() != '') {
                // check if date_to is smaller than date_from if yes delete date
                var date_from = getCalendarDate($(this).attr('id'), '-');
                var date_to = getCalendarDate($('#date-to').val(), '.');
                if (date_from.getTime() < date_to.getTime()) {
                    url += '&date_to=' + splitDateInput($('#date-to').val());
                } else {
                    $('#date-to').val('');
                }
            }
        } else {
            var date_from = getCalendarDate($('#date-from').val(), '.');
            var date_to = getCalendarDate($(this).attr('id'), '-');
            if (date_from.getTime() === date_to.getTime()) {
                return false;
            }
            if (getUrlParameter('additionals') !== undefined) {
                url += '?additionals=' + getUrlParameter('additionals') + '&';
            } else {
                url += '?';
            }
            url += 'date_from=' + splitDateInput($('#date-from').val());
            url += '&date_to=' + $(this).attr('id');
        }
        history.pushState(null, null, url);
        var date = splitDate($(this).attr('id'));
        $('#' + typ_date).val(date);

        var from_day = 0;
        if (typ_date == 'date-from') {
            from_day = splitDateDay($(this).attr('id'));
        }

        $('.calendar-dates .calendar-kidlou-table tbody tr td.deliver').each(function (i, val) {
            var day = $(this).attr('id');
            if (parseInt(from_day) > parseInt(splitDateDay(day))) {
                $(this).addClass('calendar-reserved-from');
            }
        });

        if (typ_date != 'date-to') {
            $("#date-to").trigger("click");
        } else {
            $('.calendar-dates').hide();
            // calculate days
            // send request for price calculation
            getCalculateDates();
        }

    });

    // calendar schliessen ausserhalb calendar
    $(document).click(function (e) {
        if (e.target.id != $('#date-from').attr('id') && e.target.id != $('#date-to').attr('id') && $(e.target).closest('.calendar-dates').length == 0) {
            $('.calendar-dates').hide();
        }
    });


    $(document).on('mouseover', 'table.calendar-kidlou-table > tbody > tr > td.calendar-free', function (e) {
        e.preventDefault();
        days = $(this).parent().parent().parent().data('calendardays');
        for (i = 0; i < days + 1; i++) {
            if ($(this).parent().children().eq(i).hasClass('calendar-free')) {
                $(this).parent().children().eq(i).addClass('calendar-move');
            }
        }
    });

    $(document).on('mouseout', 'table.calendar-kidlou-table > tbody > tr > td.calendar-move', function (e) {
        e.preventDefault();
        days = $(this).parent().parent().parent().data('calendardays');
        for (i = 0; i < days + 1; i++) {
            if ($(this).parent().children().eq(i).hasClass('calendar-move')) {
                $(this).parent().children().eq(i).removeClass('calendar-move');
            }
        }
    });

    $(document).on('click', '.product-grid-detail .box-border-gray', function (e) {
        if ($(this).children().eq(0).is(':checked')) {
            $(this).children().eq(0).prop('checked', false);
            delete arr_additionals[$(this).children().eq(0).val()];
        } else {
            $(this).children().eq(0).prop('checked', true);
            arr_additionals[$(this).children().eq(0).val()] = $(this).children().eq(0).val();
        }

        if (Object.keys(arr_additionals).length > 0) {
            var url = window.location.pathname;
            if (getUrlParameter('date_from') !== undefined && getUrlParameter('date_to') !== undefined) {
                url += '?date_from=' + getUrlParameter('date_from') + '&date_to=' + getUrlParameter('date_to') + '&';
            } else {
                url += '?';
            }
            var param_additionals = '';
            $.each(arr_additionals, function (i, val) {
                if (param_additionals != '') {
                    param_additionals += ',' + val;
                } else {
                    param_additionals = val;
                }
            });
            url += 'additionals=' + param_additionals;
            history.pushState(null, null, url);
        }

        if ($('#date-from').val() != '' && $('#date-from').val() != '') {
            getCalculateDates();
        }

    });

    $(document).on('click', "input[name='additionalproduct[]']", function (e) {
        if ($(this).is(':checked')) {
            $(this).prop('checked', false);
            delete arr_additionals[$(this).val()];
        } else {
            $(this).prop('checked', true);
            arr_additionals[$(this).val()] = $(this).val();
        }

        if (Object.keys(arr_additionals).length > 0) {
            var url = window.location.pathname;
            if (getUrlParameter('date_from') !== undefined && getUrlParameter('date_to') !== undefined) {
                url += '?date_from=' + getUrlParameter('date_from') + '&date_to=' + getUrlParameter('date_to') + '&';
            } else {
                url += '?';
            }
            var param_additionals = '';
            $.each(arr_additionals, function (i, val) {
                if (param_additionals != '') {
                    param_additionals += ',' + val;
                } else {
                    param_additionals = val;
                }
            });
            url += 'additionals=' + param_additionals;
            history.pushState(null, null, url);
        }

        if ($('#date-from').val() != '' && $('#date-from').val() != '') {
            getCalculateDates();
        }

    });

    $(document).on('click', ".box-button", function (e) {
        window.location.href = $(this).data('url');
    });


    var height = 0;
    $(document).on('click', '.request-resevierung .btn-cart', function (e) {
        e.preventDefault();
        // get form url
        var url = $('.kidlou-calendar').data('carturl');
        var dates = new Object();
        dates['date_from'] = $('#date-from').val();
        dates['date_to'] = $('#date-to').val();
        if (dates['date_from'] == '' && dates['date_to'] == '') {
            return false;
        }

        $('#bodyoverlay').fadeIn();
        $.ajax({
            type: 'POST',
            url: url,
            data: {dates: JSON.stringify(dates), additionals: JSON.stringify(arr_additionals)},
            success: function (data) {
                var html = '';
                $.each(data, function (i, val) {
                    html = val;
                });

                $('#bodyoverlay-box').html(html);
                $('#bodyoverlay-box').css({'display': 'block'});
                // grosses bild ist 80
                height = 80;
                // kleine bilder sind 50
                height = height + (50 * Object.keys(arr_additionals).length);
                //
                $('.box-image .box-title').each(function () {
                    height = height + $(this).height();
                });

                height = height + $('.box-dates').height();
                height = height + $('.box-button').height();
                height = height + 50;
                height = height + 'px';
                $('#bodyoverlay-box').css({'height': height, 'width': '250px', 'padding': '10px'});

            },
            failed: function (data) {
                alert('failed');
            }

        });

    });

    function getCalculateDates() {
        var product_id = $('.product-grid-detail').data('product-id');
        var url = $('.calendar').data('count-days');
        var dates = new Object();
        dates['date_from'] = $('#date-from').val();
        dates['date_to'] = $('#date-to').val();
        $('#bodyoverlay').fadeIn();
        $.ajax({
            url: url,
            data: {dates: JSON.stringify(dates), product: product_id, additionals: JSON.stringify(arr_additionals)},
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                $('.calendar-result').html(data);
                $('.calendar-result').show();
                $('#bodyoverlay').fadeOut();
            }
        });
    }

});


function getCheckNumber(number) {
    if (number < 10) {
        return '0' + number;
    }
    return number;
}

function getCalendarUrl(url, leftval, date, date_from_to, typ_date) {
    showAjaxLoader();
    $.get(url, function (data) {
        var html = jQuery.parseHTML(data);
        $('.calendar-dates-show').html(html);
        $('.calendar-dates').css({'display': 'block', 'left': leftval});
        hideAjaxLoader();
        if ($('#' + date).length > 0) {
            $('#' + date).addClass('calendar-reserved');
        }

        if (date_from_to != '') {
            if ($('#' + date_from_to).length > 0) {
                $('#' + date_from_to).addClass('calendar-reserved');
            }
        }
        setReservedFrom(typ_date, date_from_to);
    });
}



function setReservedFrom(typ_date, date_from_to) {
    if (typ_date == 'date-to') {
        if ($('#date-from').val() != '') {
            var from_date = $('#date-from').val();
            from_date = getCalendarDate(from_date, '.');
            $('.calendar-dates .calendar-kidlou-table tbody tr td.deliver').each(function (i, val) {
                var day = $(this).attr('id');
                if (from_date.getTime() > getCalendarDate(day, '-').getTime()) {
                    $(this).addClass('calendar-reserved-from');
                }
            });
        }
    }
}


function sendCalendarReservation(url) {
    //showAjaxLoader();
    $.get(url, function (data) {
        //hideAjaxLoader();
    });
}

function showAjaxLoader() {
    $('#bodyoverlay-calendar').fadeIn();
    //$('td.calendar-title img').show();
}

function hideAjaxLoader() {
    $('#bodyoverlay-calendar').fadeOut();
    //$('td.calendar-title img').hide();
}

function splitDate(date) {
    date = date.split('-');
    return date[2] + '.' + date[1] + '.' + date[0];
}

function getCalendarDate(date, splittyp) {
    var date = date.split(splittyp);
    if (splittyp == '-') {
        return new Date(date[0], date[1], date[2]);
    } else {
        return new Date(date[2], date[1], date[0]);
    }
}


function splitDateDay(date) {
    date = date.split('-');
    return date[2];
}

function splitDateInput(date) {
    date = date.split('.');
    return date[2] + '-' + date[1] + '-' + date[0];
}


var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};
