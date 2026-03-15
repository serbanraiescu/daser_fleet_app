<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'ro'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars((string)$report['document_number']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white; }
            .no-print { display: none; }
            .print-break { page-break-after: always; }
        }
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen p-4 md:p-12">
    <!-- Toolbar -->
    <div class="max-w-[210mm] mx-auto mb-8 flex items-center justify-between no-print">
        <a href="/tenant/documents" class="inline-flex items-center text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <?php echo __('cancel'); ?>
        </a>
        <button onclick="window.print()" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print
        </button>
    </div>

    <!-- Document -->
    <div class="max-w-[210mm] mx-auto bg-white shadow-2xl p-12 md:p-20 relative overflow-hidden">
        
        <!-- Header / Logo Area -->
        <div class="flex justify-between items-start mb-12 border-b-4 border-indigo-600 pb-8">
            <div>
                <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter mb-2">PROCES VERBAL</h1>
                <p class="text-indigo-600 font-bold uppercase tracking-widest text-sm">PREDARE-PRIMIRE AUTOVEHICUL</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-black text-slate-900"><?php echo htmlspecialchars((string)$report['tenant_name']); ?></div>
                <div class="text-xs text-slate-500 font-bold mt-1">
                    CUI: <?php echo htmlspecialchars((string)$report['tenant_cui']); ?><br>
                    <?php echo nl2br(htmlspecialchars((string)$report['tenant_address'])); ?>
                </div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="grid grid-cols-2 gap-8 mb-12">
            <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo __('document_number'); ?></div>
                <div class="text-xl font-black text-slate-900"><?php echo htmlspecialchars((string)$report['document_number']); ?></div>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo __('event_date'); ?></div>
                <div class="text-xl font-black text-slate-900"><?php echo date('d.m.Y H:i', strtotime($report['created_at'])); ?></div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
            <!-- Vehicle Info -->
            <div class="space-y-6">
                <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-2">DATE VEHICUL</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Număr Înmatriculare</span>
                        <span class="text-sm font-black text-slate-900"><?php echo htmlspecialchars((string)$report['vehicle_plate']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Marcă / Model</span>
                        <span class="text-sm font-black text-slate-900"><?php echo htmlspecialchars((string)$report['vehicle_model']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Kilometraj (Odometru)</span>
                        <span class="text-sm font-black text-slate-900"><?php echo number_format($report['odometer'], 0, ',', '.'); ?> KM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Nivel Combustibil</span>
                        <span class="text-sm font-black text-slate-900 uppercase"><?php echo __("fuel_" . $report['fuel_level']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Driver Info -->
            <div class="space-y-6">
                <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-2">DATE ȘOFER</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Nume și Prenume</span>
                        <span class="text-sm font-black text-slate-900"><?php echo htmlspecialchars((string)$report['driver_name']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">CNP</span>
                        <span class="text-sm font-black text-slate-900"><?php echo htmlspecialchars($report['cnp'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Serie Permis</span>
                        <span class="text-sm font-black text-slate-900"><?php echo htmlspecialchars($report['license_series'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Expirare Permis</span>
                        <span class="text-sm font-black text-slate-900"><?php echo $report['license_expiry'] ? date('d.m.Y', strtotime($report['license_expiry'])) : 'N/A'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory / Documents -->
        <div class="mb-12">
            <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-2 mb-4">DOCUMENTE PREZENTE ÎN VEHICUL</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <?php 
                $docs = [
                    'doc_registration' => __('reg_cert'),
                    'doc_insurance' => __('insurance_rca'),
                    'doc_itp' => __('itp_label'),
                    'doc_rovinieta' => __('rovinieta_label')
                ];
                foreach ($docs as $key => $label): ?>
                <div class="flex items-center space-x-3 p-3 rounded-lg <?php echo $report[$key] ? 'bg-indigo-50 border border-indigo-100' : 'bg-slate-50 border border-slate-100 opacity-50'; ?>">
                    <div class="w-5 h-5 rounded border border-slate-300 flex items-center justify-center <?php echo $report[$key] ? 'bg-indigo-600 border-indigo-600' : ''; ?>">
                        <?php if ($report[$key]): ?>
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        <?php endif; ?>
                    </div>
                    <span class="text-xs font-bold text-slate-700 leading-tight"><?php echo $label; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Condition -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
            <div>
                <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-2 mb-4">STARE ESTETICĂ</h3>
                <div class="p-4 rounded-xl border border-slate-200">
                    <span class="text-sm font-black text-slate-900"><?php echo __("aesthetic_" . $report['aesthetic_condition']); ?></span>
                </div>
            </div>
            <div>
                <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-2 mb-4">STARE MECANICĂ</h3>
                <div class="p-4 rounded-xl border border-slate-200">
                    <span class="text-sm font-black text-slate-900"><?php echo __("mech_" . $report['mechanical_condition']); ?></span>
                </div>
            </div>
        </div>

        <?php if ($report['notes']): ?>
        <div class="mb-12">
            <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-2 mb-4">OBSERVAȚII / NOTE</h3>
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 text-sm text-slate-700 italic leading-relaxed">
                <?php echo nl2br(htmlspecialchars((string)$report['notes'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Signatures -->
        <div class="mt-24 grid grid-cols-2 gap-24">
            <div class="text-center pt-8 border-t border-slate-300">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo __('signature_company'); ?></div>
                <div class="h-12"></div>
                <div class="text-slate-200">L.S.</div>
            </div>
            <div class="text-center pt-8 border-t border-slate-300">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo __('signature_driver'); ?></div>
                <div class="h-12 text-sm text-slate-900 font-bold"><?php echo htmlspecialchars((string)$report['driver_name']); ?></div>
                <div class="text-xs text-slate-400">Am primit autovehiculul în starea descrisă mai sus.</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center text-[10px] text-slate-300 font-bold uppercase tracking-widest">
            Generat automat de FleetLog &copy; <?php echo date('Y'); ?>
        </div>
    </div>
</body>
</html>
