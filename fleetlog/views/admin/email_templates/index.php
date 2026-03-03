<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Email Templates & Logs</h1>
        <p class="text-slate-500">Customize notification messages and monitor delivery status.</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="/admin/email-templates/run-check" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Run Expiry Check Now
        </a>
    </div>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        Template updated successfully!
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
    <?php foreach ($templates as $t): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col hover:border-blue-300 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?php echo $t['slug']; ?></span>
            </div>
            
            <h3 class="font-bold text-slate-800 text-lg mb-2"><?php echo $t['name']; ?></h3>
            <p class="text-sm text-slate-500 mb-6 flex-1">
                Subject: <span class="text-slate-700 italic">"<?php echo $t['subject']; ?>"</span>
            </p>
            
            <div class="pt-4 border-t flex justify-end">
                <a href="/admin/email-templates/edit/<?php echo $t['id']; ?>" class="text-blue-600 font-bold hover:text-blue-900 flex items-center">
                    Edit Template
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Email Delivery Logs Section -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
        <h2 class="text-lg font-bold text-slate-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Recent Email Delivery Logs
        </h2>
        <span class="text-xs text-slate-500 font-medium font-mono uppercase">Last 50 attempts</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Recipient</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Info / Error</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100 italic">
                <?php if (empty($emailLogs)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400">No email logs found yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($emailLogs as $log): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600 font-mono">
                                <?php echo date('d.m.Y H:i', strtotime($log['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                <?php echo htmlspecialchars($log['recipient']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 truncate max-w-xs">
                                <?php echo htmlspecialchars($log['subject']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if ($log['status'] === 'success'): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase ring-1 ring-green-200">Success</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase ring-1 ring-red-200">Failed</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 italic max-w-sm truncate">
                                <?php echo htmlspecialchars($log['error_message'] ?? '-'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
