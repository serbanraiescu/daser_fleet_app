<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">End Trip</h1>
    <p class="text-slate-500 mb-6">Finishing work with vehicle <strong><?php echo $trip['license_plate']; ?></strong></p>

    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <p class="text-sm text-blue-700">Trip started at <strong><?php echo date('H:i', strtotime($trip['start_time'])); ?></strong> with <strong><?php echo $trip['start_km']; ?> km</strong>.</p>
    </div>

    <form action="/driver/end-trip" method="POST" class="space-y-6">
        <input type="hidden" name="trip_id" value="<?php echo $trip['id']; ?>">
        
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">End KM (Current Odometer)</label>
            <input type="number" name="end_km" required min="<?php echo $trip['start_km']; ?>" class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="e.g. <?php echo $trip['start_km'] + 10; ?>">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Final Notes (Optional)</label>
            <textarea name="notes" rows="3" class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Any issues encountered?"></textarea>
        </div>

        <button type="submit" class="w-full p-4 bg-red-600 text-white rounded-xl shadow-lg font-bold text-lg hover:bg-red-700 transition-all active:scale-95">
            Close Trip & Save KM
        </button>
        
        <a href="/driver/dashboard" class="block text-center text-slate-500 font-medium py-2">Back to Dashboard</a>
    </form>
</div>
