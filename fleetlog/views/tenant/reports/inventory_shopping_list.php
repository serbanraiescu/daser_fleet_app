<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background-color: white; }
            .print-container { box-shadow: none; border: none; width: 100%; max-width: none; margin: 0; padding: 0; }
        }
        body { background-color: #f8fafc; }
    </style>
</head>
<body class="p-4 md:p-8">
    <div class="max-w-4xl mx-auto print-container">
        <div class="flex justify-between items-start mb-8 no-print">
            <a href="/tenant/vehicles" class="text-slate-600 hover:text-slate-900 flex items-center bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Înapoi la Listă
            </a>
            <button onclick="window.print()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition-colors shadow flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Printează Lista
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden overflow-hidden">
            <!-- Header -->
            <div class="bg-slate-900 text-white p-8">
                <div class="flex justify-between items-end">
                    <div>
                        <h1 class="text-3xl font-black mb-2">Shopping List</h1>
                        <p class="text-slate-400">Inventar Lipsă / Expirat - Toate Vehiculele Active</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Generat la</p>
                        <p class="text-lg font-mono"><?php echo date('d.m.Y H:i'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Summary Chips -->
            <div class="p-6 bg-slate-50 border-b border-slate-200">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Sumar Necesar Total (Buc.)</h3>
                <div class="flex flex-wrap gap-3">
                    <?php 
                    $labels = [
                        'triangles' => ['Triunghiuri', 'bg-blue-100 text-blue-800'],
                        'vests' => ['Veste', 'bg-amber-100 text-amber-800'],
                        'jacks' => ['Cric-uri', 'bg-emerald-100 text-emerald-800'],
                        'tow_ropes' => ['Șufe Tractare', 'bg-cyan-100 text-cyan-800'],
                        'jumper_cables' => ['Cabluri Curent', 'bg-rose-100 text-rose-800'],
                        'spare_wheels' => ['Roți Rezervă', 'bg-indigo-100 text-indigo-800'],
                        'med_kits' => ['Truse Med.', 'bg-red-100 text-red-800'],
                        'extinguishers' => ['Stingătoare', 'bg-red-100 text-red-800']
                    ];
                    foreach ($summary as $key => $count): 
                        if ($count > 0):
                    ?>
                        <div class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white shadow-sm flex items-center">
                            <span class="text-xs font-bold text-slate-600 mr-2"><?php echo $labels[$key][0]; ?>:</span>
                            <span class="px-2 py-0.5 rounded-lg font-black text-sm <?php echo $labels[$key][1]; ?>"><?php echo $count; ?></span>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>

            <!-- Main List -->
            <div class="p-8">
                <?php if (empty($shoppingList)): ?>
                    <div class="text-center py-20">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 text-green-600 mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h2 class="text-2xl font-black text-slate-800">Totul este în regulă!</h2>
                        <p class="text-slate-500">Toate vehiculele au inventarul complet și valid.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-8">
                        <?php foreach ($shoppingList as $item): ?>
                            <div class="border-l-4 border-indigo-500 pl-6 py-2">
                                <h2 class="text-xl font-black text-slate-900 mb-3"><?php echo htmlspecialchars($item['vehicle']); ?></h2>
                                <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                    <?php foreach ($item['missing'] as $missingItem): ?>
                                        <li class="flex items-center text-slate-700 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                            <svg class="w-4 h-4 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                            <span class="text-sm font-medium"><?php echo htmlspecialchars($missingItem); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer for printing -->
            <div class="p-8 border-t border-slate-100 bg-slate-50/50 mt-10">
                <div class="flex justify-between items-center text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                    <span>Daser Fleet Management System</span>
                    <span>Document Intern / Confidențial</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
