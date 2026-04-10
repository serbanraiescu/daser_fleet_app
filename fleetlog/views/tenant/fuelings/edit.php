<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center space-x-4">
        <a href="/tenant/fuelings" class="p-2 bg-white rounded-full shadow-sm border border-slate-200 text-slate-500 hover:text-blue-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <h1 class="text-2xl font-black text-slate-800">Editează Alimentare</h1>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 font-bold rounded-r">
            Eroare la salvarea modificărilor.
        </div>
    <?php endif; ?>

    <form action="/tenant/fuelings/edit/<?php echo $fueling['id']; ?>" method="POST" class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h2 class="font-black text-slate-700 uppercase tracking-wider text-sm italic">Detalii Alimentare</h2>
                <span class="px-3 py-1 bg-red-100 text-red-800 text-[10px] font-black uppercase tracking-widest rounded-full">ID #<?php echo $fueling['id']; ?></span>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date & Time -->
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Data & Ora Alimentării</label>
                    <input type="datetime-local" name="created_at" value="<?php echo date('Y-m-d\TH:i', strtotime($fueling['created_at'])); ?>" required
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <!-- Odometer -->
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Kilometraj (KM)</label>
                    <input type="number" name="odometer" value="<?php echo $fueling['odometer']; ?>" required
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <!-- Liters -->
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Cantitate (Litri)</label>
                    <input type="number" step="0.01" name="liters" value="<?php echo $fueling['liters']; ?>" required
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <!-- Total Price -->
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Preț Total (RON)</label>
                    <input type="number" step="0.01" name="total_price" value="<?php echo $fueling['total_price']; ?>" required
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <!-- Is Full -->
                <div class="md:col-span-2 py-4">
                    <label class="flex items-center space-x-3 cursor-pointer group">
                        <div class="relative w-6 h-6">
                            <input type="checkbox" name="is_full" value="1" <?php echo $fueling['is_full'] ? 'checked' : ''; ?>
                                   class="peer appearance-none w-6 h-6 border-2 border-slate-200 rounded-lg checked:border-blue-500 checked:bg-blue-500 transition-all">
                            <svg class="absolute top-1 left-1 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <span class="text-sm font-black text-slate-700 uppercase tracking-wider group-hover:text-blue-600 transition-colors">Plin Rezervor (Is Full)</span>
                    </label>
                </div>
            </div>
        </div>

        <?php if ($fueling['receipt_photo']): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest">Poza Bon</label>
                <div class="relative group rounded-xl overflow-hidden border border-slate-100">
                    <img src="/<?php echo $fueling['receipt_photo']; ?>" class="w-full h-auto max-h-96 object-contain bg-slate-50" />
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <a href="/<?php echo $fueling['receipt_photo']; ?>" target="_blank" class="px-6 py-2 bg-white text-slate-800 font-bold rounded-full shadow-lg hover:scale-105 transition-transform">
                            Vezi Mărime Completă
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="flex space-x-4">
            <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-black py-4 rounded-2xl shadow-lg shadow-slate-200 transition-all transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-sm">
                Salvează Modificările
            </button>
            <a href="/tenant/fuelings" class="flex-1 bg-white border border-slate-200 text-slate-500 hover:text-slate-800 font-bold py-4 rounded-2xl transition-all text-center uppercase tracking-widest text-sm">
                Anulează
            </a>
        </div>
    </form>
</div>
