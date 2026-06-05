<?php
/**
 * Chuẩn bị dữ liệu cho Google Maps JS.
 * - $locationsJson: mảng PHP được encode sang JSON để nhúng vào JS.
 * - Mỗi địa điểm gồm: id, name, address, type, lat, lng (ép kiểu float).
 * - $typeLabels: map type (camera/csgt/toll/inspection) -> nhãn tiếng Việt.
 */
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
    'camera' => 'Surveillance Camera',
    'csgt' => 'Traffic Police Station',
    'toll' => 'Toll Station',
    'inspection' => 'Inspection Station',
];
?>

<!--
  Trang bản đồ địa điểm vi phạm (client/bando/index.php)
  - Bộ lọc theo loại địa điểm: camera, CSGT, thu phí, đăng kiểm
  - Bản đồ Google Maps hiển thị markers
  - Sidebar danh sách địa điểm, click vào -> fly to vị trí trên bản đồ
  - Google Maps JS API load async
-->

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <!-- fa-map-location-dot: icon bản đồ có đánh dấu vị trí -->
        <i class="fas fa-map-location-dot me-2 text-primary"></i>Location Map
    </h2>

    <!-- Partial hiển thị thông báo -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- === Bộ lọc loại địa điểm === -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- d-flex flex-wrap gap-2: flexbox gói xuống dòng, gap 0.5rem -->
            <div class="d-flex flex-wrap gap-2">
                <!-- Nút "Tất cả" -->
                <a href="/ban-do" class="btn <?= empty($currentType) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    <!-- fa-globe: icon quả địa cầu (tất cả) -->
                    <i class="fas fa-globe me-1"></i>All
                </a>
                <!-- Camera giám sát -->
                <a href="/ban-do?type=camera" class="btn <?= ($currentType ?? '') === 'camera' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    <!-- fa-camera: icon máy quay -->
                    <i class="fas fa-camera me-1"></i>Surveillance Camera
                </a>
                <!-- Trạm CSGT -->
                <a href="/ban-do?type=csgt" class="btn <?= ($currentType ?? '') === 'csgt' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    <!-- fa-shield-halved: icon khiên chắn (cảnh sát) -->
                    <i class="fas fa-shield-halved me-1"></i>Traffic Police Station
                </a>
                <!-- Trạm thu phí -->
                <a href="/ban-do?type=toll" class="btn <?= ($currentType ?? '') === 'toll' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    <!-- fa-coins: icon đồng xu (thu phí) -->
                    <i class="fas fa-coins me-1"></i>Toll Station
                </a>
                <!-- Trạm đăng kiểm -->
                <a href="/ban-do?type=inspection" class="btn <?= ($currentType ?? '') === 'inspection' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                    <!-- fa-wrench: icon cờ lê (đăng kiểm, sửa chữa) -->
                    <i class="fas fa-wrench me-1"></i>Inspection Station
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- === Bản đồ Google Maps (col-lg-8: 8/12 desktop) === -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <!-- div#map: container của Google Maps, height 500px -->
                <div id="map" style="height: 500px; width: 100%;"></div>
            </div>
        </div>

        <!-- === Sidebar: Danh sách địa điểm (col-lg-4: 4/12 desktop) === -->
        <!-- mt-4 mt-lg-0: margin-top trên mobile, 0 trên desktop -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <!-- h-100: full chiều cao hàng -->
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    <!-- fa-list: icon danh sách -->
                    <i class="fas fa-list me-2"></i>Location List
                    <!-- badge bg-light text-dark: huy hiệu nền sáng chữ tối, hiển thị tổng số -->
                    <span class="badge bg-light text-dark ms-2"><?= count($locations ?? []) ?></span>
                </div>
                <!-- max-height: 500px + overflow-y: auto -> danh sách cuộn nếu quá dài -->
                <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                    <?php if (empty($locations)): ?>
                        <div class="text-center py-4 text-muted">
                            <!-- fa-map-marker-alt: icon địa điểm trên bản đồ -->
                            <i class="fas fa-map-marker-alt fa-3x mb-2"></i>
                            <p>No locations available.</p>
                        </div>
                    <?php else: ?>
                        <!-- list-group-flush: danh sách sát viền -->
                        <div class="list-group list-group-flush">
                            <?php foreach ($locations as $loc): ?>
                                <!--
                                  map-location-item: class để JS bắt sự kiện click
                                  data-lat, data-lng, data-name: lưu tọa độ và tên vào dataset
                                  javascript:void(0): link không điều hướng, chỉ dùng JS
                                -->
                                <a href="javascript:void(0)" class="list-group-item list-group-item-action map-location-item"
                                   data-lat="<?= (float) $loc['latitude'] ?>"
                                   data-lng="<?= (float) $loc['longitude'] ?>"
                                   data-name="<?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <!-- d-flex justify-content-between: tên trái + badge loại phải -->
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <!-- fa-map-marker-alt text-danger: icon địa điểm đỏ -->
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                <?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?>
                                            </h6>
                                            <!-- Địa chỉ (nếu có) -->
                                            <?php if (!empty($loc['address'])): ?>
                                                <small class="text-muted d-block">
                                                    <?= htmlspecialchars($loc['address'], ENT_QUOTES, 'UTF-8') ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <!-- badge bg-secondary: huy hiệu xám, hiển thị loại địa điểm -->
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

<!--
  === Google Maps JavaScript ===
  - IIFE (Immediately Invoked Function Expression) để cô lập scope
  - initMap(): callback khi Google Maps API load xong
  - Tạo markers từ PHP data (json_encode nhúng vào JS)
  - InfoWindow hiển thị khi click marker
  - Sidebar click: fly to vị trí và trigger click marker tương ứng
  - Load script API async + defer
-->
<script>
// IIFE: tránh biến global, chạy ngay khi script load
(function() {
    /**
     * Callback initMap: được gọi khi Google Maps API tải xong.
     * - Tạo bản đồ tại #map, tâm mặc định (16.0, 106.0) hoặc từ controller.
     * - Duyệt mảng locations: tạo marker + InfoWindow cho mỗi điểm.
     * - fitBounds tự động zoom vừa tất cả markers.
     * - Gắn sự kiện click cho sidebar .map-location-item.
     */
    function initMap() {
        // Tâm bản đồ: giá trị từ controller hoặc mặc định (16.0 Bắc, 106.0 Đông ~ miền Trung VN)
        var center = { lat: <?= $centerLat ?? 16.0 ?>, lng: <?= $centerLng ?? 106.0 ?> };
        // Khởi tạo bản đồ Google Maps
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,                       // Mức zoom mặc định (6 = toàn Việt Nam)
            center: center,
            mapTypeControl: true,          // Hiển thị nút chọn loại bản đồ (Map/Satellite)
            streetViewControl: false,      // Ẩn nút Street View (Pegman)
        });

        // InfoWindow: cửa sổ popup khi click marker
        var infowindow = new google.maps.InfoWindow();
        var markers = [];

        // Nhúng dữ liệu địa điểm từ PHP vào JS
        // json_encode PHP -> JSON literal trong JS
        var locations = <?= json_encode($locationsJson, JSON_UNESCAPED_UNICODE) ?>;
        // LatLngBounds: để tự động tính khung nhìn vừa tất cả markers
        var bounds = new google.maps.LatLngBounds();

        // Duyệt qua từng địa điểm, tạo marker
        locations.forEach(function(loc) {
            var position = { lat: loc.lat, lng: loc.lng };
            var marker = new google.maps.Marker({
                position: position,
                map: map,
                title: loc.name,
                animation: google.maps.Animation.DROP, // Hiệu ứng rơi từ trên xuống
            });

            // Nội dung HTML trong InfoWindow khi click marker
            var content = '<div style="min-width:200px">' +
                '<h6>' + loc.name + '</h6>' +
                (loc.address ? '<p class="mb-1 small text-muted">' + loc.address + '</p>' : '') +
                '</div>';

            // Gắn sự kiện click cho marker -> mở InfoWindow
            marker.addListener('click', function() {
                infowindow.setContent(content);
                infowindow.open(map, marker);
            });

            markers.push(marker);
            // Mở rộng bounds để chứa vị trí marker này
            bounds.extend(position);
        });

        // Nếu có ít nhất 1 marker, tự động fit bounds (padding 30px mỗi bên)
        if (locations.length > 0) {
            map.fitBounds(bounds, { top: 30, bottom: 30, left: 30, right: 30 });
        }

        // === Xử lý click vào item trong sidebar ===
        document.querySelectorAll('.map-location-item').forEach(function(el) {
            el.addEventListener('click', function() {
                // Lấy tọa độ từ data attributes
                var lat = parseFloat(this.dataset.lat);
                var lng = parseFloat(this.dataset.lng);
                var name = this.dataset.name;
                // Bay đến vị trí với zoom = 15 (mức đường phố)
                map.setCenter({ lat: lat, lng: lng });
                map.setZoom(15);

                // Tìm marker trùng khớp và trigger sự kiện click (hiển thị InfoWindow)
                markers.forEach(function(m) {
                    // So sánh tọa độ với sai số 0.001 độ (~100m) và khớp tên
                    if (Math.abs(m.getPosition().lat() - lat) < 0.001 &&
                        Math.abs(m.getPosition().lng() - lng) < 0.001 &&
                        m.getTitle() === name) {
                        // google.maps.event.trigger: kích hoạt sự kiện click trên marker
                        new google.maps.event.trigger(m, 'click');
                    }
                });
            });
        });
    }

    // === Load Google Maps API script async ===
    var script = document.createElement('script');
    // callback=initMap: gọi initMap() khi script tải xong
    // key=: API key (có thể để trống nếu dùng fallback Leaflet)
    script.src = 'https://maps.googleapis.com/maps/api/js?callback=initMap&key=';
    script.async = true;   // Tải bất đồng bộ, không chặn render trang
    script.defer = true;   // Đợi đến khi HTML parse xong mới thực thi
    document.head.appendChild(script);
    // Gán initMap vào window để Google Maps callback gọi được
    window.initMap = initMap;
})();
</script>
