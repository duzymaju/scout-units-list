(function (document, $, sul) {
    $(document).ready(function () {
        $('input[data-sul-autocomplete-action]').each(function () {
            var hidden = $(this);
            var text = $('<input type="text">');
            hidden.attr('type', 'hidden')
                .before(text);
            text.autocomplete({
                minLength: 0,
                select: function (event, ui) {
                    hidden.val(ui.item.id);
                },
                source: sul.ajaxUrl + '?action=' + hidden.data('sul-autocomplete-action')
            });
        });
    });
})(document, jQuery, sul);

