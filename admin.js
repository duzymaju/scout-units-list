(function (document, $, google, sul) {
    var autocompleteType = '';

    $(document).ready(function () {
        var form = $('form.sul-form');
        var list = $('table.sul-list');

        form.find('input[data-autocomplete-action]')
            .autocompleteInit();

        form.typeManage(form.find('select[name="type"]'), form.find('select[name="subtype"]'), function (type) {
            autocompleteType = type;
        });

        form.find('#sul-location-map')
            .mapInit();

        list.versionedItemDeleteForm();
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

    $.fn.autocompleteInit = function () {
        $(this).each(function () {
            var hidden = $(this);
            if (hidden.length !== 1) {
                return;
            }

            var action = hidden.data('autocomplete-action');
            var valueFieldSelector = hidden.data('autocomplete-value-field');
            var valueField = typeof valueFieldSelector !== 'undefined' ?
                hidden.closest('form').find(valueFieldSelector) : null;
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
                delay: 500,
                minLength: 2,
                select: function (event, ui) {
                    hidden.val(ui.item.id);
                    valueText.text(ui.item.value);
                    box.addClass(boxFilledClass);
                    text.disableField();
                    if (valueField) {
                        valueField.val(ui.item.value);
                    }
                    return false;
                },
                source: function (request, response) {
                    $.ajax({
                        dataType: 'json',
                        data: {
                            action: action,
                            term: request.term,
                            type: autocompleteType
                        },
                        success: response,
                        url: sul.ajaxUrl
                    });
                }
            });
            valueClear.on('click', function () {
                hidden.val('');
                box.removeClass(boxFilledClass);
                text.enableField();
                if (valueField) {
                    valueField.val('');
                }
            });
        });
    };
    
    $.fn.typeManage = function (typeSelect, subtypeSelect, onChange) {
        if (typeSelect.length !== 1 || subtypeSelect.length !== 1) {
            return;
        }

        function change() {
            var type = typeSelect.val();
            var subtype = subtypeSelect.val();

            subtypeSelect.find('option').each(function () {
                var forType = $(this).data('for-type');
                if (forType) {
                    if (forType === type) {
                        $(this).show();
                    } else {
                        $(this).hide();
                        if ($(this).val() === subtype) {
                            $(this).prop('selected', false);
                        }
                    }
                }
            });

            onChange(type, subtype);
        }

        typeSelect.on('change', change);
        subtypeSelect.on('change', change);
        change();
    };

    $.fn.mapInit = function () {
        var box = $(this);
        if (box.length !== 1) {
            return;
        }

        var container = box.parent();
        var latInput = container.children('input[name="locationLat"]');
        var lngInput = container.children('input[name="locationLng"]');

        var coordsSet = latInput.val() !== '' && lngInput.val() !== '';
        var lat = coordsSet ? +latInput.val() : sul.map.defaults.lat;
        var lng = coordsSet ? +lngInput.val() : sul.map.defaults.lng;
        var coords = {
            lat: lat,
            lng: lng
        };

        var map = new google.maps.Map(box[0], {
            center: coords,
            zoom: sul.map.defaults.zoom
        });
        map.addListener('click', function (event) {
            if (!coordsSet) {
                addMarker(event.latLng);
                coordsSet = true;
            } else {
                marker.setPosition(event.latLng);
            }
        });

        var infoWindow = new google.maps.InfoWindow({
            content: ''
        });

        var marker;
        function addMarker(coords) {
            marker = new google.maps.Marker({
                animation: google.maps.Animation.DROP,
                draggable: true,
                map: map,
                position: coords
            });
            marker.addListener('position_changed', function () {
                var position = marker.getPosition();
                setCoordinates(position.lat(), position.lng());
            });
            marker.addListener('click', function () {
                infoWindow.open(map, marker);
            });
            setCoordinates(coords.lat, coords.lng);
        }
        function setCoordinates(lat, lng) {
            latInput.val(lat);
            lngInput.val(lng);
            infoWindow.setContent('lat: ' + lat + ', lng: ' + lng);
        }
        if (coordsSet) {
            addMarker(coords);
        }
    };

    $.fn.versionedItemDeleteForm = function () {
        var list = $(this).find('tbody[data-delete-form-prototype]').first();
        if (list.length !== 1) {
            return;
        }

        var prototype = list.data('delete-form-prototype');
        list.on('click', 'a.sul-delete', function () {
            var listRow = $(this).closest('tr');
            var formRow = $(prototype
                .replace('%name%', listRow.data('delete-form-name'))
                .replace('%deletedId%', listRow.data('deleted-id'))
            );
            var fakeRow = $('<tr>');
            listRow.after(formRow);
            listRow.after(fakeRow);
            listRow.hide();
            formRow.find('form.sul-form input[data-autocomplete-action]')
                .autocompleteInit();

            formRow.find('button.cancel').on('click', function () {
                formRow.unbind();
                formRow.remove();
                fakeRow.remove();
                listRow.show();
            });
        });
    };
})(document, jQuery, google, sul);
