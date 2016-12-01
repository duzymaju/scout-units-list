(function (document, $, google, sul) {
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

        $('#sul-localization-map').mapInit();
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

    $.fn.mapInit = function () {
        var box = $(this);
        if (box.length !== 1) {
            return;
        }

        var container = box.parent();
        var latInput = container.children('input[name="localizationLat"]');
        var lngInput = container.children('input[name="localizationLng"]');

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
})(document, jQuery, google, sul);

