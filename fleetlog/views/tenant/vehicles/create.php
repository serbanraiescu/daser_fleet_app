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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Primary Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <form action="/tenant/vehicles/add" method="POST" class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">License Plate</label>
                        <input type="text" name="license_plate" required placeholder="B-123-ABC" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500 font-mono uppercase">
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
        </div>
    </div>

    <!-- Right Column: Inventory -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Echipament & Inventar
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-700">Triunghiuri Refl.</span>
                    <select name="has_triangles" class="text-xs border border-slate-300 rounded px-2 py-1">
                        <option value="0">0 x</option>
                        <option value="1">1 x</option>
                        <option value="2" selected>2 x</option>
                    </select>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-700">Veste Refl.</span>
                    <select name="has_vest" class="text-xs border border-slate-300 rounded px-2 py-1">
                        <option value="0">0 x</option>
                        <option value="1" selected>1 x</option>
                        <option value="2">2 x</option>
                    </select>
                </div>

                <label class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-slate-50 rounded-lg transition-colors">
                    <input type="checkbox" name="has_jack" value="1" checked class="w-4 h-4 text-blue-600 border-slate-300 rounded">
                    <span class="text-sm font-medium text-slate-700">Cric Functional</span>
                </label>

                <label class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-slate-50 rounded-lg transition-colors">
                    <input type="checkbox" name="has_tow_rope" value="1" checked class="w-4 h-4 text-blue-600 border-slate-300 rounded">
                    <span class="text-sm font-medium text-slate-700">Șufă Tractare</span>
                </label>

                <label class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-slate-50 rounded-lg transition-colors">
                    <input type="checkbox" name="has_jumper_cables" value="1" checked class="w-4 h-4 text-blue-600 border-slate-300 rounded">
                    <span class="text-sm font-medium text-slate-700">Cabluri Curent</span>
                </label>

                <div class="pt-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Expirare Trusă Medicală</label>
                    <input type="date" name="medical_kit_expiry" class="w-full text-xs px-2 py-1.5 border border-slate-300 rounded">
                </div>
            </div>
        </div>
    </div>
</div>
</form>
