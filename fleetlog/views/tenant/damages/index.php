<?php
// Separate damages by status
$pendingDamages = array_filter($damages, fn($d) => $d['status'] === 'seen');
$processedDamages = array_filter($damages, fn($d) => $d['status'] !== 'seen');
?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Damage Reports</h1>
</div>

<?php if (!empty($pendingDamages)): ?>
<div class="mb-8">
    <h2 class="text-lg font-black text-amber-600 mb-4 flex items-center px-4 md:px-0 uppercase tracking-widest text-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        Raportări Noi / Neverificate
    </h2>
    
    <!-- Desktop: New Reports Table -->
    <div class="hidden lg:block bg-amber-50 rounded-2xl shadow-sm border border-amber-200 overflow-hidden">
        <table class="min-w-full divide-y divide-amber-200">
            <thead class="bg-amber-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-amber-800 uppercase tracking-wider">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-amber-800 uppercase tracking-wider">Vehicul</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-amber-800 uppercase tracking-wider">Șofer</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-amber-800 uppercase tracking-wider">Categorie</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-amber-800 uppercase tracking-wider">Gravitate</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-amber-800 uppercase tracking-wider">Acțiuni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-amber-200">
                <?php foreach ($pendingDamages as $damage): ?>
                    <tr class="hover:bg-amber-100/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 font-bold">
                            <?php echo date('d M Y H:i', strtotime($damage['datetime'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-white border border-amber-200 text-slate-800 rounded font-mono text-xs font-black shadow-sm">
                                <?php echo $damage['license_plate']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-bold">
                            <?php echo $damage['driver_name']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 font-medium">
                            <?php echo ucfirst($damage['category']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black uppercase tracking-widest rounded-full 
                                <?php echo $damage['severity'] === 'high' ? 'bg-red-500 text-white shadow-lg shadow-red-500/20' : ($damage['severity'] === 'med' ? 'bg-orange-400 text-white shadow-lg shadow-orange-500/20' : 'bg-blue-400 text-white shadow-lg shadow-blue-500/20'); ?>">
                                <?php echo ucfirst($damage['severity']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="/tenant/damages/edit/<?php echo $damage['id']; ?>" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white font-black uppercase tracking-widest text-[10px] rounded-lg hover:bg-amber-700 transition-all shadow-md">
                                Verifică
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile: New Reports Cards -->
    <div class="lg:hidden space-y-4 px-4">
        <?php foreach ($pendingDamages as $damage): ?>
            <div class="bg-amber-50 rounded-2xl shadow-sm border border-amber-200 p-4 space-y-4">
                <div class="flex justify-between items-start">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-amber-600 border border-amber-200 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-amber-500 uppercase tracking-widest"><?php echo date('d M Y H:i', strtotime($damage['datetime'])); ?></div>
                            <div class="font-black text-slate-800"><?php echo $damage['license_plate']; ?></div>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-[9px] font-black rounded-lg <?php echo $damage['severity'] === 'high' ? 'bg-red-500 text-white' : ($damage['severity'] === 'med' ? 'bg-orange-400 text-white' : 'bg-blue-400 text-white'); ?> uppercase tracking-widest">
                        <?php echo ucfirst($damage['severity']); ?>
                    </span>
                </div>
                
                <div class="pt-2 flex justify-between items-center">
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Șofer</div>
                        <div class="text-sm font-bold text-slate-700"><?php echo $damage['driver_name']; ?></div>
                    </div>
                    <a href="/tenant/damages/edit/<?php echo $damage['id']; ?>" class="px-6 py-3 bg-amber-600 text-white font-black uppercase tracking-widest text-xs rounded-xl shadow-lg shadow-amber-500/20">
                        Verifică
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Processed Reports -->
<div>
    <h2 class="text-lg font-black text-slate-700 mb-4 flex items-center px-4 md:px-0 uppercase tracking-widest text-sm">
        <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Rapoarte Procesate
    </h2>
    
    <!-- Desktop: Processed Table -->
    <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Vehicul</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Șofer</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acțiuni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($processedDamages as $damage): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?php echo date('d M Y H:i', strtotime($damage['datetime'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded font-mono text-xs font-bold">
                                <?php echo $damage['license_plate']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <?php echo $damage['driver_name']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-[10px] font-black uppercase tracking-widest leading-5 rounded-full 
                                <?php echo $damage['status'] === 'closed' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-slate-100 text-slate-600 border border-slate-200'; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $damage['status'])); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="/tenant/damages/edit/<?php echo $damage['id']; ?>" class="text-blue-600 hover:text-blue-800 font-black uppercase tracking-widest text-xs">Vezi detalii</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile: Processed Cards -->
    <div class="lg:hidden space-y-4 px-4 pb-12">
        <?php foreach ($processedDamages as $damage): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 space-y-3 opacity-90">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?php echo date('d M Y H:i', strtotime($damage['datetime'])); ?></div>
                        <div class="font-black text-slate-700"><?php echo $damage['license_plate']; ?></div>
                    </div>
                    <span class="px-2 py-1 text-[9px] font-black rounded-lg <?php echo $damage['status'] === 'closed' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'; ?> uppercase tracking-widest">
                        <?php echo ucfirst(str_replace('_', ' ', $damage['status'])); ?>
                    </span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <div class="text-sm font-medium text-slate-500"><?php echo $damage['driver_name']; ?></div>
                    <a href="/tenant/damages/edit/<?php echo $damage['id']; ?>" class="text-blue-600 font-black uppercase tracking-widest text-[10px]">Vezi detalii</a>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($processedDamages)): ?>
            <div class="py-12 bg-white rounded-2xl border border-dashed border-slate-200 text-center text-slate-400 italic">
                Nu există rapoarte procesate.
            </div>
        <?php endif; ?>
    </div>
</div>
