<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">System Settings</h1>
    <p class="text-slate-500">Configure global application parameters and maintenance tools.</p>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <?php 
            if ($_GET['success'] === 'test_sent') echo "Test email sent successfully! Please check your inbox.";
            else echo "Settings updated successfully!";
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <?php 
            if ($_GET['error'] === 'test_failed') echo "Failed to send test email. Please check your SMTP settings and server logs.";
            elseif ($_GET['error'] === 'email_empty') echo "Please provide a valid email address for the test.";
            else echo "An error occurred.";
        ?>
    </div>
<?php endif; ?>

<div x-data="{ activeTab: localStorage.getItem('admin_settings_tab') || 'email' }" x-init="$watch('activeTab', value => localStorage.setItem('admin_settings_tab', value))" class="max-w-6xl">
    
    <!-- Tab Navigation -->
    <div class="flex flex-wrap gap-2 mb-6 p-1 bg-slate-100 rounded-2xl w-fit border border-slate-200">
        <button @click="activeTab = 'email'" 
                :class="activeTab === 'email' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                class="px-6 py-2.5 rounded-xl font-bold transition-all flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            Email & Notifications
        </button>
        <button @click="activeTab = 'sms'" 
                :class="activeTab === 'sms' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                class="px-6 py-2.5 rounded-xl font-bold transition-all flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            SMS Gateway
        </button>
        <button @click="activeTab = 'features'" 
                :class="activeTab === 'features' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                class="px-6 py-2.5 rounded-xl font-bold transition-all flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Features
        </button>
        <button @click="activeTab = 'maintenance'" 
                :class="activeTab === 'maintenance' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                class="px-6 py-2.5 rounded-xl font-bold transition-all flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 3.472A2 2 0 0116.183 12H7.817a2 2 0 01-1.95-1.528L5 7m5 4v6m4-6v6M1 10V4a1 1 0 011-1h20a1 1 0 011 1v6a1 1 0 01-1 1H2a1 1 0 01-1-1z"></path></svg>
            Maintenance & Cleanup
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- EMAIL TAB -->
        <div x-show="activeTab === 'email'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">
                <!-- Main Settings -->
                <div class="lg:col-span-2 p-8">
                    <form action="/admin/settings" method="POST" class="space-y-8">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                SMTP Configuration
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">SMTP Host</label>
                                    <input type="text" name="settings[smtp_host]" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? 'mail.daserdesign.ro'); ?>"
                                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">SMTP Port</label>
                                    <input type="text" name="settings[smtp_port]" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '465'); ?>"
                                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">SMTP User</label>
                                    <input type="text" name="settings[smtp_user]" value="<?php echo htmlspecialchars($settings['smtp_user'] ?? 'fleet@daserdesign.ro'); ?>"
                                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">SMTP Password</label>
                                    <div class="relative group">
                                        <input type="password" disabled value="********"
                                               class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-400 outline-none transition-all cursor-not-allowed">
                                        <span class="absolute right-3 top-3 bg-blue-100 text-blue-700 text-[10px] px-2 py-1 rounded-md font-bold uppercase tracking-wider">Managed in .env</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-2 italic">Set <strong>SMTP_PASS</strong> in <code>fleetlog/.env</code> for security.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Encryption</label>
                                    <select name="settings[smtp_enc]" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all cursor-pointer bg-white">
                                        <option value="tls" <?php echo ($settings['smtp_enc'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                        <option value="ssl" <?php echo ($settings['smtp_enc'] ?? 'ssl') === 'ssl' ? 'selected' : ''; ?>>SSL (Port 465)</option>
                                        <option value="none" <?php echo ($settings['smtp_enc'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-100">
                            <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                Sender Information
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">From Email</label>
                                    <input type="email" name="settings[smtp_from_email]" value="<?php echo htmlspecialchars($settings['smtp_from_email'] ?? 'fleet@daserdesign.ro'); ?>"
                                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">From Name</label>
                                    <input type="text" name="settings[smtp_from_name]" value="<?php echo htmlspecialchars($settings['smtp_from_name'] ?? 'FleetLog Notifications'); ?>"
                                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md active:scale-95">
                                Save Email Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Secondary Tools -->
                <div class="bg-slate-50 p-8 space-y-8">
                    <div>
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center text-sm uppercase tracking-wider">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a2 2 0 00-1.96 1.414l-.727 2.903a2 2 0 01-3.566 0l-.727-2.903a2 2 0 00-1.96-1.414l-2.387.477a2 2 0 00-1.022.547l2.261 2.261a2 2 0 002.828 0l2.261-2.261z"></path></svg>
                            Test Deliverability
                        </h3>
                        <form action="/admin/settings/test-email" method="POST" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Recipient Email</label>
                                <input type="email" name="test_email" required placeholder="your-email@example.com"
                                       class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-white focus:border-indigo-500 outline-none transition-all shadow-sm">
                            </div>
                            <button type="submit" class="w-full py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-sm flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Send Test Now
                            </button>
                            <p class="text-[10px] text-slate-400 italic text-center">Important: Save settings before testing!</p>
                        </form>
                    </div>

                    <div class="pt-8 border-t border-slate-200">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center text-sm uppercase tracking-wider">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Connection Info
                        </h3>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-xs space-y-2 text-slate-600">
                            <div class="flex justify-between"><span>Protocol:</span> <span class="font-bold"><?php echo strtoupper($settings['smtp_enc'] ?? 'SSL'); ?></span></div>
                            <div class="flex justify-between"><span>Default Port:</span> <span class="font-bold"><?php echo $settings['smtp_port'] ?? '465'; ?></span></div>
                            <div class="flex justify-between"><span>Mailer:</span> <span class="font-bold">PHPMailer (Native)</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMS TAB -->
        <div x-show="activeTab === 'sms'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-8">
            <div class="max-w-2xl">
                <form action="/admin/settings" method="POST" class="space-y-8">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 mb-2 flex items-center">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            SMS Gateway Configuration
                        </h2>
                        <p class="text-slate-500 mb-8 ml-14">Connect your Android Gateway application to enable automatic SMS notifications.</p>
                        
                        <div class="ml-14 space-y-6">
                            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                                <label class="block text-sm font-bold text-slate-700 mb-3">Gateway Security Key</label>
                                <input type="text" name="settings[sms_gateway_key]" value="<?php echo htmlspecialchars($settings['sms_gateway_key'] ?? ''); ?>"
                                       class="w-full px-5 py-4 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all font-mono tracking-widest text-lg shadow-sm"
                                       placeholder="fleetlog_secret_123">
                                <div class="mt-4 flex items-start text-xs text-slate-500 italic bg-amber-50 p-3 rounded-lg border border-amber-100">
                                    <svg class="w-4 h-4 mr-2 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Această cheie trebuie să fie identică cu cea setată în aplicația Android Gateway pentru a autoriza interfața de trimitere mesaje.</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="px-10 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md active:scale-95">
                                    Save SMS Gateway Key
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- FEATURES TAB -->
        <div x-show="activeTab === 'features'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-8">
            <div class="max-w-2xl">
                <form action="/admin/settings" method="POST">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        Global Feature Toggles
                    </h2>
                    
                    <div class="ml-14 space-y-6">
                        <div class="bg-slate-50 p-8 rounded-3xl border border-slate-200">
                            <label class="flex items-center cursor-pointer group">
                                <div class="relative">
                                    <input type="hidden" name="settings[enable_fueling_photos]" value="0">
                                    <input type="checkbox" name="settings[enable_fueling_photos]" value="1" 
                                           <?php echo ($settings['enable_fueling_photos'] ?? '1') === '1' ? 'checked' : ''; ?>
                                           class="sr-only peer">
                                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <div class="ml-6">
                                    <span class="text-base font-bold text-slate-700 group-hover:text-slate-900 transition-colors">Enable Fueling Receipt Upload</span>
                                    <p class="text-sm text-slate-500 mt-1">Permite șoferilor să încarce poze cu bonul fiscal atunci când înregistrează o alimentare în aplicația mobilă sau web.</p>
                                </div>
                            </label>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="px-10 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md active:scale-95">
                                Save Module Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- MAINTENANCE TAB -->
        <div x-show="activeTab === 'maintenance'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-8">
            <div class="max-w-3xl">
                <h2 class="text-xl font-bold text-slate-800 mb-2 flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 3.472A2 2 0 0116.183 12H7.817a2 2 0 01-1.95-1.528L5 7m5 4v6m4-6v6M1 10V4a1 1 0 011-1h20a1 1 0 011 1v6a1 1 0 01-1 1H2a1 1 0 01-1-1z"></path></svg>
                    </div>
                    Media Storage & Cleanup
                </h2>
                <p class="text-slate-500 mb-10 ml-14">Tools to manage server disk space by archiving or deleting old fueling receipts and damage photos.</p>
                
                <div class="ml-14 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Download Card -->
                    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </div>
                        <h3 class="font-bold text-slate-800 mb-2">Back-up Archive</h3>
                        <p class="text-xs text-slate-500 mb-6 leading-relaxed">Include toate pozele (alimentări și daune) mai vechi de 3 luni într-o arhivă ZIP pentru descărcare.</p>
                        <a href="/admin/media/download" class="inline-flex items-center px-6 py-3 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-900 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download ZIP (> 3 mo)
                        </a>
                    </div>

                    <!-- Delete Card -->
                    <div class="bg-red-50 p-8 rounded-3xl border border-red-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-24 h-24 text-red-200" fill="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 3.472A2 2 0 0116.183 12H7.817a2 2 0 01-1.95-1.528L5 7m5 4v6m4-6v6M1 10V4a1 1 0 011-1h20a1 1 0 011 1v6a1 1 0 01-1 1H2a1 1 0 01-1-1z"></path></svg>
                        </div>
                        <h3 class="font-bold text-red-800 mb-2">Safe Cleanup</h3>
                        <p class="text-xs text-red-600 mb-6 leading-relaxed">Șterge definitiv fișierele mai vechi de 3 luni pentru a elibera spațiul de stocare pe server.</p>
                        <button onclick="if(confirm('Ești sigur că vrei să ȘTERGI DEFINITIV toate pozele mai vechi de 3 luni? Această acțiune este ireversibilă!')) window.location='/admin/media/delete'" class="inline-flex items-center px-6 py-3 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 3.472A2 2 0 0116.183 12H7.817a2 2 0 01-1.95-1.528L5 7m5 4v6m4-6v6M1 10V4a1 1 0 011-1h20a1 1 0 011 1v6a1 1 0 01-1 1H2a1 1 0 01-1-1z"></path></svg>
                            Delete Local Files
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
