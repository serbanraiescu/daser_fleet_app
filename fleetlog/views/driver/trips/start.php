<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Start New Trip</h1>

    <form action="/driver/start-trip" method="POST" class="space-y-6">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Select Vehicle</label>
            <select name="vehicle_id" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <option value="">-- Choose vehicle --</option>
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?php echo $vehicle['id']; ?>"><?php echo $vehicle['license_plate']; ?> (<?php echo $vehicle['make']; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Start KM (Current Odometer)</label>
            <input type="number" name="start_km" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="e.g. 125430">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Trip Type</label>
            <select name="type" class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <option value="CURSE">CURSE</option>
                <option value="NAVETA">NAVETA</option>
                <option value="LIVRARE_SPECIALA">LIVRARE SPECIALĂ</option>
                <option value="SERVICE">SERVICE</option>
                <option value="ALTE">ALTE</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Notes (Optional)</label>
            <textarea name="notes" rows="3" class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Any specific details..."></textarea>
        </div>

        <button type="submit" class="w-full p-4 bg-blue-600 text-white rounded-xl shadow-lg font-bold text-lg hover:bg-blue-700 transition-all active:scale-95">
            Launch Trip
        </button>
        
        <a href="/driver/dashboard" class="block text-center text-slate-500 font-medium py-2">Cancel</a>
    </form>
</div>
