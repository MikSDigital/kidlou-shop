$(document).ready(function () {

    $('.reserved-order-test').click(function () {
        $('#bodyoverlay').fadeIn();
        var message = $('#bodyoverlay').data('iframe-message');
        $('#overlay-iframe').contents().find('body').html('<div style="text-align:center;">' + message + '</div>');
        $('#overlay-iframe').css({'width': '80%', 'height': '60%', 'margin-left': 'auto', 'margin-right': 'auto', 'text-align': 'center', 'padding-top': '20px'});
        $('#bodyoverlay').css({'background-image': 'none'});
        $('.send-order').submit();
    });

    var css = '<link href="/css/main.css" rel="stylesheet" />';
    $('#overlay-iframe').load(function () {
        var overlay_iframe = $('#overlay-iframe').contents();
        overlay_iframe.find("head").append(css);
        overlay_iframe.find('.order-success').click(function () {
            setLocation($(this).data('url'));
        });
    });

});


function setLocation(href) {
    window.location.href = href;
}
