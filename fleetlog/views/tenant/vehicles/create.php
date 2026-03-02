<div class="mb-6">
    <a href="/tenant/vehicles" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Vehicles
    </a>
    <h1 class="text-2xl font-bold text-slate-800">Add New Vehicle</h1>
</div>

<?php if (isset($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-2xl">
    <form action="/tenant/vehicles/add" method="POST" class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">License Plate</label>
                <input type="text" name="license_plate" required placeholder="B-123-ABC" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500 font-mono">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Make / Model</label>
                <div class="flex space-x-2">
                    <input type="text" name="make" required placeholder="Dacia" class="w-1/2 px-3 py-2 border border-slate-300 rounded-md">
                    <input type="text" name="model" required placeholder="Logan" class="w-1/2 px-3 py-2 border border-slate-300 rounded-md">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Current Odometer (KM)</label>
            <input type="number" name="current_odometer" required value="0" class="w-full px-3 py-2 border border-slate-300 rounded-md">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Expiry RCA</label>
                <input type="date" name="expiry_rca" class="w-full px-3 py-2 border border-slate-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Expiry ITP</label>
                <input type="date" name="expiry_itp" class="w-full px-3 py-2 border border-slate-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Expiry Rovigneta</label>
                <input type="date" name="expiry_rovigneta" class="w-full px-3 py-2 border border-slate-300 rounded-md">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                Save Vehicle
            </button>
        </div>
    </form>
</div>
