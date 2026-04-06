<?php
/**
 * Cron Master Runner - Central entry point for all FleetLog cron tasks
 * 
 * Supports: CLI and HTTP
 * Protection: Token-based
 * Concurrency: File locking
 * 
 * Usage CLI: php cron_master.php
 * Usage HTTP: curl https://domeniu.ro/fleetlog/cron/cron_master.php?token=secure_daser_cron
 */

require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;
use FleetLog\Core\EmailService;
use FleetLog\Core\SMSService;
use FleetLog\Core\ExpirationService;
use FleetLog\Core\CronService;

// 1. Initialize
Autoloader::register();
EnvLoader::load(__DIR__ . '/../.env');

$isCli = (PHP_SAPI === 'cli');
$token = $_GET['token'] ?? null;
$secureToken = getenv('CRON_TOKEN') ?: 'secure_daser_cron';

// 2. Authentication
if (!$isCli && $token !== $secureToken) {
    header('HTTP/1.1 403 Forbidden');
    die("403 Forbidden - Invalid Cron Token");
}

// 3. Concurrency Control (Lock)
$lockFile = sys_get_temp_dir() . '/daser_cron_master.lock';
$lockHandle = fopen($lockFile, 'w+');

if (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
    die("Cron is already running. Parallel execution prevented.\n");
}

// 4. Logging & Prep
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/cron.log';
function logCron($task, $message, $status = 'INFO') {
    global $logFile;
    $entry = "[" . date('Y-m-d H:i:s') . "] [$status] [$task] $message\n";
    file_put_contents($logFile, $entry, FILE_APPEND);
    echo $entry;
}

$tasksExecuted = 0;
$errorsCount = 0;

logCron('MASTER', 'Started Cron Master execution');

/**
 * Helper to check if a task should run based on frequency
 */
function shouldRunTask(string $key, string $frequency = 'daily'): bool {
    $lastRun = DB::fetch("SELECT `value` FROM system_settings WHERE `key` = ?", ["cron_last_{$key}"]);
    if (!$lastRun) return true;

    $lastTimestamp = strtotime($lastRun['value']);
    $now = time();

    if ($frequency === 'daily') {
        return date('Y-m-d', $lastTimestamp) !== date('Y-m-d', $now);
    }
    if ($frequency === 'weekly') {
        return (date('W', $lastTimestamp) !== date('W', $now)) && (date('N') == 1); // Only on Mondays
    }
    return true; // every_run
}

function updateLastRun(string $key) {
    DB::query("INSERT INTO system_settings (`key`, `value`) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE `value` = NOW()", ["cron_last_{$key}"]);
}

// 5. TASK SEQUENCING

// TASK 1: Process Email Queue (Every Run)
try {
    $count = EmailService::processQueue(20);
    logCron('EmailQueue', "Processed $count emails", 'SUCCESS');
    $tasksExecuted++;
} catch (Exception $e) {
    logCron('EmailQueue', $e->getMessage(), 'ERROR');
    $errorsCount++;
}

// TASK 1.5: Process Daily SMS Reminders (Every Run - handles hour checking internally)
try {
    SMSService::processDailyReminders();
    logCron('SMSReminders', "Checked and processed reminders", 'SUCCESS');
    $tasksExecuted++;
} catch (Exception $e) {
    logCron('SMSReminders', $e->getMessage(), 'ERROR');
    $errorsCount++;
}

// TASK 2: Scan Email Expirations (Daily)
if (shouldRunTask('email_expirations', 'daily')) {
    try {
        $count = ExpirationService::runEmailExpiryChecks();
        updateLastRun('email_expirations');
        logCron('EmailExpirations', "Enqueued $count alerts", 'SUCCESS');
        $tasksExecuted++;
    } catch (Exception $e) {
        logCron('EmailExpirations', $e->getMessage(), 'ERROR');
        $errorsCount++;
    }
}

// TASK 3: Scan SMS Expirations (Daily)
if (shouldRunTask('sms_expirations', 'daily')) {
    try {
        list($count, $skipped) = SMSService::processExpiryAlerts();
        updateLastRun('sms_expirations');
        logCron('SMSExpirations', "Enqueued $count SMS alerts", 'SUCCESS');
        $tasksExecuted++;
    } catch (Exception $e) {
        logCron('SMSExpirations', $e->getMessage(), 'ERROR');
        $errorsCount++;
    }
}

// TASK 4: Photo Cleanup (Daily)
if (shouldRunTask('photo_cleanup', 'daily')) {
    try {
        $count = CronService::handlePhotoCleanup();
        updateLastRun('photo_cleanup');
        logCron('PhotoCleanup', "Deleted $count old files", 'SUCCESS');
        $tasksExecuted++;
    } catch (Exception $e) {
        logCron('PhotoCleanup', $e->getMessage(), 'ERROR');
        $errorsCount++;
    }
}

// TASK 5: Weekly Report (Weekly - Monday)
if (shouldRunTask('weekly_report', 'weekly')) {
    try {
        if (CronService::handleWeeklyReport()) {
            updateLastRun('weekly_report');
            logCron('WeeklyReport', "Report enqueued successfully", 'SUCCESS');
            $tasksExecuted++;
        }
    } catch (Exception $e) {
        logCron('WeeklyReport', $e->getMessage(), 'ERROR');
        $errorsCount++;
    }
}

// TASK 5.8: Open Trip Auto-Reminders (Daily - at 19:00 and 20:00)
$currentHour = (int)date('H');
if (in_array($currentHour, [19, 20]) && shouldRunTask("open_trip_reminders_h{$currentHour}", 'daily')) {
    try {
        SMSService::sendAutomatedOpenTripReminders($currentHour);
        updateLastRun("open_trip_reminders_h{$currentHour}");
        logCron('OpenTripReminders', "Auto-reminders processed for hour $currentHour", 'SUCCESS');
        $tasksExecuted++;
    } catch (Exception $e) {
        logCron('OpenTripReminders', $e->getMessage(), 'ERROR');
        $errorsCount++;
    }
}

// TASK 6: Daily Tenant Summary Report (Daily - After 21:00)
if (shouldRunTask('daily_tenant_report', 'daily') && (int)date('H') >= 21) {
    try {
        SMSService::sendDailyTenantReports();
        updateLastRun('daily_tenant_report');
        logCron('DailyTenantReport', "Reports processed and enqueued", 'SUCCESS');
        $tasksExecuted++;
    } catch (Exception $e) {
        logCron('DailyTenantReport', $e->getMessage(), 'ERROR');
        $errorsCount++;
    }
}
// TASK 7: Monthly Admin Summary Report (Run on 1st of each month)
if (date('j') == '1' && shouldRunTask('monthly_admin_report', 'monthly')) {
    try {
        $adminEmail = DB::fetch("SELECT value FROM system_settings WHERE `key` = 'admin_report_email'")['value'] ?? null;
        if ($adminEmail) {
            $lastMonth = date('Y-m', strtotime('first day of last month'));
            $tenants = DB::fetchAll("SELECT id FROM tenants WHERE status = 'active' AND send_to_admin_enabled = 1");
            
            foreach ($tenants as $t) {
                EmailService::sendDetailedMonthlyReport((int)$t['id'], $lastMonth, $adminEmail);
            }
            
            updateLastRun('monthly_admin_report');
            logCron('MonthlyAdminReport', "Monthly summaries queued for " . count($tenants) . " tenants", 'SUCCESS');
            $tasksExecuted++;
        }
    } catch (Exception $e) {
        logCron('MonthlyAdminReport', $e->getMessage(), 'ERROR');
        $errorsCount++;
    }
}

logCron('MASTER', "Finished. Tasks: $tasksExecuted, Errors: $errorsCount");

// 6. Final Clean up
flock($lockHandle, LOCK_UN);
fclose($lockHandle);

if (!$isCli) {
    echo "\nCron executed successfully\n";
    echo "Tasks executed: $tasksExecuted\n";
    echo "Errors: $errorsCount\n";
}
