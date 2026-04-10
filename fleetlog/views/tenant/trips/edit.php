<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center space-x-4">
        <a href="/tenant/trips" class="p-2 bg-white rounded-full shadow-sm border border-slate-200 text-slate-500 hover:text-blue-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <h1 class="text-2xl font-black text-slate-800">Editează Foaie de Parcurs</h1>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 font-bold rounded-r">
            Eroare la salvarea modificărilor.
        </div>
    <?php endif; ?>

    <form action="/tenant/trips/edit/<?php echo $trip['id']; ?>" method="POST" class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h2 class="font-black text-slate-700 uppercase tracking-wider text-sm italic">Informații Generale</h2>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-[10px] font-black uppercase tracking-widest rounded-full">ID #<?php echo $trip['id']; ?></span>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Vehicul</label>
                    <select name="vehicle_id" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <?php foreach ($vehicles as $v): ?>
                            <option value="<?php echo $v['id']; ?>" <?php echo $v['id'] == $trip['vehicle_id'] ? 'selected' : ''; ?>>
                                <?php echo $v['license_plate']; ?> (<?php echo $v['make']; ?> <?php echo $v['model']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Tip Cursă</label>
                    <select name="type" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="NAVETA" <?php echo $trip['type'] === 'NAVETA' ? 'selected' : ''; ?>>NAVETA</option>
                        <option value="CURSE" <?php echo $trip['type'] === 'CURSE' ? 'selected' : ''; ?>>CURSE</option>
                        <option value="LIVRARE_SPECIALA" <?php echo $trip['type'] === 'LIVRARE_SPECIALA' ? 'selected' : ''; ?>>LIVRARE SPECIALĂ</option>
                        <option value="SERVICE" <?php echo $trip['type'] === 'SERVICE' ? 'selected' : ''; ?>>SERVICE</option>
                        <option value="ALTE" <?php echo $trip['type'] === 'ALTE' ? 'selected' : ''; ?>>ALTE</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Status</label>
                    <select name="status" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="open" <?php echo $trip['status'] === 'open' ? 'selected' : ''; ?>>DESCHISĂ (OPEN)</option>
                        <option value="closed" <?php echo $trip['status'] === 'closed' ? 'selected' : ''; ?>>ÎNCHISĂ (CLOSED)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Start Details -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-emerald-50 border-b border-emerald-100 flex items-center">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></div>
                    <h3 class="text-xs font-black text-emerald-800 uppercase tracking-widest">Pornire (Start)</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Data & Ora Pornire</label>
                        <input type="datetime-local" name="start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($trip['start_time'])); ?>" required
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kilometraj Pornire (KM)</label>
                        <input type="number" name="start_km" value="<?php echo $trip['start_km']; ?>" required
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <!-- End Details -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-blue-50 border-b border-blue-100 flex items-center">
                    <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                    <h3 class="text-xs font-black text-blue-800 uppercase tracking-widest">Sosire (End)</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Data & Ora Sosire</label>
                        <input type="datetime-local" name="end_time" value="<?php echo $trip['end_time'] ? date('Y-m-d\TH:i', strtotime($trip['end_time'])) : ''; ?>"
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kilometraj Sosire (KM)</label>
                        <input type="number" name="end_km" value="<?php echo $trip['end_km']; ?>"
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-2">
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Note / Detalii Rută</label>
            <textarea name="notes" rows="4" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"><?php echo $trip['notes']; ?></textarea>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-black py-4 rounded-2xl shadow-lg shadow-slate-200 transition-all transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-sm">
                Salvează Modificările
            </button>
            <a href="/tenant/trips" class="flex-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-800 font-bold py-4 rounded-2xl transition-all text-center uppercase tracking-widest text-sm">
                Anulează
            </a>
        </div>
    </form>
</div>
