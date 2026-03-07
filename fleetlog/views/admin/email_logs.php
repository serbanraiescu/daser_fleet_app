<?php
/** @var array $emailLogs */
/** @var int $pendingCount */
/** @var string $activeTab */
/** @var array $templates */
?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Email Gateway & Logs</h1>
        <p class="text-slate-500">Monitorizează și configurează comunicația prin E-mail.</p>
    </div>
    <div class="flex space-x-3">
        <form action="/admin/email/clear-queue" method="POST" onsubmit="return confirm('Sigur vrei să ștergi TOATE e-mailurile în așteptare?');">
            <button type="submit" class="bg-red-50 text-red-700 px-4 py-2 rounded-lg font-bold flex items-center shadow-sm border border-red-200 hover:bg-red-100 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Golire Coadă
            </button>
        </form>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="mb-6 border-b border-slate-200 flex justify-between items-end">
    <nav class="-mb-px flex space-x-8">
        <a href="/admin/email-logs?tab=logs" 
           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm <?php echo $activeTab === 'logs' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'; ?>">
            Mesaje & Activity
        </a>
        <a href="/admin/email-logs?tab=templates" 
           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm <?php echo $activeTab === 'templates' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'; ?>">
            Template-uri E-mail
        </a>
    </nav>
    <div class="pb-3 px-1">
        <a href="/admin/email-templates/run-check" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition-all shadow-md">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Verifică manual expirările
        </a>
    </div>
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
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Destinatar / Subiect</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Dată</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($emailLogs)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-slate-400 italic">Nu există e-mailuri trimise recent.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($emailLogs as $log): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-sm text-slate-800"><?php echo htmlspecialchars($log['recipient']); ?></div>
                                    <div class="text-xs text-slate-500 truncate max-w-sm"><?php echo htmlspecialchars($log['subject']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php 
                                        $statusClass = match($log['status']) {
                                            'success' => 'bg-green-100 text-green-700',
                                            'failed' => 'bg-red-100 text-red-700',
                                            default => 'bg-slate-100 text-slate-700'
                                        };
                                        $statusLabel = match($log['status']) {
                                            'success' => 'Trimis',
                                            'failed' => 'Eșuat',
                                            default => $log['status']
                                        };
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?php echo $statusClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                    <?php if ($log['error_message']): ?>
                                        <div class="text-[9px] text-red-500 mt-1 truncate max-w-[100px]" title="<?php echo htmlspecialchars($log['error_message']); ?>">
                                            <?php echo htmlspecialchars($log['error_message']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-slate-500">
                                    <div><?php echo date('d.m.Y', strtotime($log['created_at'])); ?></div>
                                    <div class="text-[10px]"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100 mb-4 text-center">
                <div class="flex items-center justify-center text-blue-700 font-bold mb-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Statistici Coadă
                </div>
                <div class="text-3xl font-black text-blue-600"><?php echo $pendingCount; ?></div>
                <div class="text-xs text-blue-500 mt-1 uppercase font-bold tracking-wider">E-mailuri în așteptare</div>
            </div>

            <div class="bg-emerald-600 rounded-2xl p-6 text-white shadow-lg overflow-hidden relative">
                <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-emerald-500 opacity-20" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                
                <h3 class="font-bold text-lg mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Notificări E-mail
                </h3>
                <p class="text-sm text-emerald-100 mb-6">Sistemul de e-mail procesează mesajele asincron în coadă la fiecare 5 minute.</p>
                
                <a href="/admin/settings" class="block w-full text-center py-3 bg-white text-emerald-600 font-bold rounded-xl hover:bg-emerald-50 transition-all shadow-md">
                    Setări SMTP
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Templates Tab -->
    <div class="max-w-5xl space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Nume Template</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Zile Alertă</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Destinatar</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Acțiuni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($templates as $tmpl): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800"><?php echo htmlspecialchars($tmpl['name']); ?></div>
                                <div class="text-[10px] text-slate-400 font-mono"><?php echo $tmpl['slug']; ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-mono">
                                    <?php echo $tmpl['alert_days']; ?> zile
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs uppercase font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">
                                    <?php echo $tmpl['recipient_type']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="/admin/email-templates/preview/<?php echo $tmpl['id']; ?>" target="_blank" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Previzualizare">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="/admin/email-templates/edit/<?php echo $tmpl['id']; ?>" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Editează">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
