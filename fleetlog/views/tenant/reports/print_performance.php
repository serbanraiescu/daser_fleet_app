<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; background: white; }
            .print-container { width: 100%; max-width: none; box-shadow: none; border: none; padding: 0; }
            @page { size: A4 landscape; margin: 1.5cm; }
        }
        body { background-color: #f8fafc; font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="p-8">
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200 print-container">
        
        <div class="flex justify-between items-start mb-8 border-b border-slate-200 pb-6">
            <div>
                <h1 class="text-3xl font-black text-slate-900 mb-2 uppercase tracking-tight">Raport Performanță Flotă</h1>
                <p class="text-slate-500 font-medium">
                    Perioada: 
                    <?php 
                        if ($period === 'monthly') echo date('F Y', strtotime("$selected_year-$selected_month-01"));
                        elseif ($period === 'yearly') echo $selected_year;
                        elseif ($period === 'weekly') echo "Ultima săptămână";
                        else echo "Astăzi";
                    ?>
                </p>
            </div>
            <div class="text-right">
                <div class="text-xl font-bold text-blue-600"><?php echo htmlspecialchars($tenant['company_name'] ?? 'Fleet Management'); ?></div>
                <div class="text-sm text-slate-500">Data Generării: <?php echo date('d.m.Y H:i'); ?></div>
                <button onclick="window.print()" class="no-print mt-4 px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-bold hover:bg-black transition-colors flex items-center ml-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"></path></svg>
                    Printează
                </button>
            </div>
        </div>

        <?php
            $totalFuel = 0;
            $totalRepairs = 0;
            $totalExtra = 0;
            $totalKm = 0;
            foreach ($vehicles as $v) {
                $totalFuel += ($v['total_fuel_cost'] ?? 0);
                $totalRepairs += ($v['total_repair_cost'] ?? 0);
                $totalExtra += ($v['total_other_expenses'] ?? 0);
                if ($v['start_km'] && $v['end_km']) $totalKm += ($v['end_km'] - $v['start_km']);
            }
            $grandTotal = $totalFuel + $totalRepairs + $totalExtra;
        ?>

        <div class="grid grid-cols-4 gap-4 mb-8">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total Combustibil</div>
                <div class="text-xl font-black text-blue-600"><?php echo number_format($totalFuel, 2); ?> <span class="text-xs font-normal">RON</span></div>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total Reparații</div>
                <div class="text-xl font-black text-red-600"><?php echo number_format($totalRepairs, 2); ?> <span class="text-xs font-normal">RON</span></div>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total Mentenanță/Extra</div>
                <div class="text-xl font-black text-purple-600"><?php echo number_format($totalExtra, 2); ?> <span class="text-xs font-normal">RON</span></div>
            </div>
            <div class="bg-slate-900 p-4 rounded-xl shadow-md">
                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">TOTAL GENERAL COSTURI</div>
                <div class="text-xl font-black text-white"><?php echo number_format($grandTotal, 2); ?> <span class="text-xs font-normal">RON</span></div>
            </div>
        </div>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="p-3 border border-slate-200 text-xs font-bold text-slate-600 uppercase">Vehicul</th>
                    <th class="p-3 border border-slate-200 text-xs font-bold text-slate-600 uppercase text-center">KM Parcurși</th>
                    <th class="p-3 border border-slate-200 text-xs font-bold text-slate-600 uppercase text-right">Cost Combustibil</th>
                    <th class="p-3 border border-slate-200 text-xs font-bold text-slate-600 uppercase text-right">Cost Reparații</th>
                    <th class="p-3 border border-slate-200 text-xs font-bold text-slate-600 uppercase text-right">Mentenanță/Extra</th>
                    <th class="p-3 border border-slate-200 text-xs font-bold text-slate-600 uppercase text-right">Total / Vehicul</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicles as $v): ?>
                    <?php 
                        $dist = ($v['end_km'] && $v['start_km']) ? ($v['end_km'] - $v['start_km']) : 0;
                        $vTotal = ($v['total_fuel_cost'] ?? 0) + ($v['total_repair_cost'] ?? 0) + ($v['total_other_expenses'] ?? 0);
                    ?>
                    <tr>
                        <td class="p-3 border border-slate-200">
                            <div class="font-bold text-slate-900"><?php echo $v['license_plate']; ?></div>
                            <div class="text-[10px] text-slate-500 uppercase"><?php echo $v['make'] . ' ' . $v['model']; ?></div>
                        </td>
                        <td class="p-3 border border-slate-200 text-center font-mono text-sm">
                            <?php echo number_format($dist); ?> KM
                        </td>
                        <td class="p-3 border border-slate-200 text-right font-bold text-blue-600">
                            <?php echo number_format($v['total_fuel_cost'] ?? 0, 2); ?>
                        </td>
                        <td class="p-3 border border-slate-200 text-right font-bold text-red-600">
                            <?php echo number_format($v['total_repair_cost'] ?? 0, 2); ?>
                        </td>
                        <td class="p-3 border border-slate-200 text-right font-bold text-purple-600">
                            <?php echo number_format($v['total_other_expenses'] ?? 0, 2); ?>
                        </td>
                        <td class="p-3 border border-slate-200 text-right font-black bg-slate-50">
                            <?php echo number_format($vTotal, 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="bg-slate-100 font-black">
                    <td class="p-3 border border-slate-200" colspan="2">TOTAL FLOTĂ</td>
                    <td class="p-3 border border-slate-200 text-right text-blue-700"><?php echo number_format($totalFuel, 2); ?></td>
                    <td class="p-3 border border-slate-200 text-right text-red-700"><?php echo number_format($totalRepairs, 2); ?></td>
                    <td class="p-3 border border-slate-200 text-right text-purple-700"><?php echo number_format($totalExtra, 2); ?></td>
                    <td class="p-3 border border-slate-200 text-right bg-slate-200"><?php echo number_format($grandTotal, 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-8 pt-8 border-t border-slate-100 flex justify-between text-[10px] text-slate-400 uppercase tracking-widest font-bold">
            <div>Generat prin DASER Fleet Management System</div>
            <div>Pagina 1 / 1</div>
        </div>
    </div>
</body>
</html>
