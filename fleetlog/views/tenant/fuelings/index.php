<div class="mb-6 flex flex-wrap justify-between items-center gap-4">
    <h1 class="text-2xl font-bold text-slate-800">Fueling Logs</h1>
    
    <div class="flex items-center gap-3">
        <form method="GET" class="flex items-center gap-2 bg-white p-2 rounded-xl border border-slate-200 shadow-sm">
            <select name="month" class="text-sm border-none focus:ring-0 text-slate-600 font-bold bg-transparent">
                <?php for($m=1; $m<=12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php echo $m == $selected_month ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                    </option>
                <?php endfor; ?>
            </select>
            <select name="year" class="text-sm border-none focus:ring-0 text-slate-600 font-bold bg-transparent">
                <?php for($y=date('Y'); $y>=2024; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $y == $selected_year ? 'selected' : ''; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="p-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-600 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>

        <a href="/tenant/fuelings/report?month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>" class="px-5 py-2.5 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-900 transition-all shadow-md flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6-9a3 3 0 116 0 3 3 0 01-6 0zm-3 9h12"></path></svg>
            Print Monthly Report (Summary)
        </a>

        <a href="/tenant/fuelings/receipts?month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>" class="px-5 py-2.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all shadow-md flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h14a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Print Receipts (Individual)
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- Mobile Fueling Cards -->
    <div class="md:hidden divide-y divide-slate-100">
        <?php foreach ($fuelings as $log): ?>
            <div class="p-4 hover:bg-slate-50 transition-colors">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1"><?php echo date('d M Y H:i', strtotime($log['created_at'])); ?></div>
                        <div class="flex items-center">
                            <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-xs border border-slate-200 mr-2"><?php echo $log['license_plate']; ?></span>
                            <?php if ($log['is_full']): ?>
                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[8px] font-black uppercase rounded border border-blue-200">Full</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black text-blue-600"><?php echo number_format($log['total_price'], 2); ?> <span class="text-[9px] font-normal text-slate-400 uppercase">RON</span></div>
                        <div class="text-[10px] font-bold text-slate-500"><?php echo $log['liters']; ?> L</div>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-4 pt-3 border-t border-slate-50">
                    <div class="text-[10px] text-slate-500 italic">
                        By <span class="font-bold text-slate-700"><?php echo htmlspecialchars($log['driver_name']); ?></span>
                    </div>
                    <?php if ($log['receipt_photo']): ?>
                        <a href="/<?php echo $log['receipt_photo']; ?>" target="_blank" class="text-[10px] font-black uppercase text-blue-600 bg-blue-50 px-2 py-1 rounded-lg border border-blue-100">
                            Vezi Poza
                        </a>
                    <?php endif; ?>
                    <a href="/tenant/fuelings/edit/<?php echo $log['id']; ?>" class="text-[10px] font-black uppercase text-slate-600 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                        Editează
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($fuelings)): ?>
            <div class="px-6 py-10 text-center text-slate-400 italic">Nicio alimentare înregistrată.</div>
        <?php endif; ?>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Odometer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Fuel Info</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Full?</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php foreach ($fuelings as $log): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <?php echo date('d M Y H:i', strtotime($log['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                            <?php echo $log['license_plate']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <?php echo $log['driver_name']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <?php echo number_format($log['odometer']); ?> KM
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?php echo $log['liters']; ?> Liters
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                            <?php echo number_format($log['total_price'], 2); ?> RON
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php if ($log['is_full']): ?>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-[10px] font-bold uppercase rounded">Full</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-slate-100 text-slate-400 text-[10px] font-bold uppercase rounded">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-3">
                            <?php if ($log['receipt_photo']): ?>
                                <a href="/<?php echo $log['receipt_photo']; ?>" target="_blank" class="text-blue-600 hover:text-blue-900 font-bold text-xs uppercase underline">
                                    Photo
                                </a>
                            <?php endif; ?>
                            <a href="/tenant/fuelings/edit/<?php echo $log['id']; ?>" class="text-slate-600 hover:text-slate-900 font-bold text-xs uppercase bg-slate-50 px-2 py-1 rounded border border-slate-100">
                                Edit
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($fuelings)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-500 italic">
                            No fueling logs recorded yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
