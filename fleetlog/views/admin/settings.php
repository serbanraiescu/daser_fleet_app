<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">System Settings</h1>
    <p class="text-slate-500">Configure global application parameters like SMTP for email notifications.</p>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        Settings updated successfully!
    </div>
<?php endif; ?>

<div class="max-w-4xl bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
    <form action="/admin/settings" method="POST" class="space-y-8">
        <div>
            <h2 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                SMTP Configuration
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">SMTP Host</label>
                    <input type="text" name="settings[smtp_host]" value="<?php echo $settings['smtp_host']; ?>" placeholder="smtp.mailtrap.io"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">SMTP Port</label>
                    <input type="text" name="settings[smtp_port]" value="<?php echo $settings['smtp_port']; ?>" placeholder="587"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">SMTP User</label>
                    <input type="text" name="settings[smtp_user]" value="<?php echo $settings['smtp_user']; ?>"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">SMTP Password</label>
                    <input type="password" name="settings[smtp_pass]" value="<?php echo $settings['smtp_pass']; ?>"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Encryption</label>
                    <select name="settings[smtp_enc]" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all cursor-pointer">
                        <option value="tls" <?php echo ($settings['smtp_enc'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                        <option value="ssl" <?php echo ($settings['smtp_enc'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                        <option value="none" <?php echo ($settings['smtp_enc'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                    </select>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Sender Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">From Email</label>
                    <input type="email" name="settings[smtp_from_email]" value="<?php echo $settings['smtp_from_email']; ?>" placeholder="noreply@fleetlog.com"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">From Name</label>
                    <input type="text" name="settings[smtp_from_name]" value="<?php echo $settings['smtp_from_name']; ?>" placeholder="FleetLog Alerts"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>
            </div>
        </div>

        <div class="pt-6 border-t flex items-center justify-end">
            <button type="submit" class="px-10 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md">
                Save System Settings
            </button>
        </div>
    </form>
</div>
