<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Report Damage/Incident</h1>

    <form action="/driver/report-damage" method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Vehicle</label>
            <select name="vehicle_id" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <option value="">-- Select Vehicle --</option>
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?php echo $vehicle['id']; ?>" <?php echo ($selectedVehicleId == $vehicle['id']) ? 'selected' : ''; ?>>
                        <?php echo $vehicle['license_plate']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Category</label>
                <select name="category" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    <option value="zgarietura">Zgârietură</option>
                    <option value="lovitura">Lovitură</option>
                    <option value="parbriz">Parbriz</option>
                    <option value="roata">Roată</option>
                    <option value="mecanic">Mecanic</option>
                    <option value="altul">Altul</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Severity</label>
                <select name="severity" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    <option value="low">Low</option>
                    <option value="med">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
            <textarea name="description" rows="3" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Describe what happened..."></textarea>
        </div>

        <div class="p-4 bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Photos (Max 6, Max 2MB each)</label>
            <input type="file" name="photos[]" multiple accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <button type="submit" class="w-full p-4 bg-amber-500 text-white rounded-xl shadow-lg font-bold text-lg hover:bg-amber-600 transition-all active:scale-95 text-center">
            Submit Report
        </button>
        
        <a href="/driver/dashboard" class="block text-center text-slate-500 font-medium py-2">Cancel</a>
    </form>
</div>
