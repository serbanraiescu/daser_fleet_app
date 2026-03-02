<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;

class ReportController extends BaseController
{
    public function index(): void
    {
        $this->render('tenant/reports/index', ['title' => 'Reports & Exports']);
    }

    public function exportTrips(): void
    {
        $tenantId = Auth::tenantId();
        $trips = DB::fetchAll("SELECT t.*, v.license_plate, u.name as driver_name 
                               FROM trips t 
                               JOIN vehicles v ON t.vehicle_id = v.id 
                               JOIN users u ON t.driver_id = u.id 
                               WHERE t.tenant_id = ? 
                               ORDER BY t.start_time DESC", [$tenantId]);

        $filename = "trips_" . date('Y-m-d') . ".csv";
        $header = ['ID', 'Vehicle', 'Driver', 'Type', 'Start Time', 'Start KM', 'End Time', 'End KM', 'Status', 'Review'];
        
        $this->downloadCsv($filename, $header, array_map(function($t) {
            return [
                $t['id'], $t['license_plate'], $t['driver_name'], $t['type'],
                $t['start_time'], $t['start_km'], $t['end_time'], $t['end_km'],
                $t['status'], $t['needs_review'] ? 'YES' : 'NO'
            ];
        }, $trips));
    }

    private function downloadCsv(string $filename, array $header, array $data): void
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $header);
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
}
