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

<?php if (isset($_GET['error'])): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center font-bold">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        Error: <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<!-- Active Fleet Section -->
<div class="mb-10">
    <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h2 class="text-lg font-bold text-slate-700 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Active Fleet
        </h2>
        <div class="relative w-full md:w-64">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" id="vehicleSearch" placeholder="Cauta masina sau nr..." 
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200" id="vehiclesTable">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Plate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">QR Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Odometer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Expiries (Act/Ec)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Inventar</th>
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
                        <td class="px-6 py-4 whitespace-nowrap text-[10px] text-slate-500">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between group/tip">
                                    <span class="font-bold">ACTE:</span>
                                    <span class="<?php echo ($vehicle['expiry_rca'] && $vehicle['expiry_rca'] < date('Y-m-d')) ? 'text-red-600 font-black' : ''; ?>"><?php echo $vehicle['expiry_rca'] ? date('d.m.y', strtotime($vehicle['expiry_rca'])) : 'N/A'; ?></span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-50 pt-1">
                                    <span class="font-bold">TRUSĂ:</span>
                                    <span class="<?php echo (!$vehicle['medical_kit_expiry'] || $vehicle['medical_kit_expiry'] < date('Y-m-d')) ? 'text-red-600 font-black' : 'text-blue-600'; ?>">
                                        <?php echo $vehicle['medical_kit_expiry'] ? date('d.m.y', strtotime($vehicle['medical_kit_expiry'])) : 'GOL'; ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold">STING:</span>
                                    <span class="<?php echo (!$vehicle['extinguisher_expiry'] || $vehicle['extinguisher_expiry'] < date('Y-m-d')) ? 'text-red-600 font-black' : 'text-blue-600'; ?>">
                                        <?php echo $vehicle['extinguisher_expiry'] ? date('d.m.y', strtotime($vehicle['extinguisher_expiry'])) : 'GOL'; ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-1.5">
                                <!-- Triangles -->
                                <div title="Triunghiuri: <?php echo $vehicle['has_triangles'] ?? 0; ?>" class="w-6 h-6 rounded flex items-center justify-center <?php echo ($vehicle['has_triangles'] ?? 0) > 0 ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-50 text-slate-300 border border-slate-100'; ?>">
                                    <span class="text-[9px] font-black"><?php echo ($vehicle['has_triangles'] ?? 0); ?>Δ</span>
                                </div>
                                <!-- Vests -->
                                <div title="Veste: <?php echo $vehicle['has_vest'] ?? 0; ?>" class="w-6 h-6 rounded flex items-center justify-center <?php echo ($vehicle['has_vest'] ?? 0) > 0 ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-slate-50 text-slate-300 border border-slate-100'; ?>">
                                    <span class="text-[9px] font-black"><?php echo ($vehicle['has_vest'] ?? 0); ?>V</span>
                                </div>
                                <!-- Jack -->
                                <div title="Cric: <?php echo !empty($vehicle['has_jack']) ? 'Da' : 'Nu'; ?>" class="w-6 h-6 rounded flex items-center justify-center <?php echo !empty($vehicle['has_jack']) ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-50 text-slate-300 border border-slate-100'; ?>">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                                <!-- Spare Wheel -->
                                <div title="Roată Rezervă: <?php echo (isset($vehicle['has_spare_wheel']) ? (bool)$vehicle['has_spare_wheel'] : true) ? 'Da' : 'Nu'; ?>" class="w-6 h-6 rounded flex items-center justify-center <?php echo (isset($vehicle['has_spare_wheel']) ? (bool)$vehicle['has_spare_wheel'] : true) ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-slate-50 text-slate-300 border border-slate-100'; ?>">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="relative inline-block text-left group/status">
                                <?php
                                    $status = $vehicle['status'] ?? 'active';
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800 border-green-200',
                                        'inactive' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        'service' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'archived' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                    $color = $statusColors[$status] ?? 'bg-slate-100 text-slate-800';
                                ?>
                                <button type="button" class="px-3 py-1.5 inline-flex items-center text-xs font-black rounded-xl justify-center border shadow-sm transition-all hover:shadow-md <?php echo $color; ?>">
                                    <?php echo ucfirst($status); ?>
                                    <svg class="ml-1.5 w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                
                                <div class="absolute left-0 mt-1 w-32 rounded-2xl bg-white shadow-xl border border-slate-100 opacity-0 invisible group-hover/status:opacity-100 group-hover/status:visible transition-all duration-200 z-50 p-1">
                                    <?php if ($status !== 'active'): ?>
                                        <a href="/tenant/vehicles/status/<?php echo $vehicle['id']; ?>/active" class="flex items-center px-3 py-2 text-[10px] font-black uppercase text-slate-600 hover:bg-green-50 hover:text-green-700 rounded-xl transition-colors">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span> Active
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($status !== 'service'): ?>
                                        <a href="/tenant/vehicles/status/<?php echo $vehicle['id']; ?>/service" class="flex items-center px-3 py-2 text-[10px] font-black uppercase text-slate-600 hover:bg-orange-50 hover:text-orange-700 rounded-xl transition-colors">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-2"></span> Service
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($status !== 'inactive'): ?>
                                        <a href="/tenant/vehicles/status/<?php echo $vehicle['id']; ?>/inactive" class="flex items-center px-3 py-2 text-[10px] font-black uppercase text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-colors">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-2"></span> Inactive
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex flex-col items-end space-y-2 w-full">
                                <div class="flex items-center space-x-2">
                                    <a href="/tenant/vehicles/mechanic-report/<?php echo $vehicle['id']; ?>" class="text-slate-600 hover:text-slate-900 bg-slate-50 px-3 py-1.5 rounded border border-slate-200 hover:bg-slate-100 transition-colors flex items-center" title="Mechanic Report">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Report
                                    </a>
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

<script>
document.getElementById('vehicleSearch')?.addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#vehiclesTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(term) || row.classList.contains('no-results')) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
