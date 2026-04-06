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
                
                -- Damage Count (from reports + timeline)
                ((SELECT COUNT(*) FROM damage_reports WHERE vehicle_id = v.id AND tenant_id = ? AND datetime >= ? AND datetime < ?) + 
                 (SELECT COUNT(*) FROM vehicle_events WHERE vehicle_id = v.id AND tenant_id = ? AND event_type = 'damage' AND event_date >= ? AND event_date < ?)) as damage_count,

                -- Total Repair Cost (from reports + timeline)
                ((SELECT IFNULL(SUM(repair_cost), 0) FROM damage_reports WHERE vehicle_id = v.id AND tenant_id = ? AND datetime >= ? AND datetime < ?) + 
                 (SELECT IFNULL(SUM(cost), 0) FROM vehicle_events WHERE vehicle_id = v.id AND tenant_id = ? AND event_type = 'damage' AND event_date >= ? AND event_date < ?)) as total_repair_cost,

                -- Total Other Expenses (from legacy expenses + timeline)
                ((SELECT IFNULL(SUM(cost), 0) FROM vehicle_expenses WHERE vehicle_id = v.id AND tenant_id = ? AND expense_date >= ? AND expense_date < ?) + 
                 (SELECT IFNULL(SUM(cost), 0) FROM vehicle_events WHERE vehicle_id = v.id AND tenant_id = ? AND event_type NOT IN ('fueling', 'damage') AND event_date >= ? AND event_date < ?)) as total_other_expenses
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

    public function printVehiclePerformance(): void
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
                
                -- Damage Count (from reports + timeline)
                ((SELECT COUNT(*) FROM damage_reports WHERE vehicle_id = v.id AND tenant_id = ? AND datetime >= ? AND datetime < ?) + 
                 (SELECT COUNT(*) FROM vehicle_events WHERE vehicle_id = v.id AND tenant_id = ? AND event_type = 'damage' AND event_date >= ? AND event_date < ?)) as damage_count,

                -- Total Repair Cost (from reports + timeline)
                ((SELECT IFNULL(SUM(repair_cost), 0) FROM damage_reports WHERE vehicle_id = v.id AND tenant_id = ? AND datetime >= ? AND datetime < ?) + 
                 (SELECT IFNULL(SUM(cost), 0) FROM vehicle_events WHERE vehicle_id = v.id AND tenant_id = ? AND event_type = 'damage' AND event_date >= ? AND event_date < ?)) as total_repair_cost,

                -- Total Other Expenses (from legacy expenses + timeline)
                ((SELECT IFNULL(SUM(cost), 0) FROM vehicle_expenses WHERE vehicle_id = v.id AND tenant_id = ? AND expense_date >= ? AND expense_date < ?) + 
                 (SELECT IFNULL(SUM(cost), 0) FROM vehicle_events WHERE vehicle_id = v.id AND tenant_id = ? AND event_type NOT IN ('fueling', 'damage') AND event_date >= ? AND event_date < ?)) as total_other_expenses
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
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate, 
            $tenantId, $dateFilter, $endDate, 
            $tenantId
        ]);

        $this->render('tenant/reports/print_performance', [
            'title' => 'Raport Performanță Flotă',
            'vehicles' => $vehicles,
            'period' => $period,
            'selected_month' => $month,
            'selected_year' => $year,
            'tenant' => DB::fetch("SELECT * FROM tenants WHERE id = ?", [$tenantId])
        ]);
    }

    public function driverDetails(int $id): void
    {
        $tenantId = Auth::tenantId();
        $period = $_GET['period'] ?? 'monthly';
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        $dateFilter = $this->getDateFilter($period, $month, $year);
        $endDate = $this->getEndDate($period, $month, $year);

        // 1. Driver Info
        $driver = DB::fetch("SELECT id, name, email, phone FROM users WHERE id = ? AND tenant_id = ? AND role = 'driver'", [$id, $tenantId]);
        if (!$driver) {
            $this->redirect('/tenant/reports/driver');
        }

        // 2. Summary Stats
        $stats = DB::fetch("
            SELECT 
                SUM(end_km - start_km) as total_km,
                COUNT(*) as trip_count,
                (SELECT SUM(liters) FROM fuelings WHERE user_id = ? AND tenant_id = ? AND created_at >= ? AND created_at < ?) as total_liters,
                (SELECT SUM(total_price) FROM fuelings WHERE user_id = ? AND tenant_id = ? AND created_at >= ? AND created_at < ?) as total_fuel_cost
            FROM trips 
            WHERE driver_id = ? AND tenant_id = ? AND start_time >= ? AND start_time < ? AND status = 'closed'
        ", [
            $id, $tenantId, $dateFilter, $endDate,
            $id, $tenantId, $dateFilter, $endDate,
            $id, $tenantId, $dateFilter, $endDate
        ]);

        // 3. KM by Type
        $kmByType = DB::fetchAll("
            SELECT type, SUM(end_km - start_km) as km 
            FROM trips 
            WHERE driver_id = ? AND tenant_id = ? AND start_time >= ? AND start_time < ? AND status = 'closed'
            GROUP BY type
        ", [$id, $tenantId, $dateFilter, $endDate]);

        // 4. Trip History
        $trips = DB::fetchAll("
            SELECT t.*, v.license_plate 
            FROM trips t
            JOIN vehicles v ON t.vehicle_id = v.id
            WHERE t.driver_id = ? AND t.tenant_id = ? AND t.start_time >= ? AND t.start_time < ?
            ORDER BY t.start_time DESC
        ", [$id, $tenantId, $dateFilter, $endDate]);

        $this->render('tenant/reports/driver_details', [
            'title' => 'Driver Performance: ' . $driver['name'],
            'driver' => $driver,
            'stats' => $stats,
            'kmByType' => $kmByType,
            'trips' => $trips,
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
