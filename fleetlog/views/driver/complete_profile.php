<div class="p-4 max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="bg-blue-600 p-6 text-white text-center">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h1 class="text-2xl font-bold"><?php echo __('complete_profile_title'); ?></h1>
            <p class="text-blue-100 mt-2"><?php echo __('complete_profile_help'); ?></p>
        </div>

        <form action="/driver/complete-profile" method="POST" class="p-6 space-y-5">
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md text-sm">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide"><?php echo __('cnp'); ?></label>
                <input type="text" name="cnp" maxlength="13" required 
                       value="<?php echo $user['cnp'] ?? ''; ?>" 
                       placeholder="1234567890123"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-lg">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide"><?php echo __('id_expiry_date'); ?></label>
                <input type="date" name="id_expiry" required 
                       value="<?php echo $user['id_expiry'] ?? ''; ?>"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-lg">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide"><?php echo __('license_series_label'); ?></label>
                <input type="text" name="license_series" required 
                       value="<?php echo $user['license_series'] ?? ''; ?>"
                       placeholder="Ex: B123456"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-lg">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide"><?php echo __('license_expiry_date'); ?></label>
                <input type="date" name="license_expiry" required 
                       value="<?php echo $user['license_expiry'] ?? ''; ?>"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-lg">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transform active:scale-95 transition-all text-xl">
                    <?php echo __('save_continue'); ?>
                </button>
            </div>
        </form>
    </div>
    
    <div class="mt-6 text-center text-slate-500 text-sm">
        <p><?php echo __('need_help'); ?></p>
        <a href="/logout" class="text-blue-600 font-bold mt-2 block"><?php echo __('logout'); ?></a>
    </div>
</div>
