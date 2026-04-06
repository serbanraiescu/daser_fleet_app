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

<form action="/tenant/vehicles/edit/<?php echo $vehicle['id']; ?>" method="POST">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Primary Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">License Plate</label>
                            <input type="text" name="license_plate" required value="<?php echo $vehicle['license_plate']; ?>" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono font-bold uppercase bg-slate-50 focus:bg-white text-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Current Odometer (KM)</label>
                            <input type="number" name="current_odometer" required value="<?php echo $vehicle['current_odometer']; ?>" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white text-lg font-black text-blue-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Make</label>
                            <input type="text" name="make" required value="<?php echo $vehicle['make']; ?>" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Model</label>
                            <input type="text" name="model" required value="<?php echo $vehicle['model']; ?>" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Unique QR Code</label>
                        <div class="flex items-center space-x-3">
                            <input type="text" name="qr_code" readonly value="<?php echo $vehicle['qr_code']; ?>" class="w-full px-4 py-3 border border-slate-100 bg-slate-50 rounded-xl text-slate-400 font-mono text-sm">
                            <?php 
                                $qrUrl = "/qr/generate?sf=8&d=" . urlencode("https://" . ($_SERVER['HTTP_HOST'] ?? 'fleet.daserdesign.ro') . "/driver/start-trip?qr=" . $vehicle['qr_code']);
                            ?>
                            <a href="<?php echo $qrUrl; ?>" target="_blank" title="View Large QR" class="flex-shrink-0 hover:scale-105 transition-transform">
                                <img src="<?php echo $qrUrl; ?>" alt="QR" class="w-12 h-12 border-2 border-white shadow-md rounded-lg">
                            </a>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Documentation Expiry Dates</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Expiry RCA</label>
                                <input type="date" name="expiry_rca" value="<?php echo $vehicle['expiry_rca']; ?>" class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Expiry ITP</label>
                                <input type="date" name="expiry_itp" value="<?php echo $vehicle['expiry_itp']; ?>" class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Expiry Rovigneta</label>
                                <input type="date" name="expiry_rovigneta" value="<?php echo $vehicle['expiry_rovigneta']; ?>" class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100">
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Vehicle Status</label>
                        <select name="status" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 bg-slate-50 font-bold text-slate-700">
                            <option value="active" <?php echo $vehicle['status'] === 'active' ? 'selected' : ''; ?>>Active (Available for trips)</option>
                            <option value="inactive" <?php echo $vehicle['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive (Hidden from drivers)</option>
                            <option value="service" <?php echo $vehicle['status'] === 'service' ? 'selected' : ''; ?>>In Service (Blocked for new trips)</option>
                        </select>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-10 py-4 rounded-xl font-black hover:bg-blue-700 transition-all shadow-lg hover:shadow-blue-500/20 uppercase tracking-widest text-lg">
                            Update Vehicle
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Inventory -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Echipament & Inventar
                </h3>
                
                <div class="space-y-4">
                    <?php if (($equipment_config['triangles'] ?? 'vehicle') === 'vehicle'): ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl">
                            <span class="text-sm font-bold text-slate-700">Triunghiuri Refl.</span>
                            <select name="has_triangles" class="text-sm border-none focus:ring-0 bg-transparent font-black text-blue-600">
                                <option value="0" <?php echo ($vehicle['has_triangles'] ?? 0) == 0 ? 'selected' : ''; ?>>0 x</option>
                                <option value="1" <?php echo ($vehicle['has_triangles'] ?? 0) == 1 ? 'selected' : ''; ?>>1 x</option>
                                <option value="2" <?php echo ($vehicle['has_triangles'] ?? 0) == 2 ? 'selected' : ''; ?>>2 x</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (($equipment_config['vest'] ?? 'vehicle') === 'vehicle'): ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl">
                            <span class="text-sm font-bold text-slate-700">Veste Refl.</span>
                            <select name="has_vest" class="text-sm border-none focus:ring-0 bg-transparent font-black text-blue-600">
                                <option value="0" <?php echo ($vehicle['has_vest'] ?? 0) == 0 ? 'selected' : ''; ?>>0 x</option>
                                <option value="1" <?php echo ($vehicle['has_vest'] ?? 0) == 1 ? 'selected' : ''; ?>>1 x</option>
                                <option value="2" <?php echo ($vehicle['has_vest'] ?? 0) == 2 ? 'selected' : ''; ?>>2 x</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (($equipment_config['jack'] ?? 'vehicle') === 'vehicle'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                            <input type="checkbox" name="has_jack" value="1" <?php echo !empty($vehicle['has_jack']) ? 'checked' : ''; ?> class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                            <span class="text-sm font-bold text-slate-700">Cric Functional</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['tow_rope'] ?? 'vehicle') === 'vehicle'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                            <input type="checkbox" name="has_tow_rope" value="1" <?php echo !empty($vehicle['has_tow_rope']) ? 'checked' : ''; ?> class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                            <span class="text-sm font-bold text-slate-700">Șufă Tractare</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['jumper_cables'] ?? 'vehicle') === 'vehicle'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                            <input type="checkbox" name="has_jumper_cables" value="1" <?php echo !empty($vehicle['has_jumper_cables']) ? 'checked' : ''; ?> class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                            <span class="text-sm font-bold text-slate-700">Cabluri Curent</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['spare_wheel'] ?? 'vehicle') === 'vehicle'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                            <input type="checkbox" name="has_spare_wheel" value="1" <?php echo (isset($vehicle['has_spare_wheel']) ? (bool)$vehicle['has_spare_wheel'] : true) ? 'checked' : ''; ?> class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                            <span class="text-sm font-bold text-slate-700">Roată Rezervă</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['medical_kit'] ?? 'vehicle') === 'vehicle'): ?>
                        <div class="pt-4 border-t border-slate-100">
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 px-1">Expirare Trusă Medicală</label>
                            <input type="date" name="medical_kit_expiry" value="<?php echo $vehicle['medical_kit_expiry'] ?? ''; ?>" class="w-full text-sm px-4 py-3 border border-slate-200 rounded-xl bg-slate-50">
                        </div>
                    <?php endif; ?>

                    <?php if (($equipment_config['extinguisher'] ?? 'vehicle') === 'vehicle'): ?>
                        <div class="pt-4 border-t border-slate-100">
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 px-1">Expirare Stingător</label>
                            <input type="date" name="extinguisher_expiry" value="<?php echo $vehicle['extinguisher_expiry'] ?? ''; ?>" class="w-full text-sm px-4 py-3 border border-slate-200 rounded-xl bg-slate-50">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>
