
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Vehicle Expenses & Maintenance</h1>
        <p class="text-slate-500 text-sm mt-1">Track service records, insurance, taxes, and other fleet costs.</p>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <?php 
            if ($_GET['success'] === 'expense_added') echo 'Expense recorded successfully!';
            elseif ($_GET['success'] === 'expense_deleted') echo 'Expense record deleted successfully.';
        ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Service Reminders (1/3 width) -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-bold text-slate-800 flex items-center mb-4">
                <svg class="w-5 h-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Service Due Soon
            </h2>
            
            <?php if (empty($serviceDue)): ?>
                <div class="text-center py-6 text-slate-500 bg-slate-50 rounded-xl">
                    <svg class="w-8 h-8 mx-auto text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm">All vehicles are up to date on their maintenance.</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($serviceDue as $veh): 
                        $isPastDue = $veh['km_until_service'] <= 0;
                        $bgClass = $isPastDue ? 'bg-red-50 border-red-200' : 'bg-amber-50 border-amber-200';
                        $textClass = $isPastDue ? 'text-red-700' : 'text-amber-700';
                        $label = $isPastDue ? 'Past Due by ' . abs($veh['km_until_service']) . ' KM' : 'Due in ' . $veh['km_until_service'] . ' KM';
                    ?>
                        <div class="p-4 border rounded-xl <?php echo $bgClass; ?>">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="font-bold text-slate-800"><?php echo htmlspecialchars($veh['license_plate']); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo htmlspecialchars($veh['make'] . ' ' . $veh['model']); ?></div>
                                </div>
                                <span class="px-2 py-1 text-xs font-bold rounded-lg bg-white <?php echo $textClass; ?> shadow-sm">
                                    <?php echo $label; ?>
                                </span>
                            </div>
                            <div class="text-xs text-slate-600 mt-2 flex justify-between">
                                <span>Current: <?php echo number_format($veh['current_odometer']); ?></span>
                                <span>Next: <?php echo number_format($veh['next_service_km']); ?></span>
                            </div>

                            <div class="mt-3 p-3 bg-white/50 rounded-lg border border-black/5">
                                <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Last Service Note:</div>
                                <div class="text-xs text-slate-700 italic"><?php echo htmlspecialchars($veh['last_maintenance_notes']); ?></div>
                                <?php if ($veh['last_maintenance_date']): ?>
                                    <div class="text-[10px] text-slate-400 mt-1"><?php echo date('d M Y', strtotime($veh['last_maintenance_date'])); ?></div>
                                <?php endif; ?>
                            </div>

                            <a href="/tenant/vehicles/mechanic-report/<?php echo $veh['id']; ?>" class="mt-3 block w-full py-2 text-center bg-white text-blue-600 text-xs font-bold rounded-lg border border-blue-200 hover:bg-blue-50 transition-colors">
                                View Mechanic Report
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-500">
                <p>Vehicles appear here when they are within 1000 KM of their scheduled Next Service KM, or if they have exceeded it.</p>
            </div>
        </div>
    </div>

    <!-- Right Column: Expense History (2/3 width) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center bg-slate-50 gap-4">
                <h2 class="text-lg font-bold text-slate-800">Istoric Cheltuieli</h2>
                <a href="/tenant/expenses/add" class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white text-sm font-black rounded-xl hover:bg-blue-700 transition shadow-lg hover:shadow-blue-500/20 flex items-center justify-center uppercase tracking-widest">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Adaugă Cheltuială
                </a>
            </div>
            
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-200 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Data</th>
                            <th class="px-6 py-4">Vehicul</th>
                            <th class="px-6 py-4">Categorie</th>
                            <th class="px-6 py-4">Descriere</th>
                            <th class="px-6 py-4 text-right">Cost (RON)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                                    Nu există cheltuieli înregistrate.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $expense): 
                                $badgeColor = 'bg-slate-100 text-slate-700';
                                if ($expense['expense_type'] === 'maintenance') $badgeColor = 'bg-blue-100 text-blue-700';
                                elseif ($expense['expense_type'] === 'insurance') $badgeColor = 'bg-purple-100 text-purple-700';
                                elseif ($expense['expense_type'] === 'tax') $badgeColor = 'bg-red-100 text-red-700';
                                elseif ($expense['expense_type'] === 'consumable') $badgeColor = 'bg-green-100 text-green-700';
                            ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                                        <?php echo date('d M Y', strtotime($expense['expense_date'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-black text-slate-800"><?php echo htmlspecialchars($expense['license_plate']); ?></div>
                                        <div class="text-xs text-slate-500"><?php echo htmlspecialchars($veh['make'] . ' ' . $veh['model']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 text-[10px] font-black rounded-lg <?php echo $badgeColor; ?> uppercase tracking-widest border border-current border-opacity-10">
                                            <?php echo htmlspecialchars($expense['expense_type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800"><?php echo htmlspecialchars($expense['name']); ?></div>
                                        <?php if ($expense['odometer_at_expense'] > 0): ?>
                                            <div class="text-xs text-slate-500 mt-0.5">@ <?php echo number_format($expense['odometer_at_expense']); ?> KM</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 font-black text-slate-900 text-right text-lg">
                                        <?php echo number_format($expense['cost'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-slate-100">
                <?php if (empty($expenses)): ?>
                    <div class="px-6 py-12 text-center text-slate-400 italic">
                        Nu există cheltuieli înregistrate.
                    </div>
                <?php else: ?>
                    <?php foreach ($expenses as $expense): 
                        $badgeColor = 'bg-slate-100 text-slate-700';
                        if ($expense['expense_type'] === 'maintenance') $badgeColor = 'bg-blue-100 text-blue-700';
                        elseif ($expense['expense_type'] === 'insurance') $badgeColor = 'bg-purple-100 text-purple-700';
                        elseif ($expense['expense_type'] === 'tax') $badgeColor = 'bg-red-100 text-red-700';
                        elseif ($expense['expense_type'] === 'consumable') $badgeColor = 'bg-green-100 text-green-700';
                    ?>
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></div>
                                    <div class="font-black text-slate-800"><?php echo htmlspecialchars($expense['license_plate']); ?></div>
                                </div>
                                <span class="px-2 py-1 text-[9px] font-black rounded-lg <?php echo $badgeColor; ?> uppercase tracking-widest border border-current border-opacity-10">
                                    <?php echo htmlspecialchars($expense['expense_type']); ?>
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-end">
                                <div>
                                    <div class="text-sm font-bold text-slate-700"><?php echo htmlspecialchars($expense['name']); ?></div>
                                    <?php if ($expense['odometer_at_expense'] > 0): ?>
                                        <div class="text-xs text-slate-400 mt-0.5">La km: <?php echo number_format($expense['odometer_at_expense']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-black text-slate-900"><?php echo number_format($expense['cost'], 2); ?> <span class="text-xs text-slate-400">RON</span></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

