<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'ro'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars((string)$report['document_number']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { 
                background: white !important; 
                padding: 0 !important; 
                margin: 0 !important;
            }
            .no-print { display: none !important; }
            .print-break { page-break-after: always; }
            .document-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }
            .signature-section { margin-top: 2rem !important; }
            .footer-section { margin-top: 1rem !important; }
        }
        @page {
            size: A4;
            margin: 1cm 1.5cm;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen p-4 md:p-12 print:p-0">
    <!-- Impersonation Warning (Non-Print) -->
    <?php if (\FleetLog\Core\Auth::isImpersonating()): ?>
    <div class="max-w-[210mm] mx-auto mb-4 bg-yellow-100 border border-yellow-200 p-3 rounded-lg text-yellow-800 text-xs font-bold no-print flex justify-between items-center">
        <span>⚠️ MOD VIZUALIZARE (IMPERSONARE ADMIN)</span>
        <a href="/admin/stop-impersonation" class="bg-yellow-800 text-white px-2 py-1 rounded hover:bg-yellow-900">Stop</a>
    </div>
    <?php endif; ?>
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
    <div class="document-container max-w-[210mm] mx-auto bg-white shadow-2xl p-8 md:p-12 relative overflow-hidden">
        
        <!-- Header / Logo Area -->
        <div class="flex justify-between items-start mb-8 border-b-4 border-indigo-600 pb-4">
            <div>
                <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter mb-2">PROCES VERBAL</h1>
                <p class="text-indigo-600 font-bold uppercase tracking-widest text-sm">PREDARE-PRIMIRE AUTOVEHICUL</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-black text-slate-900"><?php echo htmlspecialchars((string)$report['tenant_name']); ?></div>
                <div class="text-xs text-slate-500 font-bold mt-1">
                    CUI: <?php echo htmlspecialchars((string)$report['tenant_cui']); ?> 
                    <?php if (!empty($report['tenant_reg_com'])): ?>
                        | Reg. Com: <?php echo htmlspecialchars((string)$report['tenant_reg_com']); ?>
                    <?php endif; ?><br>
                    <?php echo htmlspecialchars((string)($report['tenant_city'] ?? '')); ?><?php echo !empty($report['tenant_county']) ? ', ' . htmlspecialchars((string)$report['tenant_county']) : ''; ?><br>
                    <?php echo nl2br(htmlspecialchars((string)$report['tenant_address'])); ?>
                </div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo __('document_number'); ?></div>
                <div class="text-xl font-black text-slate-900"><?php echo htmlspecialchars((string)$report['document_number']); ?></div>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo __('event_date'); ?></div>
                <div class="text-xl font-black text-slate-900"><?php echo date('d.m.Y H:i', strtotime($report['created_at'])); ?></div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <!-- Vehicle Info -->
            <div>
                <h3 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-1 mb-1">DATE VEHICUL</h3>
                <div class="space-y-1">
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-500">Nr. Înmatriculare</span>
                        <span class="font-black text-slate-900"><?php echo htmlspecialchars((string)$report['vehicle_plate']); ?></span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-500">Marcă / Model</span>
                        <span class="font-black text-slate-900"><?php echo htmlspecialchars((string)$report['vehicle_model']); ?></span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-500">Kilometraj</span>
                        <span class="font-black text-slate-900"><?php echo number_format($report['odometer'], 0, ',', '.'); ?> KM</span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-500">Combustibil</span>
                        <span class="font-black text-slate-900 uppercase"><?php echo __("fuel_" . $report['fuel_level']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Driver Info -->
            <div>
                <h3 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-1 mb-1">DATE ȘOFER</h3>
                <div class="space-y-1">
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-500">Nume și Prenume</span>
                        <span class="font-black text-slate-900"><?php echo htmlspecialchars((string)$report['driver_name']); ?></span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-500">CNP</span>
                        <span class="font-black text-slate-900"><?php echo htmlspecialchars($report['cnp'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-500">Serie Permis</span>
                        <span class="font-black text-slate-900"><?php echo htmlspecialchars($report['license_series'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-500">Exp. Permis</span>
                        <span class="font-black text-slate-900"><?php echo $report['license_expiry'] ? date('d.m.Y', strtotime($report['license_expiry'])) : 'N/A'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory / Documents -->
        <div class="mb-4">
            <h3 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-1 mb-1">DOCUMENTE PREZENTE ÎN VEHICUL</h3>
            <div class="grid grid-cols-4 gap-2">
                <?php 
                $docs = [
                    'doc_registration' => __('reg_cert'),
                    'doc_insurance' => __('insurance_rca'),
                    'doc_itp' => __('itp_label'),
                    'doc_rovinieta' => __('rovinieta_label')
                ];
                foreach ($docs as $key => $label): ?>
                <div class="flex items-center space-x-2 p-2 rounded-lg <?php echo $report[$key] ? 'bg-indigo-50 border border-indigo-100' : 'bg-slate-50 border border-slate-100 opacity-50'; ?>">
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
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-1 mb-1">STARE ESTETICĂ</h3>
                <div class="p-2 rounded-lg border border-slate-200">
                    <span class="text-xs font-black text-slate-900"><?php echo __("aesthetic_" . $report['aesthetic_condition']); ?></span>
                </div>
            </div>
            <div>
                <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-1 mb-1">STARE MECANICĂ</h3>
                <div class="p-2 rounded-lg border border-slate-200">
                    <span class="text-xs font-black text-slate-900"><?php echo __("mech_" . $report['mechanical_condition']); ?></span>
                </div>
            </div>
        </div>

        <?php if ($report['notes']): ?>
        <div class="mb-4">
            <h3 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-1 mb-1">OBSERVAȚII / NOTE</h3>
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 text-[11px] text-slate-700 italic leading-snug">
                <?php echo nl2br(htmlspecialchars((string)$report['notes'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Signatures -->
        <div class="signature-section mt-8 grid grid-cols-2 gap-8">
            <div class="text-center pt-4 border-t border-slate-300">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo __('signature_company'); ?></div>
                <div class="h-8"></div>
                <div class="text-slate-200 text-xs">L.S.</div>
            </div>
            <div class="text-center pt-4 border-t border-slate-300">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo __('signature_driver'); ?></div>
                <div class="h-8 text-sm text-slate-900 font-bold"><?php echo htmlspecialchars((string)$report['driver_name']); ?></div>
                <div class="text-[10px] text-slate-400">Am primit autovehiculul în starea descrisă mai sus.</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-section mt-8 text-center text-[10px] text-slate-300 font-bold uppercase tracking-widest">
            Generat automat de FleetLog &copy; <?php echo date('Y'); ?>
        </div>
    </div>
</body>
</html>
