/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(function () {

//    $("#shipping-country select").val("CH");
//    $("#shipping-country select").attr("disabled", "disabled");

    var zone_url = $(".zone-data").data('urlgeodata');
    var zone_img_ajax = $(".zone-data").data('img');
    var zone_deliver = $(".zone-data").data('txtdeliver');
    var zone_no_deliver = $(".zone-data").data('txtnodeliver');
    var zone_url_checkout = $(".zone-data").data('urlcheckout');

    var postcity = "";
    $("#zone_shipping").autocomplete({
        minLength: 2,
        cache: false,
        dataType: 'json',
        source: function (request, response) {
            $("#zone_shipping").attr("style", "background:transparent url('" + zone_img_ajax + "')no-repeat 99% 7px;)");
            $.getJSON(zone_url, {term: request.term},
                    function (data) {
                        var results = [];
                        $.each(data, function (i, item) {
                            //results.push(item.entry);
                            results.push({'value': item.value, 'label': item.label})
                        })

                        response(results);
                        $("#zone_shipping").attr("style", "");
                    })

        },
        select: function (event, ui) {
            postcity = ui.item.value;
            $(this).val(ui.item.label);
            checkDistance();
            return false;
        },
        focus: function (event, ui) {
            $(this).val(ui.item.label);
            return false;
        },
    });

    $("#shipping_methode").click(function () {
        if (checkDistance()) {
            //$("#postcity").val(postcity);
            //shippingMethod.save();
        }
    });

    function checkDistance() {
        var val = postcity.split("___");
        if (parseInt(val[2]) <= parseInt(val[3])) {
            $(".zone-message").html(zone_deliver + ' <img src="' + zone_img_ajax + '" />');
            $(".zone-show-data").hide();
            $("input[name=zone-plz]").val(val[0]);
            $("input[name=zone-city]").val(val[1]);
            $(".zone-message-start").html(zone_deliver);
            setTimeout(function () {
                $("#zone_shipping_post").submit();
            }, 1000);
            return true;
        } else {
            $(".zone-message").html(zone_no_deliver);
            $(".zone-show-data").show();
            $(".zone-message-start").html(zone_no_deliver);
            //initialize();
            return false;
        }

    }
});

