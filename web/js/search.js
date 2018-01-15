/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(function () {

    var url = $(".search-data").data('url');
    var img_ajax = $(".search-data").data('img');

    $("#search").autocomplete({
        minLength: 2,
        cache: false,
        dataType: 'json',
        source: function (request, response) {
            $("#search").attr("style", "background:transparent url('" + img_ajax + "')no-repeat 99% 10px;)");
            $.getJSON(url, {term: request.term},
                    function (data) {
                        var results = [];
                        $.each(data, function (i, item) {
                            var label = '<div class=""><img src="' + item.filename + '"/> ' + item.name + "</div>";
                            results.push({'value': item.value, 'label': label, 'name': item.name, 'href': item.href})
                        })
                        response(results);
                        $("#search").attr("style", "");
                    })

        },
        select: function (event, ui) {
            $(this).val(ui.item.name);
            window.location.href = ui.item.href;
            return false;
        },
        focus: function (event, ui) {
            $(this).val(ui.item.name);
            return false;
        },

    }).data('ui-autocomplete')._renderItem = function (ul, item) {
        return $("<li>")
                .attr("data-value", item.value)
                .append(item.label)
                .appendTo(ul);
    }



});

