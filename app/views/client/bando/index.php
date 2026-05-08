<?php
$locationsJson = [];
if (!empty($locations)) {
    $locs = [];
    foreach ($locations as $loc) {
        $locs[] = [
            'id' => $loc['id'],
            'name' => $loc['name'],
            'address' => $loc['address'] ?? '',
            'type' => $loc['type'],
            'lat' => (float) $loc['latitude'],
            'lng' => (float) $loc['longitude'],
        ];
    }
    $locationsJson = $locs;
}

$typeLabels = [
    'camera' => 'Camera giám sát',
    'csgt' => 'Trạm CSGT',
    'toll' => 'Trạm thu phí',
    'inspection' => 'Trạm đăng kiểm',
];
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-map-location-dot me-2 text-primary"></i>Bản đồ địa điểm
    </h2>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="/ban-do" class="btn <?= empty($currentType) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    <i class="fas fa-globe me-1"></i>Tất cả
                </a>
                <a href="/ban-do?type=camera" class="btn <?= ($currentType ?? '') === 'camera' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    <i class="fas fa-camera me-1"></i>Camera giám sát
                </a>
                <a href="/ban-do?type=csgt" class="btn <?= ($currentType ?? '') === 'csgt' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    <i class="fas fa-shield-halved me-1"></i>Trạm CSGT
                </a>
                <a href="/ban-do?type=toll" class="btn <?= ($currentType ?? '') === 'toll' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    <i class="fas fa-coins me-1"></i>Trạm thu phí
                </a>
                <a href="/ban-do?type=inspection" class="btn <?= ($currentType ?? '') === 'inspection' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    <i class="fas fa-wrench me-1"></i>Trạm đăng kiểm
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Map -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div id="map" style="height: 500px; width: 100%;"></div>
            </div>
        </div>

        <!-- Location List -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-list me-2"></i>Danh sách địa điểm
                    <span class="badge bg-light text-dark ms-2"><?= count($locations ?? []) ?></span>
                </div>
                <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                    <?php if (empty($locations)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-map-marker-alt fa-3x mb-2"></i>
                            <p>Chưa có địa điểm nào.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($locations as $loc): ?>
                                <a href="javascript:void(0)" class="list-group-item list-group-item-action map-location-item"
                                   data-lat="<?= (float) $loc['latitude'] ?>"
                                   data-lng="<?= (float) $loc['longitude'] ?>"
                                   data-name="<?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                <?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?>
                                            </h6>
                                            <?php if (!empty($loc['address'])): ?>
                                                <small class="text-muted d-block">
                                                    <?= htmlspecialchars($loc['address'], ENT_QUOTES, 'UTF-8') ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <span class="badge bg-secondary ms-2">
                                            <?= htmlspecialchars($typeLabels[$loc['type']] ?? $loc['type'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Google Maps -->
<script>
(function() {
    function initMap() {
        var center = { lat: <?= $centerLat ?? 16.0 ?>, lng: <?= $centerLng ?? 106.0 ?> };
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,
            center: center,
            mapTypeControl: true,
            streetViewControl: false,
        });

        var infowindow = new google.maps.InfoWindow();
        var markers = [];

        var locations = <?= json_encode($locationsJson, JSON_UNESCAPED_UNICODE) ?>;
        var bounds = new google.maps.LatLngBounds();

        locations.forEach(function(loc) {
            var position = { lat: loc.lat, lng: loc.lng };
            var marker = new google.maps.Marker({
                position: position,
                map: map,
                title: loc.name,
                animation: google.maps.Animation.DROP,
            });

            var content = '<div style="min-width:200px">' +
                '<h6>' + loc.name + '</h6>' +
                (loc.address ? '<p class="mb-1 small text-muted">' + loc.address + '</p>' : '') +
                '</div>';

            marker.addListener('click', function() {
                infowindow.setContent(content);
                infowindow.open(map, marker);
            });

            markers.push(marker);
            bounds.extend(position);
        });

        if (locations.length > 0) {
            map.fitBounds(bounds, { top: 30, bottom: 30, left: 30, right: 30 });
        }

        // Click on sidebar location
        document.querySelectorAll('.map-location-item').forEach(function(el) {
            el.addEventListener('click', function() {
                var lat = parseFloat(this.dataset.lat);
                var lng = parseFloat(this.dataset.lng);
                var name = this.dataset.name;
                map.setCenter({ lat: lat, lng: lng });
                map.setZoom(15);

                // Trigger click on matching marker
                markers.forEach(function(m) {
                    if (Math.abs(m.getPosition().lat() - lat) < 0.001 &&
                        Math.abs(m.getPosition().lng() - lng) < 0.001 &&
                        m.getTitle() === name) {
                        new google.maps.event.trigger(m, 'click');
                    }
                });
            });
        });
    }

    // Load Google Maps API
    var script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?callback=initMap&key=';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
    window.initMap = initMap;
})();
</script>
