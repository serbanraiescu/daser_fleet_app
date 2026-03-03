<div class="mb-6 flex items-center justify-between no-print">
    <div>
        <a href="/tenant/expenses" class="text-blue-600 hover:underline text-sm flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Expenses
        </a>
        <h1 class="text-2xl font-bold text-slate-800 uppercase">Mechanic Report / Fișă Service</h1>
    </div>
    <button onclick="window.print()" class="px-6 py-3 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-900 transition-all shadow-md flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Printează / PDF
    </button>
</div>

<div class="print-area bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
    <!-- Header Info -->
    <div class="flex flex-wrap justify-between items-start border-b-2 border-slate-800 pb-6 mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 mb-1"><?php echo htmlspecialchars($vehicle['license_plate']); ?></h2>
            <p class="text-xl text-slate-600"><?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model']); ?></p>
        </div>
        <div class="text-right">
            <div class="text-sm text-slate-500 uppercase font-bold">Current Odometer</div>
            <div class="text-2xl font-mono font-bold text-slate-900"><?php echo number_format($vehicle['current_odometer']); ?> KM</div>
            
            <div class="text-sm text-slate-500 uppercase font-bold mt-4">Next Service Scheduled at</div>
            <div class="text-xl font-mono font-bold text-blue-600"><?php echo number_format($vehicle['next_service_km']); ?> KM</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Maintenance History -->
        <div>
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center uppercase tracking-wider">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Istoric Intervenții / Service History
            </h3>
            
            <?php if (empty($history)): ?>
                <div class="p-6 bg-slate-50 rounded-xl border border-dashed border-slate-300 text-center text-slate-500">
                    Nu există istoric înregistrat.
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($history as $exp): ?>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xs font-bold text-slate-500"><?php echo date('d.m.Y', strtotime($exp['expense_date'])); ?></span>
                                <span class="text-xs font-mono font-bold text-slate-400"><?php echo number_format($exp['odometer_at_expense']); ?> KM</span>
                            </div>
                            <div class="font-bold text-slate-800"><?php echo htmlspecialchars($exp['name']); ?></div>
                            <span class="inline-block mt-1 px-2 py-0.5 bg-white border border-slate-200 rounded text-[10px] font-bold uppercase text-slate-600">
                                <?php echo $exp['expense_type']; ?>
                            </span>
                            <?php if (!empty($exp['notes'])): ?>
                                <p class="mt-2 text-sm text-slate-600 italic border-l-2 border-slate-300 pl-3">
                                    <?php echo nl2br(htmlspecialchars($exp['notes'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Active Damges / Issues -->
        <div>
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center uppercase tracking-wider">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Defecțiuni Raportate / Reported Issues
            </h3>
            
            <?php if (empty($activeDamages)): ?>
                <div class="p-6 bg-green-50 rounded-xl border border-dashed border-green-200 text-center text-green-700">
                    Nicio problemă tehnică sau daună raportată activă.
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($activeDamages as $dmg): ?>
                        <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-xs font-bold text-red-400"><?php echo date('d.m.Y H:i', strtotime($dmg['datetime'])); ?></span>
                                <span class="px-2 py-0.5 bg-white text-[10px] font-extrabold rounded text-red-600 border border-red-200 uppercase">
                                    <?php echo $dmg['severity']; ?>
                                </span>
                            </div>
                            <div class="font-bold text-slate-800"><?php echo htmlspecialchars($dmg['category']); ?></div>
                            <p class="mt-2 text-sm text-slate-700 italic border-l-2 border-red-200 pl-3">
                                <?php echo nl2br(htmlspecialchars($dmg['description'])); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="mt-8 pt-8 border-t border-slate-200">
                <h4 class="text-sm font-bold text-slate-400 uppercase mb-4">Mecanic / Service Notes</h4>
                <div class="h-40 border-2 border-dashed border-slate-200 rounded-xl"></div>
            </div>
        </div>
    </div>
    
    <div class="mt-12 text-center text-[10px] text-slate-400 uppercase tracking-widest border-t border-slate-100 pt-6">
        Generat automat de FleetLog App • <?php echo date('d.m.Y H:i'); ?>
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    .no-print { display: none !important; }
    .print-area { border: none !important; box-shadow: none !important; padding: 0 !important; }
    .main-content { padding: 0 !important; margin: 0 !important; }
    header, aside { display: none !important; }
}
</style>
