<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center">
        <a href="/tenant/reports/driver?period=<?php echo $period; ?>&month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>" class="mr-4 p-2 bg-white rounded-xl border border-slate-200 text-slate-400 hover:text-indigo-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800"><?php echo htmlspecialchars($driver['name']); ?></h1>
            <p class="text-slate-500 text-sm"><?php echo htmlspecialchars($driver['email'] ?? ''); ?> • <?php echo htmlspecialchars($driver['phone'] ?? ''); ?></p>
        </div>
    </div>
    
    <div class="flex flex-wrap items-center gap-2">
        <form class="flex items-center space-x-2 bg-white p-1 rounded-xl border border-slate-200">
            <input type="hidden" name="period" value="<?php echo $period; ?>">
            
            <?php if ($period === 'monthly'): ?>
                <select name="month" class="text-sm font-bold bg-transparent outline-none px-2 cursor-pointer border-r border-slate-100">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <?php $mPadded = str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                        <option value="<?php echo $mPadded; ?>" <?php echo $selected_month == $mPadded ? 'selected' : ''; ?>>
                            <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            <?php endif; ?>

            <select name="year" class="text-sm font-bold bg-transparent outline-none px-2 cursor-pointer">
                <?php for ($y = date('Y'); $y >= 2024; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $selected_year == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit" class="p-1 px-3 bg-slate-800 text-white rounded-lg text-xs font-bold hover:bg-black transition-colors">
                Filtrează
            </button>
        </form>

        <div class="flex items-center bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
            <?php foreach (['daily' => 'Azi', 'weekly' => 'Săpt.', 'monthly' => 'Lună', 'yearly' => 'An'] as $p => $label): ?>
                <a href="?period=<?php echo $p; ?>&month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all <?php echo $period === $p ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50'; ?>">
                    <?php echo $label; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-1">Distanță Totală</div>
        <div class="flex items-baseline">
            <span class="text-3xl font-black text-slate-800"><?php echo number_format($stats['total_km'] ?? 0); ?></span>
            <span class="text-xs font-bold text-slate-400 ml-2 uppercase">KM</span>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-1">Consum Total</div>
        <div class="flex items-baseline">
            <span class="text-3xl font-black text-slate-800"><?php echo number_format($stats['total_liters'] ?? 0, 1); ?></span>
            <span class="text-xs font-bold text-slate-400 ml-2 uppercase">Litri</span>
        </div>
        <div class="text-[10px] font-bold text-slate-400 mt-1"><?php echo number_format($stats['total_fuel_cost'] ?? 0, 2); ?> RON Cheltuiți</div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-1">Număr Curse</div>
        <div class="flex items-baseline">
            <span class="text-3xl font-black text-slate-800"><?php echo $stats['trip_count'] ?? 0; ?></span>
            <span class="text-xs font-bold text-slate-400 ml-2 uppercase">Foi Parcurs</span>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-1">Consum Mediu</div>
        <?php 
            $avg = 0;
            if (($stats['total_km'] ?? 0) > 0 && ($stats['total_liters'] ?? 0) > 0) {
                $avg = ($stats['total_liters'] / $stats['total_km']) * 100;
            }
        ?>
        <div class="flex items-baseline">
            <span class="text-3xl font-black <?php echo $avg > 12 ? 'text-amber-600' : 'text-emerald-600'; ?>">
                <?php echo $avg > 0 ? number_format($avg, 2) : '---'; ?>
            </span>
            <span class="text-xs font-bold text-slate-400 ml-2 uppercase">L/100KM</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- KM by Type Breakdown -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500"><?php echo __('km_by_type'); ?></h3>
            </div>
            <div class="p-6 space-y-6">
                <?php if (empty($kmByType)): ?>
                    <p class="text-slate-400 italic text-sm text-center py-4">Fără date disponibile.</p>
                <?php else: ?>
                    <?php 
                    $maxKm = 0;
                    foreach($kmByType as $k) if($k['km'] > $maxKm) $maxKm = $k['km']; 
                    ?>
                    <?php foreach ($kmByType as $k): ?>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold text-slate-700">
                                    <?php echo __('type_' . strtolower($k['type'])) ?? $k['type']; ?>
                                </span>
                                <span class="text-sm font-black text-indigo-600"><?php echo number_format($k['km']); ?> KM</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: <?php echo $maxKm > 0 ? ($k['km'] / $maxKm * 100) : 0; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Trip History Table -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500"><?php echo __('trip_history'); ?></h3>
                <span class="text-[10px] font-bold bg-white px-2 py-1 rounded border border-slate-200 text-slate-500">
                    <?php echo count($trips); ?> înregistrări
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Vehicul</th>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tip</th>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Perioadă</th>
                            <th class="px-6 py-3 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Distanță</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($trips as $trip): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded">
                                        <?php echo htmlspecialchars($trip['license_plate']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-[10px] font-bold uppercase py-1 px-2 rounded bg-indigo-50 text-indigo-600">
                                        <?php echo __('type_' . strtolower($trip['type'])) ?? $trip['type']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-slate-600"><?php echo date('d M Y, H:i', strtotime($trip['start_time'])); ?></div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        <?php echo $trip['end_time'] ? 'Până la ' . date('H:i', strtotime($trip['end_time'])) : 'În cursă...'; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <?php if ($trip['status'] === 'closed'): ?>
                                        <span class="text-sm font-black text-slate-800"><?php echo number_format($trip['end_km'] - $trip['start_km']); ?> KM</span>
                                    <?php else: ?>
                                        <span class="text-[10px] font-black text-blue-600 uppercase">Activ</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($trips)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">Nu există curse înregistrate.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
