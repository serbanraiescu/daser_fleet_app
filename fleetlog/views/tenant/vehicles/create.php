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

<form action="/tenant/vehicles/add" method="POST">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Primary Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">License Plate</label>
                            <input type="text" name="license_plate" required placeholder="B-123-ABC" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono font-bold uppercase transition-all outline-none bg-slate-50 focus:bg-white text-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Make / Model</label>
                            <div class="flex space-x-2">
                                <input type="text" name="make" required placeholder="Dacia" class="w-1/2 px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none bg-slate-50 focus:bg-white">
                                <input type="text" name="model" required placeholder="Logan" class="w-1/2 px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none bg-slate-50 focus:bg-white">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Current Odometer (KM)</label>
                        <input type="number" name="current_odometer" required value="0" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none bg-slate-50 focus:bg-white text-lg font-black text-blue-600">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Expiry RCA</label>
                            <input type="date" name="expiry_rca" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Expiry ITP</label>
                            <input type="date" name="expiry_itp" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Expiry Rovigneta</label>
                            <input type="date" name="expiry_rovigneta" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-8 py-4 rounded-xl font-black text-lg hover:bg-blue-700 transition-all shadow-lg hover:shadow-blue-500/20 uppercase tracking-widest">
                            Save Vehicle
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Inventory -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Echipament & Inventar
                </h3>
                
                <div class="space-y-4">
                    <?php if (($equipment_config['triangles'] ?? 'vehicle') === 'vehicle'): ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-sm font-bold text-slate-700">Triunghiuri Refl.</span>
                            <select name="has_triangles" class="text-sm border-none focus:ring-0 bg-transparent font-black text-blue-600">
                                <option value="0">0 x</option>
                                <option value="1">1 x</option>
                                <option value="2" selected>2 x</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (($equipment_config['vest'] ?? 'vehicle') === 'vehicle'): ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-sm font-bold text-slate-700">Veste Refl.</span>
                            <select name="has_vest" class="text-sm border-none focus:ring-0 bg-transparent font-black text-blue-600">
                                <option value="0">0 x</option>
                                <option value="1" selected>1 x</option>
                                <option value="2">2 x</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (($equipment_config['jack'] ?? 'vehicle') === 'vehicle'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                            <input type="checkbox" name="has_jack" value="1" checked class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                            <span class="text-sm font-bold text-slate-700">Cric Functional</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['tow_rope'] ?? 'vehicle') === 'vehicle'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                            <input type="checkbox" name="has_tow_rope" value="1" checked class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                            <span class="text-sm font-bold text-slate-700">Șufă Tractare</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['jumper_cables'] ?? 'vehicle') === 'vehicle'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                            <input type="checkbox" name="has_jumper_cables" value="1" checked class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                            <span class="text-sm font-bold text-slate-700">Cabluri Curent</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['spare_wheel'] ?? 'vehicle') === 'vehicle'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                            <input type="checkbox" name="has_spare_wheel" value="1" checked class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                            <span class="text-sm font-bold text-slate-700">Roată Rezervă</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['medical_kit'] ?? 'vehicle') === 'vehicle'): ?>
                        <div class="pt-4 border-t border-slate-100">
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 px-1">Expirare Trusă Medicală</label>
                            <input type="date" name="medical_kit_expiry" class="w-full text-sm px-4 py-3 border border-slate-200 rounded-xl bg-slate-50">
                        </div>
                    <?php endif; ?>

                    <?php if (($equipment_config['extinguisher'] ?? 'vehicle') === 'vehicle'): ?>
                        <div class="pt-4 border-t border-slate-100">
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 px-1">Expirare Stingător</label>
                            <input type="date" name="extinguisher_expiry" class="w-full text-sm px-4 py-3 border border-slate-200 rounded-xl bg-slate-50">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>
