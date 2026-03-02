<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Edit Vehicle</h1>
    <a href="/tenant/vehicles" class="text-slate-600 hover:text-slate-900 flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Fleet
    </a>
</div>

<?php if (isset($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-2xl">
    <form action="/tenant/vehicles/edit/<?php echo $vehicle['id']; ?>" method="POST" class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">License Plate</label>
                <input type="text" name="license_plate" required value="<?php echo $vehicle['license_plate']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500 underline uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Make</label>
                <input type="text" name="make" required value="<?php echo $vehicle['make']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Model</label>
                <input type="text" name="model" required value="<?php echo $vehicle['model']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Current Odometer (KM)</label>
                <input type="number" name="current_odometer" required value="<?php echo $vehicle['current_odometer']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Unique QR Code</label>
                <div class="flex items-center space-x-3">
                    <input type="text" name="qr_code" readonly value="<?php echo $vehicle['qr_code']; ?>" class="w-full px-3 py-2 border border-slate-200 bg-slate-50 rounded-md text-slate-500 font-mono">
                    <?php 
                        $qrUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode("https://" . ($_SERVER['HTTP_HOST'] ?? 'fleet.daserdesign.ro') . "/driver/start-trip?qr=" . $vehicle['qr_code']);
                    ?>
                    <a href="<?php echo $qrUrl; ?>" target="_blank" title="View Large QR" class="flex-shrink-0">
                        <img src="<?php echo $qrUrl; ?>" alt="QR" class="w-10 h-10 border border-slate-300 rounded shadow-sm">
                    </a>
                </div>
                <p class="text-[10px] text-slate-400 mt-1 italic">Used for driver fast-scan selection. Regenerates on save if deleted.</p>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Documentation Expiry Dates</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Expiry RCA</label>
                    <input type="date" name="expiry_rca" value="<?php echo $vehicle['expiry_rca']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Expiry ITP</label>
                    <input type="date" name="expiry_itp" value="<?php echo $vehicle['expiry_itp']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Expiry Rovigneta</label>
                    <input type="date" name="expiry_rovigneta" value="<?php echo $vehicle['expiry_rovigneta']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <label class="block text-sm font-medium text-slate-700 mb-1">Vehicle Status</label>
            <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="active" <?php echo $vehicle['status'] === 'active' ? 'selected' : ''; ?>>Active (Available for trips)</option>
                <option value="inactive" <?php echo $vehicle['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive (Hidden from drivers)</option>
                <option value="service" <?php echo $vehicle['status'] === 'service' ? 'selected' : ''; ?>>In Service (Blocked for new trips)</option>
            </select>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                Update Vehicle
            </button>
        </div>
    </form>
</div>
