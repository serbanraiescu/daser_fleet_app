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
    <h2 class="text-lg font-bold text-amber-600 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        New / Unseen Reports
    </h2>
    <div class="bg-amber-50 rounded-xl shadow-sm border border-amber-200 overflow-hidden">
        <table class="min-w-full divide-y divide-amber-200">
            <thead class="bg-amber-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 uppercase tracking-wider">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 uppercase tracking-wider">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-amber-800 uppercase tracking-wider">Severity</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-amber-800 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-amber-200">
                <?php foreach ($pendingDamages as $damage): ?>
                    <tr class="hover:bg-amber-100 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 font-medium">
                            <?php echo date('d M Y H:i', strtotime($damage['datetime'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-white border border-amber-200 text-slate-800 rounded font-mono text-xs font-bold shadow-sm">
                                <?php echo $damage['license_plate']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-medium">
                            <?php echo $damage['driver_name']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">
                            <?php echo ucfirst($damage['category']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full 
                                <?php echo $damage['severity'] === 'high' ? 'bg-red-200 text-red-900' : ($damage['severity'] === 'med' ? 'bg-orange-200 text-orange-900' : 'bg-blue-200 text-blue-900'); ?>">
                                <?php echo ucfirst($damage['severity']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="/tenant/damages/edit/<?php echo $damage['id']; ?>" class="inline-flex items-center px-3 py-1 bg-amber-600 text-white font-bold rounded hover:bg-amber-700 transition-colors">
                                Review & Manage
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div>
    <h2 class="text-lg font-bold text-slate-700 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Processed Reports
    </h2>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php foreach ($processedDamages as $damage): ?>
                    <tr class="hover:bg-slate-50 opacity-80 hover:opacity-100 transition-opacity">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <?php echo date('d M Y H:i', strtotime($damage['datetime'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded font-mono text-xs font-bold">
                                <?php echo $damage['license_plate']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <?php echo $damage['driver_name']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $damage['status'] === 'closed' ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-800'; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $damage['status'])); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="/tenant/damages/edit/<?php echo $damage['id']; ?>" class="text-blue-600 hover:text-blue-900 font-bold">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($processedDamages)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-500 italic">
                            No processed damage reports found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
