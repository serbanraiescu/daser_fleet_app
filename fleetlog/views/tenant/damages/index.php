<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Damage Reports</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Vehicle</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Driver</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Severity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            <?php foreach ($damages as $damage): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?php echo date('d M Y H:i', strtotime($damage['datetime'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded font-mono text-xs font-bold">
                            <?php echo $damage['license_plate']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                        <?php echo $damage['driver_name']; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?php echo ucfirst($damage['category']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php echo $damage['severity'] === 'high' ? 'bg-red-100 text-red-800' : ($damage['severity'] === 'med' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'); ?>">
                            <?php echo ucfirst($damage['severity']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php echo $damage['status'] === 'closed' ? 'bg-green-100 text-green-800' : ($damage['status'] === 'in_repair' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800'); ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $damage['status'])); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        <a href="/tenant/damages/edit/<?php echo $damage['id']; ?>" class="text-blue-600 hover:text-blue-900 font-bold">Manage</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($damages)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-500 italic">
                        No damage reports found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
