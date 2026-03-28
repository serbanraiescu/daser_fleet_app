<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <h1 class="text-2xl font-bold text-slate-800">Fleet Drivers</h1>
    <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
        <div class="relative w-full md:w-64">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" id="driverSearch" placeholder="Cauta sofer..." 
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
        </div>
        <a href="/tenant/drivers/add" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors w-full md:w-auto text-center">
            + Add Driver
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200" id="driversTable">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            <?php foreach ($drivers as $driver): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-900"><?php echo $driver['name']; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-slate-600"><?php echo $driver['email']; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-slate-600"><?php echo $driver['phone'] ?: '-'; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $driver['active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo $driver['active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                        <?php if (!$driver['active']): ?>
                            <a href="/tenant/drivers/approve/<?php echo $driver['id']; ?>" class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-lg border border-green-200 hover:bg-green-100 transition-colors text-xs font-black uppercase tracking-widest">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Aprobă
                            </a>
                        <?php endif; ?>
                        <a href="/tenant/documents/inventory/add?driver_id=<?php echo $driver['id']; ?>" class="text-blue-600 hover:text-blue-900 font-bold">Protocol Inventar</a>
                        <a href="/tenant/drivers/edit/<?php echo $driver['id']; ?>" class="text-slate-400 hover:text-slate-600 transition-colors">Edit</a>
                        <a href="/tenant/drivers/archive/<?php echo $driver['id']; ?>" class="text-red-400 hover:text-red-600 transition-colors" title="Archive Driver">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($drivers)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-500 italic">
                        No active drivers registered yet.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Archived Drivers Section -->
<?php if (!empty($archivedDrivers)): ?>
<div class="mt-10 mb-10">
    <h2 class="text-lg font-bold text-red-700 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        Archived Drivers
    </h2>
    <div class="bg-red-50/30 rounded-xl shadow-sm border border-red-100 overflow-hidden opacity-90 hover:opacity-100 transition-opacity">
        <table class="min-w-full divide-y divide-red-200">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Archive Reason / Notes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Archived Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-red-100 bg-white">
                <?php foreach ($archivedDrivers as $driver): ?>
                    <tr class="hover:bg-red-50/50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-900 line-through decoration-slate-400"><?php echo htmlspecialchars($driver['name']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?php echo htmlspecialchars($driver['email']); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-red-800 italic max-w-sm overflow-hidden text-ellipsis">
                            "<?php echo htmlspecialchars($driver['archive_notes'] ?? 'No reason provided'); ?>"
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                            <?php echo date('d.m.Y H:i', strtotime($driver['updated_at'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script>
document.getElementById('driverSearch')?.addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#driversTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>
