(function (document, $, google, sul) {
    $(document).ready(function () {
        $('.sul-unit-map[data-unit][data-script="default"]')
            .each(function () {
                $(this).mapInit($(this).data('unit'));
            });
    });

    $.fn.mapInit = function (unit) {
        var box = $(this);
        if (box.length !== 1 || !$.isPlainObject(unit)) {
            return;
        }
        var trans = box.data('translations');

        var map = new google.maps.Map(box[0], {
            zoom: sul.map.defaults.zoom
        });

        var infoWindow = new google.maps.InfoWindow({
            content: ''
        });

        function addUnit(unit, markers, withCurrent, ancestorsIcon) {
            if (unit.markerUrl) {
                ancestorsIcon = unit.markerUrl;
            }
            if (withCurrent && unit.location) {
                var key = unit.location.lat + ',' + unit.location.lng;
                if (!markers.infos[key]) {
                    markers.infos[key] = {
                        address: null,
                        list: []
                    };
                    var markerConfig = {
                        animation: google.maps.Animation.DROP,
                        draggable: false,
                        map: map,
                        position: unit.location
                    };
                    if (ancestorsIcon) {
                        markerConfig.icon = ancestorsIcon;
                    }
                    var marker = new google.maps.Marker(markerConfig);
                    marker.addListener('click', function () {
                        infoWindow.setContent(
                            (markers.infos[key].address ? markers.infos[key].address + '<br><br>' : '') +
                            markers.infos[key].list.join('<br>')
                        );
                        infoWindow.open(map, marker);
                    });
                    markers.center.lat *= markers.count;
                    markers.center.lng *= markers.count;
                    markers.center.lat += unit.location.lat;
                    markers.center.lng += unit.location.lng;
                    markers.count++;
                    markers.center.lat /= markers.count;
                    markers.center.lng /= markers.count;
                }
                if (!markers.infos[key].address && unit.address) {
                    markers.infos[key].address = unit.address;
                }
                var url = unit.url ? (!unit.url.match(/https?:\/\//i) ? 'http://' : '') + unit.url : null;
                markers.infos[key].list.push(
                    '<strong>' + (url ? '<a href="' + url + '" target="_blank">' + unit.name + '</a>' : unit.name) +
                        '</strong><br>' +
                    (unit.meetingsTime ? trans.meetingsTime + ' ' + unit.meetingsTime + '<br>' : '')
                );
            }
            if ($.isArray(unit.children)) {
                $.each(unit.children, function (index, child) {
                    markers = addUnit(child, markers, true, ancestorsIcon);
                });
            }
            return markers;
        }

        var markers = addUnit(unit, {
            center: {
                lat: 0,
                lng: 0
            },
            count: 0,
            infos: {}
        }, !!box.data('with-current'));
        map.setCenter(markers.center);
    };
})(document, jQuery, google, sul);
