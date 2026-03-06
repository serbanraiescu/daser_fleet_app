<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">SMS Gateway</h1>
        <p class="text-slate-500">Monitorizează și configurează comunicația cu aplicația Android.</p>
    </div>
    <div class="flex space-x-3">
        <a href="/read_log.php" target="_blank" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-lg font-bold flex items-center shadow-sm border border-slate-200 hover:bg-slate-200 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Log Diagnostic
        </a>
        <form action="/admin/sms/clear-queue" method="POST" onsubmit="return confirm('Sigur vrei să ștergi TOATE mesajele în așteptare?');">
            <button type="submit" class="bg-red-50 text-red-700 px-4 py-2 rounded-lg font-bold flex items-center shadow-sm border border-red-200 hover:bg-red-100 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Golire Coadă
            </button>
        </form>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="mb-6 border-b border-slate-200">
    <nav class="-mb-px flex space-x-8">
        <a href="/admin/sms-logs?tab=logs" 
           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm <?php echo $activeTab === 'logs' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'; ?>">
            Mesaje & Activity
        </a>
        <a href="/admin/sms-logs?tab=settings" 
           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm <?php echo $activeTab === 'settings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'; ?>">
            Setări Gateway
        </a>
    </nav>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
    </div>
<?php endif; ?>

<?php if ($activeTab === 'logs'): ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Logs Table -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Destinatar</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Mesaj</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Dată</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($smsLogs)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">Nu există mesaje în coadă.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($smsLogs as $sms): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-sm text-slate-700"><?php echo $sms['phone']; ?></td>
                                <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate" title="<?php echo $sms['message']; ?>">
                                    <?php echo $sms['message']; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php 
                                        $statusClass = match($sms['status']) {
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'sending' => 'bg-blue-100 text-blue-700 animate-pulse',
                                            'sent' => 'bg-green-100 text-green-700',
                                            'failed' => 'bg-red-100 text-red-700',
                                        };
                                        $statusLabel = match($sms['status']) {
                                            'pending' => 'În așteptare',
                                            'sending' => 'Se trimite...',
                                            'sent' => 'Trimis',
                                            'failed' => 'Eșuat',
                                        };
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?php echo $statusClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-slate-500">
                                    <div><?php echo date('d.m.Y', strtotime($sms['created_at'])); ?></div>
                                    <div class="text-[10px]"><?php echo date('H:i:s', strtotime($sms['created_at'])); ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100 mb-4">
                <div class="flex items-center text-blue-700 font-bold mb-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Statistici Coadă
                </div>
                <div class="text-3xl font-black text-blue-600"><?php echo $pendingCount; ?></div>
                <div class="text-xs text-blue-500 mt-1 uppercase font-bold tracking-wider">SMS-uri în așteptare</div>
            </div>

            <div class="bg-blue-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden relative">
                <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-blue-500 opacity-20" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 11H7V9h2v2zm4 0h-2V9h2v2zm4 0h-2V9h2v2z"/></svg>
                
                <h3 class="font-bold text-lg mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Trimite SMS Test
                </h3>
                
                <form action="/admin/sms/test-send" method="POST" class="space-y-4 relative z-10">
                    <div>
                        <label class="block text-xs font-bold text-blue-100 uppercase mb-1">Număr Telefon</label>
                        <input type="text" name="test_phone" required placeholder="+407..."
                               class="w-full px-4 py-2 text-sm rounded-lg bg-blue-700 border border-blue-500 text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-blue-100 uppercase mb-1">Mesaj Test</label>
                        <textarea name="test_message" rows="3" 
                                  class="w-full px-4 py-2 text-sm rounded-lg bg-blue-700 border border-blue-500 text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all">Salut! Acesta este un SMS de test de la sistemul FleetLog.</textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-all shadow-md">
                        Adaugă în Coadă
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Settings Tab -->
    <div class="max-w-4xl">
        <form action="/admin/sms/settings" method="POST" class="space-y-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Configurare Gateway
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Cheie API (Secret Key)</label>
                        <input type="password" name="settings[sms_gateway_key]" 
                               value="<?php echo htmlspecialchars($settings['sms_gateway_key'] ?? ''); ?>"
                               class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <p class="mt-1 text-xs text-slate-500 italic">Această cheie trebuie să fie identică cu cea din aplicația Android și din .env (recomandat).</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Prefix Telefon Implicit</label>
                        <input type="text" name="settings[sms_default_prefix]" 
                               placeholder="+40"
                               value="<?php echo htmlspecialchars($settings['sms_default_prefix'] ?? '+40'); ?>"
                               class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Limita Mesaje / Polling</label>
                        <input type="number" name="settings[sms_poll_limit]" 
                               value="<?php echo htmlspecialchars($settings['sms_poll_limit'] ?? '5'); ?>"
                               class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <p class="mt-1 text-xs text-slate-500 italic">Câte mesaje ridică telefonul la o singură interogare.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Interval Status</label>
                        <select name="settings[sms_retry_failed]" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="0" <?php echo ($settings['sms_retry_failed'] ?? '0') === '0' ? 'selected' : ''; ?>>Nu retrimite automat</option>
                            <option value="1" <?php echo ($settings['sms_retry_failed'] ?? '0') === '1' ? 'selected' : ''; ?>>Retrimite mesajele eșuate de 1 dată</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Template Alerte Expirare (Universal)</label>
                        <textarea name="settings[sms_expiry_template]" rows="3" 
                                  class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                  placeholder="Ex: Alerta: {expiry_type} expira pentru {vehicle_plate} pe data de {expiry_date}."><?php echo htmlspecialchars($settings['sms_expiry_template'] ?? 'Alerta: {expiry_type} expira pentru {vehicle_plate} pe data de {expiry_date}.'); ?></textarea>
                        <p class="mt-1 text-xs text-slate-500 italic">Placeholder-e disponibile: <code>{vehicle_plate}</code>, <code>{expiry_type}</code>, <code>{expiry_date}</code>.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Telefon Notificări (Admin)</label>
                        <input type="text" name="settings[sms_admin_phone]" 
                               value="<?php echo htmlspecialchars($settings['sms_admin_phone'] ?? ''); ?>"
                               class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                               placeholder="Numărul unde adminul primește alertele">
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center">
                    <div class="text-sm text-slate-500">
                        <p><b>Cron Job / Trigger:</b> Poți apela manual notificările de expirare:</p>
                        <a href="/admin/sms/trigger-alerts" class="text-blue-600 hover:underline font-bold">Trimite acum alertele de expirare</a>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-md">
                        Salvează Setările
                    </button>
                </div>
            </div>
            
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Detalii Tehnice pentru Aplicație
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-xl border border-slate-200">
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Base API URL</p>
                        <code class="text-blue-600 font-bold break-all">https://fleet.daserdesign.ro/api/</code>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200">
                        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Status Endpoints</p>
                        <p class="text-xs text-slate-600">sms_pending.php, sms_confirm.php</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>
