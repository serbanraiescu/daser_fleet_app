<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;

class ReportController extends BaseController
{
    public function index(): void
    {
        $this->render('tenant/reports/index', ['title' => 'Fleet Analysis & Reports']);
    }

    public function vehicleReport(): void
    {
        $tenantId = Auth::tenantId();
        $period = $_GET['period'] ?? 'monthly';
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        $dateFilter = $this->getDateFilter($period, $month, $year);
        $endDate = $this->getEndDate($period, $month, $year);

        // Fetch vehicle stats
        $vehicles = DB::fetchAll("
            SELECT 
                v.id, v.license_plate, v.make, v.model,
                (SELECT MIN(start_km) FROM trips WHERE vehicle_id = v.id AND tenant_id = ? AND start_time >= ? AND start_time < ?) as start_km,
                (SELECT MAX(end_km) FROM trips WHERE vehicle_id = v.id AND tenant_id = ? AND end_time >= ? AND end_time < ?) as end_km,
                (SELECT SUM(liters) FROM fuelings WHERE vehicle_id = v.id AND tenant_id = ? AND created_at >= ? AND created_at < ?) as total_liters,
                (SELECT SUM(total_price) FROM fuelings WHERE vehicle_id = v.id AND tenant_id = ? AND created_at >= ? AND created_at < ?) as total_fuel_cost,
                (SELECT COUNT(*) FROM trips WHERE vehicle_id = v.id AND tenant_id = ? AND start_time >= ? AND start_time < ?) as trip_count,
                (SELECT COUNT(*) FROM damage_reports WHERE vehicle_id = v.id AND tenant_id = ? AND datetime >= ? AND datetime < ?) as damage_count,
                (SELECT SUM(repair_cost) FROM damage_reports WHERE vehicle_id = v.id AND tenant_id = ? AND datetime >= ? AND datetime < ?) as total_repair_cost,
                (SELECT SUM(cost) FROM vehicle_expenses WHERE vehicle_id = v.id AND tenant_id = ? AND expense_date >= ? AND expense_date < ?) as total_other_expenses
            FROM vehicles v
            WHERE v.tenant_id = ?
        ", [
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate,
            $tenantId
        ]);

        $this->render('tenant/reports/vehicle_report', [
            'title' => 'Vehicle Performance Report',
            'vehicles' => $vehicles,
            'period' => $period,
            'selected_month' => $month,
            'selected_year' => $year
        ]);
    }

    public function driverReport(): void
    {
        $tenantId = Auth::tenantId();
        $period = $_GET['period'] ?? 'monthly';
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        $dateFilter = $this->getDateFilter($period, $month, $year);
        $endDate = $this->getEndDate($period, $month, $year);

        $drivers = DB::fetchAll("
            SELECT 
                u.id, u.name,
                (SELECT SUM(end_km - start_km) FROM trips WHERE driver_id = u.id AND tenant_id = ? AND start_time >= ? AND start_time < ? AND status = 'closed') as total_km,
                (SELECT COUNT(*) FROM trips WHERE driver_id = u.id AND tenant_id = ? AND start_time >= ? AND start_time < ?) as trip_count,
                (SELECT COUNT(DISTINCT vehicle_id) FROM trips WHERE driver_id = u.id AND tenant_id = ? AND start_time >= ? AND start_time < ?) as vehicle_count,
                (SELECT COUNT(*) FROM damage_reports WHERE driver_id = u.id AND tenant_id = ? AND datetime >= ? AND datetime < ?) as damage_count,
                (SELECT SUM(liters) FROM fuelings WHERE user_id = u.id AND tenant_id = ? AND created_at >= ? AND created_at < ?) as total_liters,
                (SELECT SUM(total_price) FROM fuelings WHERE user_id = u.id AND tenant_id = ? AND created_at >= ? AND created_at < ?) as total_fuel_cost
            FROM users u
            WHERE u.tenant_id = ? AND u.role = 'driver'
        ", [
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate,
            $tenantId, $dateFilter, $endDate,
            $tenantId, $dateFilter, $endDate, 
            $tenantId
        ]);

        $this->render('tenant/reports/driver_report', [
            'title' => 'Driver Activity Report',
            'drivers' => $drivers,
            'period' => $period,
            'selected_month' => $month,
            'selected_year' => $year
        ]);
    }

    private function getDateFilter(string $period, string $month, string $year): string
    {
        switch ($period) {
            case 'daily': return date('Y-m-d 00:00:00');
            case 'weekly': return date('Y-m-d 00:00:00', strtotime('-7 days'));
            case 'yearly': return "{$year}-01-01 00:00:00";
            case 'monthly':
            default: return "{$year}-{$month}-01 00:00:00";
        }
    }

    private function getEndDate(string $period, string $month, string $year): string
    {
        switch ($period) {
            case 'daily': return date('Y-m-d 23:59:59');
            case 'weekly': return date('Y-m-d 23:59:59');
            case 'yearly': return "{$year}-12-31 23:59:59";
            case 'monthly':
            default: 
                $lastDay = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
                return "{$year}-{$month}-{$lastDay} 23:59:59";
        }
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
