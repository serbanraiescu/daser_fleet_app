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

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl">
    <form action="/tenant/drivers/add" method="POST" class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Nume Complet</label>
                <input type="text" name="name" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition-all outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Adresa Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition-all outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Telefon</label>
                <input type="text" name="phone" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 bg-slate-50 focus:bg-white transition-all outline-none font-bold text-blue-600" placeholder="07xx xxx xxx">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Parolă Acces</label>
            <input type="password" name="password" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 bg-slate-50 transition-all outline-none">
        </div>

        <div class="pt-6 border-t border-slate-100">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Date Personale & Documente</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">CNP</label>
                    <input type="text" name="cnp" maxlength="13" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 focus:bg-white transition-all outline-none font-mono" placeholder="1234567890123">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Dată Expirare CI</label>
                    <input type="date" name="id_expiry" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Serie Permis</label>
                    <input type="text" name="license_series" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 focus:bg-white outline-none uppercase font-mono" placeholder="B123456">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Dată Expirare Permis</label>
                    <input type="date" name="license_expiry" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 focus:bg-white outline-none">
                </div>
            </div>
        </div>

        <?php 
            $anyDriverEq = false;
            if (isset($equipment_config) && is_array($equipment_config)) {
                $anyDriverEq = in_array('driver', $equipment_config);
            }
        ?>
        <?php if ($anyDriverEq): ?>
        <div class="pt-6 border-t border-slate-100">
            <div class="flex items-center mb-6">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Inventar Personal (În Custodie)</h3>
                <span class="ml-2 bg-blue-100 text-blue-700 text-[8px] font-black px-2 py-0.5 rounded-full uppercase">Regulă Firmă</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php if (($equipment_config['triangles'] ?? 'vehicle') === 'driver'): ?>
                    <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl">
                        <span class="text-sm font-bold text-slate-700">Triunghiuri Refl.</span>
                        <select name="has_triangles" class="text-sm border-none focus:ring-0 bg-transparent font-black text-blue-600">
                            <option value="0">0 x</option>
                            <option value="1">1 x</option>
                            <option value="2" selected>2 x</option>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if (($equipment_config['vest'] ?? 'vehicle') === 'driver'): ?>
                    <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl">
                        <span class="text-sm font-bold text-slate-700">Vestă Refl.</span>
                        <select name="has_vest" class="text-sm border-none focus:ring-0 bg-transparent font-black text-blue-600">
                            <option value="0">0 x</option>
                            <option value="1" selected>1 x</option>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if (($equipment_config['jack'] ?? 'vehicle') === 'driver'): ?>
                    <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                        <input type="checkbox" name="has_jack" value="1" class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                        <span class="text-sm font-bold text-slate-700">Cric personal</span>
                    </label>
                <?php endif; ?>

                <?php if (($equipment_config['spare_wheel'] ?? 'vehicle') === 'driver'): ?>
                    <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                        <input type="checkbox" name="has_spare_wheel" value="1" class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                        <span class="text-sm font-bold text-slate-700">Roată rezervă</span>
                    </label>
                <?php endif; ?>

                <?php if (($equipment_config['tow_rope'] ?? 'vehicle') === 'driver'): ?>
                    <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                        <input type="checkbox" name="has_tow_rope" value="1" class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                        <span class="text-sm font-bold text-slate-700">Șufă tractare</span>
                    </label>
                <?php endif; ?>

                <?php if (($equipment_config['jumper_cables'] ?? 'vehicle') === 'driver'): ?>
                    <label class="flex items-center space-x-3 cursor-pointer p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-xl transition-all">
                        <input type="checkbox" name="has_jumper_cables" value="1" class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg">
                        <span class="text-sm font-bold text-slate-700">Cabluri curent</span>
                    </label>
                <?php endif; ?>

                <?php if (($equipment_config['medical_kit'] ?? 'vehicle') === 'driver'): ?>
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Expirare Trusă Medicală</label>
                        <input type="date" name="medical_kit_expiry" class="w-full text-sm bg-transparent border-none focus:ring-0 p-0 font-bold text-blue-600">
                    </div>
                <?php endif; ?>

                <?php if (($equipment_config['extinguisher'] ?? 'vehicle') === 'driver'): ?>
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Expirare Stingător</label>
                        <input type="date" name="extinguisher_expiry" class="w-full text-sm bg-transparent border-none focus:ring-0 p-0 font-bold text-blue-600">
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="pt-6 border-t border-slate-100 flex justify-end">
            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-10 py-4 rounded-xl font-black text-lg hover:bg-blue-700 transition-all shadow-lg hover:shadow-blue-500/20 uppercase tracking-widest">
                Salvează Șofer
            </button>
        </div>
    </form>
</div>
