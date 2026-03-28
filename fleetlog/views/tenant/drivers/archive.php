<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="/tenant/drivers" class="text-slate-500 hover:text-slate-700 flex items-center text-sm font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Drivers
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h1 class="text-xl font-bold text-slate-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Archive Driver: <?php echo htmlspecialchars($driver['name']); ?>
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Archiving will remove the driver from the active list but keep all trip and fueling records.
            </p>
        </div>

        <form action="/tenant/drivers/archive/<?php echo $driver['id']; ?>" method="POST" class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Archive Reason / Notes</label>
                <textarea name="archive_notes" rows="4" required
                    placeholder="e.g. Duplicated account, Left the company, Personal reasons..."
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all text-slate-700"
                ></textarea>
                <p class="mt-2 text-xs text-slate-400 italic">
                    These notes will be visible in the archived drivers list.
                </p>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4">
                <a href="/tenant/drivers" class="px-6 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
                    Confirm Archiving
                </button>
            </div>
        </form>
    </div>
</div>
