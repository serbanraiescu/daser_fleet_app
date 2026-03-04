<?php
// View Switcher logic
$view = $_GET['v'] ?? 'table'; // options: table, cards, explorer
$vehicles = $vehicles ?? [];
$archivedVehicles = $archivedVehicles ?? [];
?>

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Fleet Management</h1>
        <p class="text-sm text-slate-500 font-medium italic">Manage your active and archived vehicles</p>
    </div>
    
    <div class="flex items-center bg-white p-1 rounded-xl shadow-sm border border-slate-200">
        <a href="?v=table" class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all <?php echo $view === 'table' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-50'; ?>">Table</a>
        <a href="?v=cards" class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all <?php echo $view === 'cards' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-50'; ?>">Cards</a>
        <a href="?v=explorer" class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all <?php echo $view === 'explorer' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-50'; ?>">Explorer</a>
        <div class="w-px h-4 bg-slate-200 mx-2"></div>
        <a href="/tenant/vehicles/add" class="bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors shadow flex items-center text-xs font-black uppercase tracking-widest">
             Add New
        </a>
    </div>
</div>

<?php if ($view === 'table'): ?>
    <!-- VARIANT 1: REFINED TABLE (CURRENT) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50/50">
                <tr>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Vehicle & Plate</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Access (QR)</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Odometer</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Document Status</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Equipment</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Management</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach ($vehicles as $vehicle): ?>
                    <tr class="hover:bg-slate-50/50 group transition-all">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-black text-slate-900"><?php echo $vehicle['make'] . ' ' . $vehicle['model']; ?></div>
                            <div class="mt-1">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[10px] font-black tracking-tighter ring-1 ring-slate-200 uppercase">
                                    <?php echo $vehicle['license_plate']; ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <?php $qrUrl = "/qr/generate?sf=4&d=" . urlencode("https://" . ($_SERVER['HTTP_HOST'] ?? 'fleet.daserdesign.ro') . "/driver/start-trip?qr=" . $vehicle['qr_code']); ?>
                                <img src="<?php echo $qrUrl; ?>" alt="QR" class="w-10 h-10 rounded border border-slate-200 p-0.5 bg-white">
                                <span class="font-mono text-[9px] text-slate-400 font-bold uppercase tracking-tighter"><?php echo $vehicle['qr_code']; ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-slate-700"><?php echo number_format($vehicle['current_odometer']); ?> <span class="text-[10px] font-normal text-slate-400 uppercase">KM</span></div>
                            <?php 
                            if (!empty($vehicle['next_service_km']) && $vehicle['next_service_km'] > 0) {
                                $kmLeft = $vehicle['next_service_km'] - $vehicle['current_odometer'];
                                $color = $kmLeft <= 0 ? 'text-red-500' : ($kmLeft <= 1000 ? 'text-amber-500' : 'text-slate-400');
                                echo '<div class="text-[9px] font-black uppercase mb-[-2px] '.$color.'">Service: '.($kmLeft <= 0 ? 'PAST DUE' : number_format($kmLeft).' KM').'</div>';
                            }
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                <?php 
                                $limit = date('Y-m-d', strtotime('+30 days'));
                                $today = date('Y-m-d');
                                $docs = [
                                    ['L' => 'RCA', 'V' => $vehicle['expiry_rca']],
                                    ['L' => 'ITP', 'V' => $vehicle['expiry_itp']],
                                    ['L' => 'TRUSA', 'V' => $vehicle['medical_kit_expiry']],
                                    ['L' => 'STING', 'V' => $vehicle['extinguisher_expiry']]
                                ];
                                ?>
                                <?php foreach ($docs as $d): ?>
                                    <?php 
                                        $isExp = $d['V'] && $d['V'] <= $limit;
                                        $isPast = $d['V'] && $d['V'] < $today;
                                        $isMissing = in_array($d['L'], ['TRUSA', 'STING']) && empty($d['V']);
                                    ?>
                                    <span title="<?php echo $d['V'] ?? 'Not set'; ?>" class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter <?php echo ($isPast || $isMissing) ? 'bg-red-50 text-red-600 ring-1 ring-red-100' : ($isExp ? 'bg-orange-50 text-orange-600 ring-1 ring-orange-100' : 'bg-slate-50 text-slate-400 ring-1 ring-slate-100'); ?>">
                                        <?php echo $d['L']; ?><?php echo $isMissing ? ': GOL' : ''; ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-1">
                                <div title="Triunghiuri" class="w-6 h-6 rounded flex items-center justify-center <?php echo ($vehicle['has_triangles'] ?? 0) > 0 ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-50 text-slate-300 border border-slate-100'; ?>"><span class="text-[8px] font-bold"><?php echo ($vehicle['has_triangles'] ?? 0); ?>Δ</span></div>
                                <div title="Vesta" class="w-6 h-6 rounded flex items-center justify-center <?php echo ($vehicle['has_vest'] ?? 0) > 0 ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-slate-50 text-slate-300 border border-slate-100'; ?>"><span class="text-[8px] font-bold"><?php echo ($vehicle['has_vest'] ?? 0); ?>V</span></div>
                                <div title="Roata" class="w-6 h-6 rounded flex items-center justify-center <?php echo (isset($vehicle['has_spare_wheel']) ? (bool)$vehicle['has_spare_wheel'] : true) ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-slate-50 text-slate-300 border border-slate-100'; ?>"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                             <div class="flex justify-end space-x-1">
                                <a href="/tenant/vehicles/edit/<?php echo $vehicle['id']; ?>" class="p-2 transition-colors hover:bg-slate-100 rounded-lg text-slate-400 hover:text-blue-600 border border-transparent hover:border-slate-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <a href="/tenant/vehicles/mechanic-report/<?php echo $vehicle['id']; ?>" class="p-2 transition-colors hover:bg-slate-100 rounded-lg text-slate-400 hover:text-emerald-600 border border-transparent hover:border-slate-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </a>
                             </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($view === 'cards'): ?>
    <!-- VARIANT 2: CARD GRID (MODERN) -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($vehicles as $vehicle): ?>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-all transform hover:-translate-y-1 group">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="text-[10px] font-black uppercase text-slate-400 tracking-widest leading-none mb-1"><?php echo $vehicle['make']; ?></div>
                            <h3 class="text-xl font-black text-slate-900"><?php echo $vehicle['model']; ?></h3>
                            <span class="mt-2 inline-block px-3 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-black tracking-widest uppercase ring-1 ring-slate-200">
                                <?php echo $vehicle['license_plate']; ?>
                            </span>
                        </div>
                        <div class="relative group/qr">
                            <?php $qrUrl = "/qr/generate?sf=4&d=" . urlencode("https://" . ($_SERVER['HTTP_HOST'] ?? 'fleet.daserdesign.ro') . "/driver/start-trip?qr=" . $vehicle['qr_code']); ?>
                            <img src="<?php echo $qrUrl; ?>" alt="QR" class="w-16 h-16 rounded-2xl border border-slate-100 p-1 bg-white shadow-sm transition-transform group-hover/qr:scale-110">
                            <div class="absolute -bottom-2 -right-2 bg-blue-600 text-white text-[8px] font-black px-2 py-0.5 rounded-full shadow-sm">SCAN</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-4 border-y border-slate-50 my-4">
                        <div>
                            <div class="text-[9px] font-black text-slate-400 uppercase mb-1">Current Odo</div>
                            <div class="text-base font-black text-slate-800"><?php echo number_format($vehicle['current_odometer']); ?> <span class="text-[10px] text-slate-400 font-normal">KM</span></div>
                        </div>
                        <div class="text-right">
                             <div class="text-[9px] font-black text-slate-400 uppercase mb-1">Status</div>
                             <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-600 text-[10px] font-black uppercase border border-green-100">Live</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-slate-500">Document Expiries:</span>
                            <div class="flex -space-x-1">
                                <?php foreach (['RCA' => $vehicle['expiry_rca'], 'ITP' => $vehicle['expiry_itp']] as $l => $v): ?>
                                    <div title="<?php echo $l; ?>: <?php echo $v; ?>" class="w-6 h-6 rounded-full border-2 border-white <?php echo $v < date('Y-m-d') ? 'bg-red-500' : 'bg-green-500'; ?> flex items-center justify-center text-[8px] text-white font-black"><?php echo substr($l, 0, 1); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                             <?php 
                                $inv = [
                                    ['L' => 'Δ', 'H' => ($vehicle['has_triangles'] ?? 0) > 0],
                                    ['L' => 'V', 'H' => ($vehicle['has_vest'] ?? 0) > 0],
                                    ['L' => 'W', 'H' => (isset($vehicle['has_spare_wheel']) ? (bool)$vehicle['has_spare_wheel'] : true)],
                                ];
                             ?>
                             <?php foreach($inv as $i): ?>
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center border <?php echo $i['H'] ? 'bg-blue-50 border-blue-100 text-blue-600' : 'bg-slate-50 border-slate-100 text-slate-300'; ?>">
                                    <span class="text-[10px] font-black"><?php echo $i['L']; ?></span>
                                </div>
                             <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 flex justify-between items-center group-hover:bg-blue-50/50 transition-colors">
                    <a href="/tenant/vehicles/mechanic-report/<?php echo $vehicle['id']; ?>" class="text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-blue-600">Mechanic Report</a>
                    <a href="/tenant/vehicles/edit/<?php echo $vehicle['id']; ?>" class="bg-white text-blue-600 px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-200 shadow-sm hover:border-blue-300 transition-colors">Quick Edit</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php elseif ($view === 'explorer'): ?>
    <!-- VARIANT 3: SPLIT EXPLORER (PRO) -->
    <div class="flex flex-col lg:flex-row bg-white rounded-3xl shadow-sm border border-slate-200 h-[600px] overflow-hidden">
        <!-- Sidebar -->
        <div class="w-full lg:w-80 border-r border-slate-100 flex flex-col">
            <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Units (<?php echo count($vehicles); ?>)</span>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-slate-50">
                <?php foreach ($vehicles as $index => $vehicle): ?>
                    <button onclick="window.location.href='?v=explorer&id=<?php echo $vehicle['id']; ?>'" 
                        class="w-full text-left p-4 hover:bg-blue-50 transition-colors flex items-center justify-between group <?php echo (($_GET['id'] ?? $vehicles[0]['id']) == $vehicle['id']) ? 'bg-blue-50/70 border-r-4 border-blue-600' : ''; ?>">
                        <div>
                            <div class="text-xs font-black text-slate-700 uppercase tracking-tighter"><?php echo $vehicle['license_plate']; ?></div>
                            <div class="text-[10px] text-slate-400 font-bold"><?php echo $vehicle['make'] . ' ' . $vehicle['model']; ?></div>
                        </div>
                        <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Main Panel -->
        <?php 
            $selectedId = $_GET['id'] ?? ($vehicles[0]['id'] ?? null);
            $v = null;
            if ($selectedId) {
                foreach($vehicles as $item) { if($item['id'] == $selectedId) { $v = $item; break; } }
            }
        ?>
        <div class="flex-1 bg-slate-50/30 p-8 overflow-y-auto">
            <?php if ($v): ?>
                <div class="max-w-3xl">
                    <div class="flex flex-col md:flex-row justify-between items-start mb-10">
                        <div>
                            <h2 class="text-4xl font-black text-slate-900 tracking-tighter mb-2"><?php echo $v['make'] . ' ' . $v['model']; ?></h2>
                            <div class="flex items-center space-x-3">
                                <span class="px-4 py-1.5 bg-slate-900 text-white rounded-xl text-lg font-black tracking-widest uppercase shadow-lg"><?php echo $v['license_plate']; ?></span>
                                <span class="px-3 py-1 bg-green-100 text-green-700 border border-green-200 rounded-lg text-xs font-black uppercase tracking-widest">AVAILABLE</span>
                            </div>
                        </div>
                        <div class="mt-6 md:mt-0 p-3 bg-white border border-slate-200 rounded-3xl shadow-lg group">
                             <?php $qrUrl = "/qr/generate?sf=6&d=" . urlencode("https://" . ($_SERVER['HTTP_HOST'] ?? 'fleet.daserdesign.ro') . "/driver/start-trip?qr=" . $v['qr_code']); ?>
                             <img src="<?php echo $qrUrl; ?>" alt="QR" class="w-32 h-32 rounded-2xl mb-2">
                             <div class="text-center font-mono text-xs font-black text-slate-400 uppercase tracking-widest"><?php echo $v['qr_code']; ?></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Core Inventory Checklist</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-xs font-bold text-slate-700">Warning Triangles</span>
                                    <span class="px-2 bg-blue-100 text-blue-700 rounded text-[10px] font-black"><?php echo $v['has_triangles']; ?> x</span>
                                </div>
                                <div class="flex justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-xs font-bold text-slate-700">Reflective Vests</span>
                                    <span class="px-2 bg-amber-100 text-amber-700 rounded text-[10px] font-black"><?php echo $v['has_vest']; ?> x</span>
                                </div>
                                <div class="flex justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="text-xs font-bold text-slate-700">Operating Jack</span>
                                    <span class="px-2 <?php echo $v['has_jack'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'; ?> rounded text-[10px] font-black"><?php echo $v['has_jack'] ? 'PRESENT' : 'MISSING'; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                             <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Critical Expirations</h4>
                             <div class="space-y-3">
                                <?php foreach(['expiry_rca' => 'RCA Policy', 'expiry_itp' => 'ITP Inspection', 'medical_kit_expiry' => 'Medical Kit'] as $k => $l): ?>
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                        <div class="text-xs font-bold text-slate-700"><?php echo $l; ?></div>
                                        <div class="text-xs font-black <?php echo ($v[$k] && $v[$k] < date('Y-m-d')) ? 'text-red-600' : 'text-slate-900'; ?>">
                                            <?php echo !empty($v[$k]) ? date('d M Y', strtotime($v[$k])) : 'NOT SET'; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                             </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="h-full flex flex-col items-center justify-center text-slate-300">
                    <svg class="w-16 h-16 mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <p class="font-bold uppercase tracking-widest text-[10px]">Select a vehicle from the list to explore details</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Archived Fleet (Only in Table View) -->
<?php if ($view === 'table' && !empty($archivedVehicles)): ?>
<div class="mt-12">
    <h2 class="text-sm font-black text-red-600 mb-4 flex items-center uppercase tracking-widest">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        Archived / Written-off Units
    </h2>
    <div class="bg-red-50/30 rounded-2xl border border-red-100 overflow-hidden opacity-60 hover:opacity-100 transition-opacity">
        <table class="min-w-full divide-y divide-red-100">
            <thead class="bg-red-100/30">
                <tr>
                    <th class="px-6 py-3 text-left text-[10px] font-black text-red-800 uppercase tracking-widest">Vehicle</th>
                    <th class="px-6 py-3 text-left text-[10px] font-black text-red-800 uppercase tracking-widest">Final Odo</th>
                    <th class="px-6 py-3 text-left text-[10px] font-black text-red-800 uppercase tracking-widest">Reason / Archive Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-red-50">
                <?php foreach ($archivedVehicles as $vehicle): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-slate-900 line-through decoration-slate-300"><?php echo $vehicle['make'] . ' ' . $vehicle['model']; ?></div>
                            <span class="text-[9px] font-black text-slate-400 uppercase"><?php echo $vehicle['license_plate']; ?></span>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-slate-500"><?php echo number_format($vehicle['current_odometer']); ?> KM</td>
                        <td class="px-6 py-4 text-[11px] text-red-800 font-medium italic">"<?php echo htmlspecialchars($vehicle['archive_notes'] ?? 'N/A'); ?>"</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
