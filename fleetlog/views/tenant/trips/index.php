<div class="mb-6 flex justify-between items-center px-4 md:px-0">
    <h1 class="text-2xl font-bold text-slate-800">Foi de parcurs (Trip Logs)</h1>
</div>

<!-- Desktop View: Table -->
<div class="hidden lg:block bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Start Trip</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">End Trip</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Distance</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                </tr>
            </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            <?php foreach ($trips as $trip): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-slate-900"><?php echo $trip['driver_name'] ?? 'Unknown'; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-slate-600 font-mono font-bold bg-slate-100 px-2 py-1 rounded border border-slate-200"><?php echo $trip['license_plate'] ?? 'Unknown'; ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-[10px] font-black uppercase tracking-widest rounded bg-slate-100 text-slate-500 border border-slate-200">
                            <?php echo $trip['type'] ?? 'ALTE'; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                        <div class="text-slate-400"><?php echo $trip['start_time']; ?></div>
                        <div class="font-black text-slate-700 text-sm"><?php echo number_format($trip['start_km']); ?> KM</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                        <div class="text-slate-400"><?php echo $trip['end_time'] ?? '-'; ?></div>
                        <div class="font-black text-slate-700 text-sm"><?php echo $trip['end_km'] ? number_format($trip['end_km']) . ' KM' : '-'; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-blue-600">
                        <?php echo $trip['end_km'] ? ($trip['end_km'] - $trip['start_km']) . ' KM' : '-'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-[10px] font-black uppercase tracking-widest leading-5 rounded-full <?php echo $trip['status'] === 'open' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800'; ?>">
                            <?php echo ucfirst($trip['status']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    </div>
</div>

<!-- Mobile View: Cards -->
<div class="lg:hidden space-y-4 px-4 pb-10">
    <?php foreach ($trips as $trip): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 space-y-4 relative overflow-hidden">
            <!-- Ribbon for status -->
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold">
                        <?php echo substr($trip['driver_name'] ?? 'U', 0, 1); ?>
                    </div>
                    <div>
                        <div class="font-black text-slate-800"><?php echo $trip['driver_name'] ?? 'Unknown'; ?></div>
                        <div class="text-xs font-mono text-blue-600 font-bold"><?php echo $trip['license_plate'] ?? 'Unknown'; ?></div>
                    </div>
                </div>
                <span class="px-2 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg <?php echo $trip['status'] === 'open' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800'; ?>">
                    <?php echo ucfirst($trip['status']); ?>
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 py-3 border-y border-slate-50">
                <div class="space-y-1">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pornire (Start)</div>
                    <div class="text-xs text-slate-500"><?php echo date('H:i', strtotime($trip['start_time'])); ?></div>
                    <div class="text-sm font-black text-slate-700"><?php echo number_format($trip['start_km']); ?> KM</div>
                </div>
                <div class="space-y-1 text-right">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sosire (End)</div>
                    <div class="text-xs text-slate-500"><?php echo $trip['end_time'] ? date('H:i', strtotime($trip['end_time'])) : '-'; ?></div>
                    <div class="text-sm font-black text-slate-700"><?php echo $trip['end_km'] ? number_format($trip['end_km']) . ' KM' : '-'; ?></div>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <span class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-500 rounded uppercase tracking-tighter">
                    Tip: <?php echo $trip['type'] ?? 'ALTE'; ?>
                </span>
                <div class="text-right">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Distanță Totală</div>
                    <div class="text-lg font-black text-blue-600">
                        <?php echo $trip['end_km'] ? ($trip['end_km'] - $trip['start_km']) . ' KM' : '-'; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($trips)): ?>
        <div class="py-12 bg-white rounded-2xl border border-dashed border-slate-300 text-center text-slate-400 italic">
            Nu există foi de parcurs înregistrate.
        </div>
    <?php endif; ?>
</div>
