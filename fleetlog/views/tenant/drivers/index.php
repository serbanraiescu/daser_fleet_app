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
        <button onclick="toggleArchived()" class="inline-flex items-center px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 bg-white hover:bg-slate-50 transition-colors w-full md:w-auto justify-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Vezi Arhivați
        </button>
        <a href="/tenant/drivers/add" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors w-full md:w-auto text-center flex items-center justify-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Adaugă Șofer
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- Mobile Driver Cards (Visible on mobile only) -->
    <div class="md:hidden divide-y divide-slate-100">
        <?php foreach ($drivers as $driver): ?>
            <div class="p-4 hover:bg-slate-50 transition-colors">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 font-black text-sm mr-3">
                            <?php echo strtoupper(substr($driver['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 text-sm"><?php echo htmlspecialchars($driver['name']); ?></div>
                            <div class="text-[10px] text-slate-500 font-medium"><?php echo htmlspecialchars($driver['email']); ?></div>
                        </div>
                    </div>
                    <?php if ($driver['active']): ?>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-green-50 text-green-700 border border-green-100 uppercase">Activ</span>
                    <?php else: ?>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-50 text-amber-700 border border-amber-100 uppercase">In așteptare</span>
                    <?php endif; ?>
                </div>

                <?php if ($driver['phone']): ?>
                <div class="mb-4">
                    <a href="tel:<?php echo $driver['phone']; ?>" class="flex items-center text-xs font-bold text-blue-600 bg-blue-50/50 px-3 py-2 rounded-lg border border-blue-100 w-fit">
                        <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Suna: <?php echo htmlspecialchars($driver['phone']); ?>
                    </a>
                </div>
                <?php endif; ?>

                <div class="flex items-center space-x-2">
                    <?php if (!$driver['active']): ?>
                        <a href="/tenant/drivers/approve/<?php echo $driver['id']; ?>" class="flex-1 bg-green-600 text-white px-3 py-2 rounded-lg text-center font-bold text-[10px] uppercase">Aprobă</a>
                    <?php endif; ?>
                    <a href="/tenant/drivers/edit/<?php echo $driver['id']; ?>" class="flex-1 bg-slate-50 text-slate-600 px-3 py-2 rounded-lg border border-slate-200 text-center font-bold text-[10px] uppercase">Editează</a>
                    <a href="/tenant/documents/inventory/add?driver_id=<?php echo $driver['id']; ?>" class="w-10 h-8 bg-blue-50 text-blue-600 rounded-lg border border-blue-100 flex items-center justify-center" title="Inventar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Desktop Table (Visible on desktop only) -->
    <table class="hidden md:table min-w-full divide-y divide-slate-200" id="driversTable">
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
                        <?php if ($driver['active']): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                Active
                            </span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                Așteaptă Aprobare
                            </span>
                        <?php endif; ?>
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
<div id="archivedSection" class="mt-10 mb-10 hidden border-t-2 border-slate-100 pt-8">
    <h2 class="text-lg font-bold text-red-700 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        Archived Drivers (Istoric)
    </h2>
    <div class="bg-red-50/30 rounded-xl shadow-sm border border-red-100 overflow-hidden opacity-90 hover:opacity-100 transition-opacity">
        <?php if (!empty($archivedDrivers)): ?>
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
        <?php else: ?>
            <div class="px-6 py-10 text-center text-slate-400 italic">
                Niciun șofer arhivat momentan.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleArchived() {
    const section = document.getElementById('archivedSection');
    section.classList.toggle('hidden');
    if (!section.classList.contains('hidden')) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

document.getElementById('driverSearch')?.addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#driversTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>
