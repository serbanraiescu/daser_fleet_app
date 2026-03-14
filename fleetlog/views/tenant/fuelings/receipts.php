<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
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
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .receipt-image-container img {
                max-width: 100% !important;
                max-height: 80vh !important;
                object-contain: contain;
            }
            .receipt-header { border-bottom: 2px solid black !important; }
            .footer-note { display: block !important; }
        }
        body { background-color: #f1f5f9; padding: 2rem; }
        .receipt-page { 
            background: white; 
            padding: 2.5rem; 
            margin: 0 auto 3rem auto; 
            max-width: 800px;
            border: 1px solid #e2e8f0; 
            border-radius: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="mb-6 flex items-center justify-between no-print max-w-[800px] mx-auto">
        <div>
            <a href="/tenant/fuelings?month=<?php echo $month; ?>&year=<?php echo $year; ?>" class="text-blue-600 hover:underline text-sm flex items-center mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back to Fueling Logs
            </a>
            <h1 class="text-2xl font-bold text-slate-800 uppercase">Individual Receipts</h1>
        </div>
        <button onclick="window.print()" class="px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all shadow-md flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Printează Toate Bonurile
        </button>
    </div>

    <div class="print-container">
        <?php if (empty($fuelings)): ?>
            <div class="max-w-[800px] mx-auto p-10 text-center text-slate-400 italic bg-white rounded-xl border border-dashed border-slate-200">
                No receipts available for this period.
            </div>
        <?php else: ?>
            <?php foreach ($fuelings as $f): ?>
                <?php if ($f['receipt_photo']): ?>
                    <div class="receipt-page">
                        <div class="receipt-header border-b-2 border-slate-900 pb-4 mb-6 flex justify-between items-end">
                            <div>
                                <div class="text-[10px] text-slate-500 uppercase font-black tracking-widest leading-none">FleetLog Receipt Export</div>
                                <h2 class="text-3xl font-black text-slate-900 mt-1"><?php echo htmlspecialchars($f['license_plate']); ?></h2>
                                <div class="text-sm font-bold text-slate-600"><?php echo date('d.m.Y H:i', strtotime($f['created_at'])); ?></div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-slate-400 uppercase font-bold">Total Cost</div>
                                <div class="text-2xl font-black text-blue-700"><?php echo number_format($f['total_price'], 2); ?> RON</div>
                            </div>
                        </div>
                        
                        <div class="receipt-image-container flex items-center justify-center bg-slate-50 border border-slate-100 rounded-2xl overflow-hidden mb-6">
                            <img src="/<?php echo $f['receipt_photo']; ?>" class="max-w-full h-auto shadow-sm" alt="Receipt">
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm mt-auto">
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-400 font-bold uppercase block text-[10px] mb-1">Driver / Șofer</span>
                                <span class="font-bold text-slate-800"><?php echo htmlspecialchars($f['driver_name']); ?></span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-right">
                                <span class="text-slate-400 font-bold uppercase block text-[10px] mb-1">Odometer / Kilometraj</span>
                                <span class="font-bold text-slate-800"><?php echo number_format($f['odometer']); ?> KM</span>
                            </div>
                        </div>

                        <div class="footer-note mt-8 text-[10px] text-slate-300 text-center uppercase tracking-widest hidden">
                            Document generat de FleetLog pentru raportul de alimentare din <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
