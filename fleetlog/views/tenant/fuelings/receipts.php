<div class="mb-6 flex items-center justify-between no-print">
    <div>
        <a href="/tenant/fuelings?month=<?php echo $month; ?>&year=<?php echo $year; ?>" class="text-blue-600 hover:underline text-sm flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Fueling Logs
        </a>
        <h1 class="text-2xl font-bold text-slate-800 uppercase">Individual Receipts / Bonuri Fiscale Separate</h1>
    </div>
    <button onclick="window.print()" class="px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all shadow-md flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Printează Toate Bonurile (One per page)
    </button>
</div>

<div class="print-container">
    <?php if (empty($fuelings)): ?>
        <div class="p-10 text-center text-slate-400 italic bg-slate-50 rounded-xl border border-dashed border-slate-200">
            No receipts available for this period.
        </div>
    <?php else: ?>
        <?php foreach ($fuelings as $f): ?>
            <?php if ($f['receipt_photo']): ?>
                <div class="receipt-page">
                    <div class="receipt-header border-b-2 border-slate-900 pb-4 mb-4 flex justify-between items-end">
                        <div>
                            <div class="text-[10px] text-slate-500 uppercase font-black tracking-widest leading-none">FleetLog Receipt Export</div>
                            <h2 class="text-2xl font-black text-slate-900 mt-1"><?php echo htmlspecialchars($f['license_plate']); ?></h2>
                            <div class="text-sm font-bold text-slate-600"><?php echo date('d.m.Y H:i', strtotime($f['created_at'])); ?></div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-400 uppercase font-bold">Total Cost</div>
                            <div class="text-xl font-black text-blue-700"><?php echo number_format($f['total_price'], 2); ?> RON</div>
                        </div>
                    </div>
                    
                    <div class="receipt-image-container flex items-center justify-center bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden min-h-[500px]">
                        <img src="/<?php echo $f['receipt_photo']; ?>" class="max-w-full max-h-[800px] object-contain shadow-sm" alt="Receipt">
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase block text-[8px]">Driver / Șofer</span>
                            <span class="font-bold text-slate-800"><?php echo htmlspecialchars($f['driver_name']); ?></span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 text-right">
                            <span class="text-slate-400 font-bold uppercase block text-[8px]">Odometer / Kilometraj</span>
                            <span class="font-bold text-slate-800"><?php echo number_format($f['odometer']); ?> KM</span>
                        </div>
                    </div>

                    <div class="footer-note mt-8 text-[8px] text-slate-300 text-center uppercase tracking-tighter">
                        This document is part of the monthly fueling report for <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.print-container { width: 100%; }
.receipt-page { 
    background: white; 
    padding: 2rem; 
    margin-bottom: 2rem; 
    border: 1px solid #e2e8f0; 
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

@media print {
    body { background: white !important; }
    .no-print { display: none !important; }
    header, aside { display: none !important; }
    .main-content { padding: 0 !important; margin: 0 !important; }
    .receipt-page { 
        padding: 0 !important; 
        margin: 0 !important; 
        border: none !important; 
        box-shadow: none !important;
        page-break-after: always;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .receipt-image-container { 
        flex-grow: 1; 
        background: transparent !important; 
        border: none !important;
    }
    .receipt-image-container img {
        max-height: 85vh !important;
    }
}
</style>
