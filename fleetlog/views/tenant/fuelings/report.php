<div class="mb-6 flex items-center justify-between no-print">
    <div>
        <a href="/tenant/fuelings?month=<?php echo $month; ?>&year=<?php echo $year; ?>" class="text-blue-600 hover:underline text-sm flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Fueling Logs
        </a>
        <h1 class="text-2xl font-bold text-slate-800 uppercase">Monthly Fueling Report / Raport Lunar Alimentări</h1>
    </div>
    <button onclick="window.print()" class="px-6 py-3 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-900 transition-all shadow-md flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Printează / PDF
    </button>
</div>

<div class="print-area bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
    <div class="flex justify-between items-start border-b-2 border-slate-800 pb-6 mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 mb-1">
                <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?>
            </h2>
            <p class="text-xl text-slate-600 uppercase tracking-widest font-bold">RAPORT ALIMENTĂRI LUNAR</p>
        </div>
        <div class="text-right">
            <div class="text-sm text-slate-500 uppercase font-bold">Total Fuelings</div>
            <div class="text-2xl font-mono font-bold text-slate-900"><?php echo count($fuelings); ?> entries</div>
        </div>
    </div>

    <?php if (empty($fuelings)): ?>
        <div class="p-10 text-center text-slate-400 italic bg-slate-50 rounded-xl border border-dashed border-slate-200">
            No fuelings recorded for this period.
        </div>
    <?php else: ?>
        <table class="w-full border-collapse mb-10">
            <thead>
                <tr class="border-b-2 border-slate-300">
                    <th class="py-3 px-2 text-left text-xs font-bold uppercase text-slate-500">Date/Time</th>
                    <th class="py-3 px-2 text-left text-xs font-bold uppercase text-slate-500">Vehicle</th>
                    <th class="py-3 px-2 text-left text-xs font-bold uppercase text-slate-500">Driver</th>
                    <th class="py-3 px-2 text-right text-xs font-bold uppercase text-slate-500">Odometer</th>
                    <th class="py-3 px-2 text-right text-xs font-bold uppercase text-slate-500">Liters</th>
                    <th class="py-3 px-2 text-right text-xs font-bold uppercase text-slate-500">Cost</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php 
                $totalLiters = 0;
                $totalCost = 0;
                foreach ($fuelings as $f): 
                    $totalLiters += $f['liters'];
                    $totalCost += $f['total_price'];
                ?>
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 px-2 text-sm text-slate-700"><?php echo date('d.m.Y H:i', strtotime($f['created_at'])); ?></td>
                        <td class="py-4 px-2 text-sm font-bold text-slate-900"><?php echo htmlspecialchars($f['license_plate']); ?></td>
                        <td class="py-4 px-2 text-sm text-slate-600"><?php echo htmlspecialchars($f['driver_name']); ?></td>
                        <td class="py-4 px-2 text-sm text-right font-mono"><?php echo number_format($f['odometer']); ?> KM</td>
                        <td class="py-4 px-2 text-sm text-right font-bold"><?php echo number_format($f['liters'], 2); ?> L</td>
                        <td class="py-4 px-2 text-sm text-right font-bold text-blue-700"><?php echo number_format($f['total_price'], 2); ?> RON</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="border-t-2 border-slate-800">
                <tr>
                    <td colspan="4" class="py-4 px-2 text-right text-sm font-bold uppercase text-slate-500">Monthly Totals</td>
                    <td class="py-4 px-2 text-right text-lg font-black text-slate-900"><?php echo number_format($totalLiters, 2); ?> L</td>
                    <td class="py-4 px-2 text-right text-lg font-black text-blue-700"><?php echo number_format($totalCost, 2); ?> RON</td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <div class="mt-12 text-center text-[10px] text-slate-400 uppercase tracking-widest border-t border-slate-100 pt-6">
        Generat automat de FleetLog App • <?php echo date('d.m.Y H:i'); ?>
    </div>
</div>

<style>
@media print {
    body { background: white !important; font-size: 12px; }
    .no-print { display: none !important; }
    .print-area { border: none !important; box-shadow: none !important; padding: 0 !important; width: 100% !important; }
    .main-content { padding: 0 !important; margin: 0 !important; }
    header, aside { display: none !important; }
    table { page-break-inside: auto; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    .break-inside-avoid { page-break-inside: avoid; }
}
</style>
