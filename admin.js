(function (document, $, sul) {
    $(document).ready(function () {
        $('input[data-autocomplete-action]').each(function () {
            var hidden = $(this);
            var box = hidden.closest('.autocomplete-box');
            var text = box.find('input[type="text"]');
            var boxFilledClass = 'autocomplete-filled';

            var valueBox = box.find('.autocomplete-value');
            var valueText = $('<span>');
            valueText.text(valueBox.text());
            var valueClear = $('<span class="autocomplete-clear">×</span>');
            valueBox.text(' ')
                .prepend(valueText)
                .append(valueClear);
            if (box.hasClass(boxFilledClass)) {
                text.disableField();
            }

            text.autocomplete({
                minLength: 2,
                select: function (event, ui) {
                    hidden.val(ui.item.id);
                    valueText.text(ui.item.value);
                    box.addClass(boxFilledClass);
                    text.disableField();
                    return false;
                },
                source: sul.ajaxUrl + '?action=' + hidden.data('autocomplete-action')
            });
            valueClear.on('click', function () {
                hidden.val('');
                box.removeClass(boxFilledClass);
                text.enableField();
            });
        });
    });

    $.fn.enableField = function () {
        $(this).prop('readonly', false)
            .prop('disabled', false)
            .focus();
    };

    $.fn.disableField = function () {
        $(this).val('')
            .prop('readonly', true)
            .prop('disabled', true);
    };
})(document, jQuery, sul);

