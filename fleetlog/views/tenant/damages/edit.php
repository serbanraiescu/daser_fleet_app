<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="/tenant/damages" class="text-blue-600 hover:underline text-sm flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to list
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Manage Damage Report #<?php echo $damage['id']; ?></h1>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Details & Photos -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Details Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Incident Details</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Vehicle</label>
                    <div class="font-bold text-slate-900"><?php echo $damage['license_plate']; ?></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Driver</label>
                    <div class="text-slate-700"><?php echo $damage['driver_name']; ?></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Date & Time</label>
                    <div class="text-slate-700"><?php echo date('d M Y H:i', strtotime($damage['datetime'])); ?></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Category</label>
                    <div class="text-slate-700 capitalize"><?php echo $damage['category']; ?></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Severity</label>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        <?php echo $damage['severity'] === 'high' ? 'bg-red-100 text-red-800' : ($damage['severity'] === 'med' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'); ?>">
                        <?php echo ucfirst($damage['severity']); ?>
                    </span>
                </div>
            </div>
            <div class="mt-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Driver Description</label>
                <div class="p-4 bg-slate-50 rounded-xl text-slate-700 italic border border-slate-100">
                    "<?php echo nl2br($damage['description']); ?>"
                </div>
            </div>
        </div>

        <!-- Photos Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Photos</h3>
            <?php if (empty($photos)): ?>
                <p class="text-slate-500 italic">No photos were uploaded for this report.</p>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php foreach ($photos as $photo): ?>
                        <a href="/<?php echo $photo['path']; ?>" target="_blank" class="group relative block rounded-xl overflow-hidden border border-slate-200 hover:border-blue-400 transition-all">
                            <img src="/<?php echo $photo['path']; ?>" alt="Damage" class="w-full h-48 object-cover group-hover:scale-105 transition-transform">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 flex items-center justify-center transition-all text-white opacity-0 group-hover:opacity-100 font-bold">
                                View Full Size
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Management Form -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Admin Management</h3>
            <form action="/tenant/damages/edit/<?php echo $damage['id']; ?>" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                        <option value="seen" <?php echo $damage['status'] === 'seen' ? 'selected' : ''; ?>>Seen / Pending</option>
                        <option value="in_repair" <?php echo $damage['status'] === 'in_repair' ? 'selected' : ''; ?>>In Repair</option>
                        <option value="closed" <?php echo $damage['status'] === 'closed' ? 'selected' : ''; ?>>Closed / Repaired</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Repair Cost (RON)</label>
                    <input type="number" step="0.01" name="repair_cost" value="<?php echo $damage['repair_cost']; ?>" 
                           class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Admin Notes</label>
                    <textarea name="admin_notes" rows="6" class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all placeholder-slate-400" 
                              placeholder="Internal notes about insurance, service, etc."><?php echo $damage['admin_notes']; ?></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
