<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Vehicle Handover</h1>

    <form action="/driver/handover" method="POST" class="space-y-6">
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
            <label class="block text-sm font-semibold text-slate-700 mb-2">Handover To (Driver)</label>
            <select name="to_user_id" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <option value="">-- Select next driver --</option>
                <?php foreach ($drivers as $driver): ?>
                    <?php if ($driver['id'] !== $currentUser['id']): ?>
                        <option value="<?php echo $driver['id']; ?>"><?php echo $driver['name']; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Current Odometer (KM)</label>
            <input type="number" name="odometer_km" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Enter current KM">
        </div>

        <div class="flex items-center p-4 bg-amber-50 rounded-xl border border-amber-200">
            <input type="checkbox" name="has_damage" id="has_damage" class="w-5 h-5 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
            <label for="has_damage" class="ml-3 block text-sm font-bold text-amber-800">
                Are there new damages/incidents?
            </label>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Notes (Optional)</label>
            <textarea name="notes" rows="2" class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Condition of the car..."></textarea>
        </div>

        <button type="submit" class="w-full p-4 bg-slate-800 text-white rounded-xl shadow-lg font-bold text-lg hover:bg-slate-900 transition-all active:scale-95">
            Complete Handover
        </button>
        
        <a href="/driver/dashboard" class="block text-center text-slate-500 font-medium py-2">Back</a>
    </form>
</div>
