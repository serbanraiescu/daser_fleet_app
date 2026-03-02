<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;
use FleetLog\App\Repositories\FuelingRepository;
use FleetLog\App\Repositories\VehicleRepository;

class FuelingController extends BaseController
{
    public function show(): void
    {
        $vehicleRepo = new VehicleRepository();
        $vehicles = $vehicleRepo->getActiveByTenant(Auth::tenantId());

        $this->render('driver/fueling/create', [
            'title' => 'Log Fueling',
            'vehicles' => $vehicles
        ]);
    }

    public function store(): void
    {
        $fuelingRepo = new FuelingRepository();
        
        $data = [
            'tenant_id' => Auth::tenantId(),
            'user_id' => Auth::user()['id'],
            'vehicle_id' => (int)$_POST['vehicle_id'],
            'odometer' => (int)$_POST['odometer'],
            'liters' => (float)$_POST['liters'],
            'total_price' => (float)$_POST['total_price'],
            'receipt_photo' => null
        ];

        // Basic image upload handling
        if (isset($_FILES['receipt_photo']) && $_FILES['receipt_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(dirname(dirname(__DIR__))) . '/public/uploads/receipts/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['receipt_photo']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('receipt_') . '.' . $extension;
            if (move_uploaded_file($_FILES['receipt_photo']['tmp_name'], $uploadDir . $filename)) {
                $data['receipt_photo'] = 'uploads/receipts/' . $filename;
            }
        }

        if ($fuelingRepo->create($data)) {
            $this->redirect('/driver/dashboard?success=fueling_logged');
        } else {
            $this->show();
        }
    }
}
