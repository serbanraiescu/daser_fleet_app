<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">System Settings</h1>
    <p class="text-slate-500">Configure global application parameters like SMTP for email notifications.</p>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php 
            if ($_GET['success'] === 'test_sent') echo "Test email sent successfully! Please check your inbox.";
            else echo "Settings updated successfully!";
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php 
            if ($_GET['error'] === 'test_failed') echo "Failed to send test email. Please check your SMTP settings and server logs.";
            elseif ($_GET['error'] === 'email_empty') echo "Please provide a valid email address for the test.";
            else echo "An error occurred.";
        ?>
    </div>
<?php endif; ?>

<div class="max-w-4xl grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <form action="/admin/settings" method="POST" class="space-y-8">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        SMTP Configuration
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">SMTP Host</label>
                            <input type="text" name="settings[smtp_host]" value="<?php echo !empty($settings['smtp_host']) ? $settings['smtp_host'] : 'mail.daserdesign.ro'; ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">SMTP Port</label>
                            <input type="text" name="settings[smtp_port]" value="<?php echo !empty($settings['smtp_port']) ? $settings['smtp_port'] : '465'; ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">SMTP User</label>
                            <input type="text" name="settings[smtp_user]" value="<?php echo !empty($settings['smtp_user']) ? $settings['smtp_user'] : 'fleet@daserdesign.ro'; ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">SMTP Password</label>
                            <div class="relative">
                                <input type="password" disabled value="********"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-400 outline-none transition-all cursor-not-allowed">
                                <span class="absolute right-3 top-3 bg-blue-100 text-blue-700 text-[10px] px-2 py-1 rounded-md font-bold uppercase">Managed in .env</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-2 italic">For security, set <strong>SMTP_PASS</strong> in your <code>fleetlog/.env</code> file.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Encryption</label>
                            <select name="settings[smtp_enc]" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all cursor-pointer">
                                <option value="tls" <?php echo ($settings['smtp_enc'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo ($settings['smtp_enc'] ?? 'ssl') === 'ssl' ? 'selected' : ''; ?>>SSL (Port 465)</option>
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
                            <input type="email" name="settings[smtp_from_email]" value="<?php echo !empty($settings['smtp_from_email']) ? $settings['smtp_from_email'] : 'fleet@daserdesign.ro'; ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">From Name</label>
                            <input type="text" name="settings[smtp_from_name]" value="<?php echo !empty($settings['smtp_from_name']) ? $settings['smtp_from_name'] : 'FleetLog Notifications'; ?>"
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
    </div>

    <div class="space-y-6">
        <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 shadow-sm">
            <h3 class="font-bold text-indigo-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a2 2 0 00-1.96 1.414l-.727 2.903a2 2 0 01-3.566 0l-.727-2.903a2 2 0 00-1.96-1.414l-2.387.477a2 2 0 00-1.022.547l2.261 2.261a2 2 0 002.828 0l2.261-2.261z"></path></svg>
                Send Test Email
            </h3>
            <form action="/admin/settings/test-email" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-indigo-700 uppercase mb-1">Recipient Email</label>
                    <input type="email" name="test_email" required placeholder="your-email@example.com"
                           class="w-full px-4 py-2 text-sm rounded-lg border border-indigo-200 focus:border-indigo-500 outline-none transition-all">
                </div>
                <button type="submit" class="w-full py-3 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-sm">
                    Send Test Now
                </button>
            </form>
            <p class="text-[10px] text-indigo-500 mt-4 italic text-center">Save settings first before testing!</p>
        </div>

        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
            <h3 class="font-bold text-blue-800 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Current Config
            </h3>
            <ul class="text-xs text-blue-700 space-y-2">
                <li><strong>Encryption:</strong> SSL (Port 465)</li>
                <li><strong>User:</strong> fleet@daserdesign.ro</li>
                <li><strong>Server:</strong> mail.daserdesign.ro</li>
            </ul>
        </div>
    </div>
</div>
