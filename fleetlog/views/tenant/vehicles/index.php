<?php
// Ensure variables exist even if empty
$vehicles = $vehicles ?? [];
$archivedVehicles = $archivedVehicles ?? [];
?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Fleet Vehicles</h1>
    <a href="/tenant/vehicles/add" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors shadow flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Add Vehicle
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        Action completed successfully.
    </div>
<?php endif; ?>

<!-- Active Fleet Section -->
<div class="mb-10">
    <h2 class="text-lg font-bold text-slate-700 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        Active Fleet
    </h2>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Plate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">QR Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Odometer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Expiries (RCA/ITP)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php foreach ($vehicles as $vehicle): ?>
                    <tr class="hover:bg-slate-50 group transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-900"><?php echo $vehicle['make'] . ' ' . $vehicle['model']; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded font-mono text-xs font-bold ring-1 ring-slate-200">
                                <?php echo $vehicle['license_plate']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <div class="flex items-center space-x-3">
                                <?php 
                                    $qrUrl = "/qr/generate?sf=4&d=" . urlencode("https://" . ($_SERVER['HTTP_HOST'] ?? 'fleet.daserdesign.ro') . "/driver/start-trip?qr=" . $vehicle['qr_code']);
                                ?>
                                <a href="<?php echo $qrUrl; ?>" target="_blank" class="hover:scale-105 transition-transform inline-block group/qr" title="Click to view large QR code">
                                    <div class="relative">
                                        <img src="<?php echo $qrUrl; ?>" alt="QR" class="w-12 h-12 rounded border border-slate-200 shadow-sm bg-white p-0.5">
                                        <div class="absolute inset-0 bg-blue-600/0 group-hover/qr:bg-blue-600/10 rounded transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600 opacity-0 group-hover/qr:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </div>
                                    </div>
                                </a>
                                <div class="flex flex-col">
                                    <span class="font-mono text-[10px] text-slate-400 font-bold uppercase"><?php echo $vehicle['qr_code']; ?></span>
                                    <span class="text-[9px] text-slate-300">Scan to Start</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <?php echo number_format($vehicle['current_odometer']); ?> KM
                            <?php 
                            if (!empty($vehicle['next_service_km']) && $vehicle['next_service_km'] > 0) {
                                $kmLeft = $vehicle['next_service_km'] - $vehicle['current_odometer'];
                                if ($kmLeft <= 0) {
                                    echo '<div class="mt-1 flex items-center text-xs font-bold text-red-600"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Service Past Due</div>';
                                } elseif ($kmLeft <= 1000) {
                                    echo '<div class="mt-1 flex items-center text-xs font-bold text-amber-600"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Service Due: ' . number_format($kmLeft) . ' KM</div>';
                                } else {
                                    echo '<div class="mt-1 text-xs text-slate-400">Next Service: ' . number_format($vehicle['next_service_km']) . ' KM</div>';
                                }
                            }
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                            <div>RCA: <?php echo $vehicle['expiry_rca'] ?: 'N/A'; ?></div>
                            <div>ITP: <?php echo $vehicle['expiry_itp'] ?: 'N/A'; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-2">
                                <?php
                                    $status = $vehicle['status'] ?? 'active';
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-slate-100 text-slate-600 border border-slate-200',
                                        'service' => 'bg-orange-100 text-orange-800'
                                    ];
                                    $color = $statusColors[$status] ?? 'bg-slate-100 text-slate-800';
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full justify-center <?php echo $color; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                                
                                <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <?php if ($status !== 'active'): ?>
                                        <a href="/tenant/vehicles/status/<?php echo $vehicle['id']; ?>/active" title="Set Active" class="p-1 rounded bg-green-50 text-green-600 hover:bg-green-100 border border-green-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($status !== 'service'): ?>
                                        <a href="/tenant/vehicles/status/<?php echo $vehicle['id']; ?>/service" title="Set to Service" class="p-1 rounded bg-orange-50 text-orange-600 hover:bg-orange-100 border border-orange-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($status !== 'inactive'): ?>
                                        <a href="/tenant/vehicles/status/<?php echo $vehicle['id']; ?>/inactive" title="Set Inactive" class="p-1 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-300">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex flex-col items-end space-y-2 w-full">
                                <div class="flex items-center space-x-2">
                                    <a href="/tenant/expenses/add/<?php echo $vehicle['id']; ?>" class="text-green-700 hover:text-green-900 bg-green-50 px-3 py-1.5 rounded border border-green-200 hover:bg-green-100 transition-colors shadow-sm flex items-center" title="Add Expense/Service">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Expense
                                    </a>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="/tenant/vehicles/edit/<?php echo $vehicle['id']; ?>" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1.5 rounded border border-blue-100 hover:bg-blue-100 transition-colors">Edit</a>
                                    <a href="/tenant/vehicles/archive/<?php echo $vehicle['id']; ?>" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded border border-red-100 hover:bg-red-100 transition-colors" title="Write-off / Archive">
                                        <svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Archive
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($vehicles)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-500 italic">
                            No vehicles found in your active fleet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Archived Fleet Section -->
<?php if (!empty($archivedVehicles)): ?>
<div>
    <h2 class="text-lg font-bold text-red-700 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        Archived / Written-off Vehicles
    </h2>
    <div class="bg-red-50/50 rounded-xl shadow-sm border border-red-100 overflow-hidden opacity-90 hover:opacity-100 transition-opacity">
        <table class="min-w-full divide-y divide-red-200">
            <thead class="bg-red-100/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Plate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Final Odometer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Archive Notes / Explanation</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-red-200">
                <?php foreach ($archivedVehicles as $vehicle): ?>
                    <tr class="hover:bg-red-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-900 line-through decoration-slate-400"><?php echo $vehicle['make'] . ' ' . $vehicle['model']; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-white text-slate-700 rounded font-mono text-xs font-bold ring-1 ring-slate-200 opacity-70">
                                <?php echo $vehicle['license_plate']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <?php echo number_format($vehicle['current_odometer']); ?> KM
                        </td>
                        <td class="px-6 py-4 text-sm text-red-800 font-medium italic max-w-md break-words">
                            "<?php echo nl2br(htmlspecialchars($vehicle['archive_notes'] ?? 'No reason provided')); ?>"
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
