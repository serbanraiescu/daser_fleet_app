<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'ro'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protocol Inventar: <?php echo htmlspecialchars((string)$report['document_number']); ?></title>
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
    <!-- Toolbar -->
    <div class="max-w-[210mm] mx-auto mb-8 flex items-center justify-between no-print">
        <a href="/tenant/documents" class="inline-flex items-center text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Documents
        </a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Protocol
        </button>
    </div>

    <!-- Document -->
    <div class="document-container max-w-[210mm] mx-auto bg-white shadow-2xl p-8 md:p-12 relative overflow-hidden">
        
        <!-- Header / Logo Area -->
        <div class="flex justify-between items-start mb-8 border-b-4 border-blue-600 pb-4">
            <div>
                <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter mb-2">PROCES VERBAL</h1>
                <p class="text-blue-600 font-bold uppercase tracking-widest text-sm">CUSTODIE INVENTAR PERSONAL (ȘOFER)</p>
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
        <div class="grid grid-cols-2 gap-8 mb-8 bg-slate-50 p-6 rounded-2xl border border-slate-100">
            <div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">NUMĂR DOCUMENT</div>
                <div class="text-xl font-black text-slate-900"><?php echo htmlspecialchars((string)$report['document_number']); ?></div>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">DATA EMITERII</div>
                <div class="text-xl font-black text-slate-900"><?php echo date('d.m.Y H:i', strtotime($report['created_at'])); ?></div>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-[10px] font-black text-blue-600 uppercase tracking-widest border-b border-blue-100 pb-1 mb-4 text-center">DETALII GESTIONAR (ȘOFER)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500 text-sm">Nume și Prenume</span>
                    <span class="font-bold text-slate-900"><?php echo htmlspecialchars((string)$driver['name']); ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500 text-sm">Email</span>
                    <span class="font-bold text-slate-900"><?php echo htmlspecialchars((string)$driver['email']); ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500 text-sm">CNP</span>
                    <span class="font-bold text-slate-900"><?php echo htmlspecialchars((string)$driver['cnp'] ?: 'N/A'); ?></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <span class="text-slate-500 text-sm">Serie Permis</span>
                    <span class="font-bold text-slate-900"><?php echo htmlspecialchars((string)$driver['license_series'] ?: 'N/A'); ?></span>
                </div>
            </div>
        </div>

        <!-- Equipment Inventory -->
        <div class="mb-8">
            <h3 class="text-[10px] font-black text-blue-600 uppercase tracking-widest border-b border-blue-100 pb-1 mb-4 text-center">ECHIPAMENTE ÎN CUSTODIE</h3>
            <div class="grid grid-cols-2 gap-4">
                <?php 
                    $items = [
                        'has_triangles' => ['label' => 'Triunghiuri Reflectorizante', 'type' => 'count'],
                        'has_vest' => ['label' => 'Vestă Reflectorizantă', 'type' => 'count'],
                        'has_jack' => ['label' => 'Cric Functional', 'type' => 'bool'],
                        'has_tow_rope' => ['label' => 'Șufă Tractare', 'type' => 'bool'],
                        'has_jumper_cables' => ['label' => 'Cabluri Curent', 'type' => 'bool'],
                        'has_spare_wheel' => ['label' => 'Roată Rezervă', 'type' => 'bool'],
                    ];
                ?>
                <?php foreach ($items as $field => $cfg): 
                    $val = $report[$field] ?? 0;
                    $isPresent = $cfg['type'] === 'count' ? $val > 0 : $val == 1;
                ?>
                    <div class="flex items-center justify-between p-3 rounded-lg border <?php echo $isPresent ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-100 opacity-40'; ?>">
                        <span class="text-sm font-bold text-slate-700"><?php echo $cfg['label']; ?></span>
                        <div class="font-black text-blue-700">
                            <?php if ($cfg['type'] === 'count'): ?>
                                <?php echo (int)$val; ?> x
                            <?php else: ?>
                                <?php echo $val == 1 ? 'DA' : 'NU'; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Expiry Reminders -->
        <div class="mb-8">
            <h3 class="text-[10px] font-black text-blue-600 uppercase tracking-widest border-b border-blue-100 pb-1 mb-4 text-center">TERMENE DE EXPIRARE (VALABILITATE)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl border border-slate-200 flex justify-between items-center">
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">TRUSĂ MEDICALĂ</div>
                        <div class="text-sm font-black text-slate-900"><?php echo $report['medical_kit_expiry'] ? date('d.m.Y', strtotime($report['medical_kit_expiry'])) : 'NESETATĂ'; ?></div>
                    </div>
                    <?php if ($report['medical_kit_expiry'] && $report['medical_kit_expiry'] < date('Y-m-d')): ?>
                        <span class="text-[10px] bg-red-100 text-red-700 px-2 py-1 rounded font-bold uppercase">EXPIRATĂ</span>
                    <?php endif; ?>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 flex justify-between items-center">
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">STINGĂTOR INCENDIU</div>
                        <div class="text-sm font-black text-slate-900"><?php echo $report['extinguisher_expiry'] ? date('d.m.Y', strtotime($report['extinguisher_expiry'])) : 'NESETAT'; ?></div>
                    </div>
                    <?php if ($report['extinguisher_expiry'] && $report['extinguisher_expiry'] < date('Y-m-d')): ?>
                        <span class="text-[10px] bg-red-100 text-red-700 px-2 py-1 rounded font-bold uppercase">EXPIRAT</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($report['notes']): ?>
        <div class="mb-12">
            <h3 class="text-[10px] font-black text-blue-600 uppercase tracking-widest border-b border-blue-100 pb-1 mb-2">OBSERVAȚII / NOTE SPECIALE</h3>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700 italic leading-relaxed">
                <?php echo nl2br(htmlspecialchars((string)$report['notes'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Disclaimer -->
        <div class="mb-12 text-[10px] text-slate-400 leading-relaxed italic border-t border-slate-100 pt-4">
            Prezentul proces verbal atestă preluarea în folosință și custodie a echipamentelor menționate mai sus. 
            Gestionarul (Șoferul) se obligă să mențină echipamentele în stare bună de funcționare și să raporteze orice pierdere sau deteriorare. 
            În cazul încetării contractului, toate echipamentele aflate în custodie vor fi returnate către <?php echo htmlspecialchars((string)$report['tenant_name']); ?>.
        </div>

        <!-- Signatures -->
        <div class="mt-8 grid grid-cols-2 gap-12">
            <div class="text-center pt-6 border-t border-slate-200">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">PREDĂTOR (ADMIN)</div>
                <div class="h-12 flex items-center justify-center">
                    <span class="text-slate-200 text-xs italic">Locus Sigilli (L.S.)</span>
                </div>
                <div class="text-xs font-bold text-slate-900"><?php echo htmlspecialchars((string)$report['tenant_name']); ?></div>
            </div>
            <div class="text-center pt-6 border-t border-slate-200">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">PRIMITOR (ȘOFER)</div>
                <div class="h-12 flex items-center justify-center font-bold text-slate-800 underline decoration-blue-200 decoration-4">
                    <?php echo htmlspecialchars((string)$driver['name']); ?>
                </div>
                <div class="text-[10px] text-slate-400">Am primit echipamentele în starea descrisă mai sus.</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-16 text-center text-[10px] text-slate-300 font-bold uppercase tracking-widest border-t border-slate-100 pt-4">
            Sistem Generat de DASER FLEET &copy; <?php echo date('Y'); ?> | www.daser.ro
        </div>
    </div>
</body>
</html>
