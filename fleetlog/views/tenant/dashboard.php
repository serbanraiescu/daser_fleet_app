<!-- Top Row: Core Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-2">
            <span class="text-slate-500 text-sm font-bold uppercase tracking-wider">Costs (Current Month)</span>
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="text-3xl font-black text-slate-900"><?php echo number_format($stats['monthly_expenses'], 2); ?> <span class="text-sm font-normal text-slate-400">RON</span></div>
        <div class="text-xs text-slate-400 mt-2 font-medium">Recorded this month</div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-2">
            <span class="text-slate-500 text-sm font-bold uppercase tracking-wider">Distance (Current Month)</span>
            <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m-6 3l6-3"></path></svg>
            </div>
        </div>
        <div class="text-3xl font-black text-slate-900"><?php echo number_format($stats['monthly_km']); ?> <span class="text-sm font-normal text-slate-400">KM</span></div>
        <div class="text-xs text-slate-400 mt-2 font-medium">Travelled by fleet</div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-2">
            <span class="text-slate-500 text-sm font-bold uppercase tracking-wider">Active Trips</span>
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>
        <div class="text-3xl font-black text-indigo-600"><?php echo $stats['active_trips_count']; ?></div>
        <div class="text-xs text-slate-400 mt-2 font-medium">Vehicles currently on road</div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-2">
            <span class="text-slate-500 text-sm font-bold uppercase tracking-wider">Fleet Status</span>
            <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
        </div>
        <div class="flex items-baseline space-x-2">
            <div class="text-3xl font-black text-slate-900"><?php echo $stats['total_vehicles']; ?></div>
            <div class="text-xs text-green-600 font-bold"><?php echo $stats['fleet_status']['active'] ?? 0; ?> active</div>
        </div>
        <div class="mt-2 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden flex">
            <?php 
            $activeCount = $stats['fleet_status']['active'] ?? 0;
            $serviceCount = $stats['fleet_status']['service'] ?? 0;
            $total = max(1, $stats['total_vehicles']);
            ?>
            <div style="width: <?php echo ($activeCount/$total)*100; ?>%" class="bg-green-500 h-full"></div>
            <div style="width: <?php echo ($serviceCount/$total)*100; ?>%" class="bg-orange-500 h-full"></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Left Column: Urgent Alerts -->
    <div class="space-y-8">
        <!-- Service Reminders -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h2 class="text-base font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Service Due Soon
                </h2>
                <span class="bg-white px-2 py-0.5 rounded text-[10px] font-black text-slate-400 uppercase border border-slate-200"><?php echo count($serviceDue); ?> ALERTĂ</span>
            </div>
            <div class="p-5 space-y-4">
                <?php if (empty($serviceDue)): ?>
                    <p class="text-sm text-slate-500 text-center py-4 italic">No vehicles due for service.</p>
                <?php else: ?>
                    <?php foreach ($serviceDue as $veh): 
                        $isPast = $veh['km_until_service'] <= 0;
                    ?>
                        <div class="flex items-center justify-between p-3 <?php echo $isPast ? 'bg-red-50' : 'bg-slate-50'; ?> rounded-xl border <?php echo $isPast ? 'border-red-100' : 'border-slate-100'; ?>">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg <?php echo $isPast ? 'bg-red-100' : 'bg-white'; ?> flex items-center justify-center mr-3 font-black text-sm text-slate-700 shadow-sm border border-slate-200">
                                    <?php echo substr($veh['license_plate'], 0, 2); ?>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm"><?php echo htmlspecialchars($veh['license_plate']); ?></div>
                                    <div class="text-[10px] text-slate-500 uppercase font-bold tracking-tight">Last: <?php echo truncate_text($veh['last_maintenance_notes'], 30); ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-black <?php echo $isPast ? 'text-red-600' : 'text-slate-700'; ?>">
                                    <?php echo $isPast ? 'EXPIRAT' : 'IN ' . number_format($veh['km_until_service']) . ' KM'; ?>
                                </div>
                                <a href="/tenant/vehicles/mechanic-report/<?php echo $veh['id']; ?>" class="text-[10px] font-bold text-blue-600 hover:underline">Mechanic Report →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Expiring Documents -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h2 class="text-base font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Expiring Documents
                </h2>
                <span class="bg-white px-2 py-0.5 rounded text-[10px] font-black text-slate-400 uppercase border border-slate-200">Next 30 Days</span>
            </div>
            <div class="p-5 space-y-4">
                <?php if (empty($expiringDocs)): ?>
                    <p class="text-sm text-slate-500 text-center py-4 italic">All documents are up to date.</p>
                <?php else: ?>
                    <?php foreach ($expiringDocs as $veh): ?>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="font-bold text-slate-900 text-sm mb-3"><?php echo htmlspecialchars($veh['license_plate']); ?></div>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">
                                <?php 
                                $today = date('Y-m-d'); 
                                $limit = date('Y-m-d', strtotime('+30 days')); 
                                $checks = [
                                    'RCA' => $veh['expiry_rca'], 
                                    'ITP' => $veh['expiry_itp'], 
                                    'RO' => $veh['expiry_rovigneta'],
                                    'TRUSĂ' => $veh['medical_kit_expiry'],
                                    'STING.' => $veh['extinguisher_expiry']
                                ];
                                ?>
                                <?php foreach ($checks as $label => $val): ?>
                                    <?php 
                                        $isEquipment = in_array($label, ['TRUSĂ', 'STING.']);
                                        $isMissing = empty($val);
                                        $isExpiring = $val && $val <= $limit;
                                        $isExpired = $val && $val < $today;
                                    ?>
                                    <?php if ($isExpiring || ($isEquipment && $isMissing)): ?>
                                        <div class="px-2 py-1.5 rounded-lg border <?php echo ($isExpired || ($isEquipment && $isMissing)) ? 'bg-red-50 border-red-100 text-red-600' : 'bg-orange-50 border-orange-100 text-orange-600'; ?>">
                                            <div class="text-[9px] font-black uppercase tracking-widest"><?php echo $label; ?></div>
                                            <div class="text-[11px] font-bold">
                                                <?php 
                                                    if ($isMissing) echo 'NESETAT';
                                                    else echo date('d.m', strtotime($val)); 
                                                ?>
                                                <?php if ($isExpired) echo ' <span class="text-[8px]">EXP!</span>'; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Insight & Activity -->
    <div class="space-y-8">
        <!-- Live Active Trips -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-indigo-50/30">
                <h2 class="text-base font-bold text-slate-800 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-indigo-600 mr-2 animate-pulse"></span>
                    Live Traffic Activity
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[10px] uppercase font-black text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Driver</th>
                            <th class="px-5 py-3">Vehicle</th>
                            <th class="px-5 py-3 text-right">Started</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($activeTrips)): ?>
                            <tr><td colspan="3" class="px-5 py-8 text-center text-slate-400 italic">Nu sunt șoferi pe traseu acum.</td></tr>
                        <?php else: ?>
                            <?php foreach ($activeTrips as $trip): ?>
                                <tr>
                                    <td class="px-5 py-3 font-bold text-slate-700"><?php echo htmlspecialchars($trip['driver_name']); ?></td>
                                    <td class="px-5 py-3 text-slate-500 font-medium"><?php echo htmlspecialchars($trip['license_plate']); ?></td>
                                    <td class="px-5 py-3 text-right text-slate-400 text-xs"><?php echo date('H:i', strtotime($trip['start_time'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Costly Vehicles -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-base font-bold text-slate-800 mb-6 flex items-center uppercase tracking-wider">
                <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Budget Impact Vehicles (90d)
            </h2>
            <div class="space-y-6">
                <?php if (empty($topCostly)): ?>
                    <p class="text-sm text-slate-500 italic">No significant expenses match the period.</p>
                <?php else: ?>
                    <?php 
                    $maxCost = max(array_column($topCostly, 'total_cost')) ?: 1;
                    foreach ($topCostly as $v): 
                        $pct = ($v['total_cost'] / $maxCost) * 100;
                    ?>
                        <div>
                            <div class="flex justify-between items-baseline mb-2">
                                <span class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($v['license_plate']); ?></span>
                                <span class="text-sm font-black text-slate-900"><?php echo number_format($v['total_cost'], 0); ?> <span class="text-[10px] font-normal text-slate-400">RON</span></span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div style="width: <?php echo $pct; ?>%" class="bg-gradient-to-r from-rose-400 to-rose-600 h-full rounded-full"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
function truncate_text($text, $length) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}
?>
