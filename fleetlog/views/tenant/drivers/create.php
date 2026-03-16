<div class="mb-6">
    <a href="/tenant/drivers" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Drivers
    </a>
    <h1 class="text-2xl font-bold text-slate-800">Add New Driver</h1>
</div>

<?php if (isset($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-md">
    <form action="/tenant/drivers/add" method="POST" class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                <input type="text" name="phone" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="07xx xxx xxx">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" required class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Personal Data & Documentation</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">CNP</label>
                    <input type="text" name="cnp" maxlength="13" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="1234567890123">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">ID Expiry Date</label>
                    <input type="date" name="id_expiry" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Driver License Series</label>
                    <input type="text" name="license_series" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="B123456">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">License Expiry Date</label>
                    <input type="date" name="license_expiry" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <?php 
            $anyDriverEq = in_array('driver', $equipment_config);
        ?>
        <?php if ($anyDriverEq): ?>
        <div class="pt-4 border-t border-slate-100">
            <div class="flex items-center mb-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Personal Inventory (Custody)</h3>
                <span class="ml-2 bg-blue-100 text-blue-700 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">Per Tenant Rule</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="space-y-4">
                    <?php if (($equipment_config['triangles'] ?? 'vehicle') === 'driver'): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-700">Triunghiuri Refl.</span>
                            <select name="has_triangles" class="text-xs border border-slate-300 rounded px-2 py-1">
                                <option value="0">0 x</option>
                                <option value="1">1 x</option>
                                <option value="2" selected>2 x</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (($equipment_config['vest'] ?? 'vehicle') === 'driver'): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-700">Vestă Refl.</span>
                            <select name="has_vest" class="text-xs border border-slate-300 rounded px-2 py-1">
                                <option value="0">0 x</option>
                                <option value="1" selected>1 x</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (($equipment_config['jack'] ?? 'vehicle') === 'driver'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-white rounded-lg transition-colors">
                            <input type="checkbox" name="has_jack" value="1" class="w-4 h-4 text-blue-600 border-slate-300 rounded">
                            <span class="text-sm font-medium text-slate-700">Cric personal</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['spare_wheel'] ?? 'vehicle') === 'driver'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-white rounded-lg transition-colors">
                            <input type="checkbox" name="has_spare_wheel" value="1" class="w-4 h-4 text-blue-600 border-slate-300 rounded">
                            <span class="text-sm font-medium text-slate-700">Roată rezervă</span>
                        </label>
                    <?php endif; ?>
                </div>

                <div class="space-y-4">
                    <?php if (($equipment_config['tow_rope'] ?? 'vehicle') === 'driver'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-white rounded-lg transition-colors">
                            <input type="checkbox" name="has_tow_rope" value="1" class="w-4 h-4 text-blue-600 border-slate-300 rounded">
                            <span class="text-sm font-medium text-slate-700">Șufă tractare</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['jumper_cables'] ?? 'vehicle') === 'driver'): ?>
                        <label class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-white rounded-lg transition-colors">
                            <input type="checkbox" name="has_jumper_cables" value="1" class="w-4 h-4 text-blue-600 border-slate-300 rounded">
                            <span class="text-sm font-medium text-slate-700">Cabluri curent</span>
                        </label>
                    <?php endif; ?>

                    <?php if (($equipment_config['medical_kit'] ?? 'vehicle') === 'driver'): ?>
                        <div class="pt-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Expirare Trusă Medicală</label>
                            <input type="date" name="medical_kit_expiry" class="w-full text-xs px-2 py-1.5 border border-slate-300 rounded">
                        </div>
                    <?php endif; ?>

                    <?php if (($equipment_config['extinguisher'] ?? 'vehicle') === 'driver'): ?>
                        <div class="pt-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Expirare Stingător</label>
                            <input type="date" name="extinguisher_expiry" class="w-full text-xs px-2 py-1.5 border border-slate-300 rounded">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                Save Driver
            </button>
        </div>
    </form>
</div>
