<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Models\Location;

class BanDoController extends Controller
{
    public function index(): void
    {
        $locationModel = new Location();

        $type = $this->input('type', '');
        if ($type !== '') {
            $locations = $locationModel->findByType($type);
        } else {
            $locations = $locationModel->getActiveLocations();
        }

        // Center of Vietnam
        $centerLat = 16.0;
        $centerLng = 106.0;

        if (!empty($locations)) {
            $sumLat = 0;
            $sumLng = 0;
            foreach ($locations as $loc) {
                $sumLat += (float) $loc['latitude'];
                $sumLng += (float) $loc['longitude'];
            }
            $centerLat = $sumLat / count($locations);
            $centerLng = $sumLng / count($locations);
        }

        $this->view('client/bando/index', [
            'title' => 'Bản đồ địa điểm',
            'locations' => $locations,
            'centerLat' => $centerLat,
            'centerLng' => $centerLng,
            'currentType' => $type,
        ]);
    }
}
