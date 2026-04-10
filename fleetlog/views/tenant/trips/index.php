<div class="mb-6 space-y-4 px-4 md:px-0">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <h1 class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter">Foi de parcurs</h1>
        
        <form method="GET" class="flex items-center gap-2 bg-white p-2 rounded-xl border border-slate-200 shadow-sm focus-within:ring-2 focus-within:ring-blue-500/20 transition-all">
            <span class="pl-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </span>
            <input type="date" name="date" value="<?php echo $selectedDate; ?>" onchange="this.form.submit()"
                   class="text-sm border-none focus:ring-0 text-slate-600 font-black bg-transparent">
        </form>
    </div>

    <!-- Mini Report Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Kilometri</div>
                <div class="text-xl font-black text-slate-800"><?php echo number_format($stats['total_km'], 1); ?> <span class="text-xs font-normal">KM</span></div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Curse Deschise</div>
                <div class="text-xl font-black text-slate-800"><?php echo $stats['open_trips']; ?></div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2 2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Curse Zi</div>
                <div class="text-xl font-black text-slate-800"><?php echo $stats['total_trips']; ?></div>
            </div>
        </div>
    </div>

    <?php if (!empty($pendingDays)): ?>
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-2xl shadow-sm flex items-start space-x-3 transition-all animate-pulse">
            <div class="flex-shrink-0 pt-0.5">
                <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
                <h3 class="text-sm font-black text-orange-800 uppercase tracking-widest">Atenție: Curse uitate deschise!</h3>
                <div class="mt-1 text-xs font-bold text-orange-700">
                    Sunt curse care nu au fost închise în următoarele zile: 
                    <div class="flex flex-wrap gap-2 mt-2">
                        <?php foreach ($pendingDays as $day): ?>
                            <a href="?date=<?php echo $day; ?>" class="px-2 py-1 bg-orange-100 border border-orange-200 rounded text-[10px] font-black hover:bg-orange-200 transition-colors">
                                <?php echo date('d.m.Y', strtotime($day)); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Desktop View: Table -->
<div class="hidden lg:block bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mx-4 md:mx-0">
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
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
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
                            <div class="text-slate-400"><?php echo date('H:i', strtotime($trip['start_time'])); ?></div>
                            <div class="font-black text-slate-700 text-sm"><?php echo number_format($trip['start_km']); ?> KM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                            <div class="text-slate-400"><?php echo $trip['end_time'] ? date('H:i', strtotime($trip['end_time'])) : '-'; ?></div>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="/tenant/trips/edit/<?php echo $trip['id']; ?>" class="text-blue-600 hover:text-blue-900 inline-flex items-center text-xs font-bold uppercase tracking-wider">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                Edit
                            </a>
                            <?php if ($trip['status'] === 'open'): ?>
                            <a href="/tenant/trips/close/<?php echo $trip['id']; ?>" onclick="return confirm('Dorești să închizi această cursă cu kilometrajul actual al vehiculului?')" class="text-orange-600 hover:text-orange-900 inline-flex items-center text-xs font-bold uppercase tracking-wider">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Close
                            </a>
                            <?php endif; ?>
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

            <div class="flex justify-between items-center pt-2 gap-2">
                <a href="/tenant/trips/edit/<?php echo $trip['id']; ?>" class="flex-1 text-center py-2 text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 rounded-lg border border-blue-100">
                    Editează
                </a>
                <?php if ($trip['status'] === 'open'): ?>
                <a href="/tenant/trips/close/<?php echo $trip['id']; ?>" onclick="return confirm('Închide cursa?')" class="flex-1 text-center py-2 text-[10px] font-black uppercase tracking-widest text-orange-600 bg-orange-50 rounded-lg border border-orange-100">
                    Închide
                </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($trips)): ?>
        <div class="py-12 bg-white rounded-2xl border border-dashed border-slate-300 text-center text-slate-400 italic mx-4">
            Nu există foi de parcurs înregistrate pentru această zi.
        </div>
    <?php endif; ?>
</div>
