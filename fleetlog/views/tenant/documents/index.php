<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-black text-slate-900"><?php echo __('documents'); ?></h1>
            <p class="text-slate-500 mt-1"><?php echo __('protocol_history'); ?></p>
        </div>
        <div>
            <a href="/tenant/documents/handover/add" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <?php echo __('new_handover'); ?>
            </a>
        </div>
    </div>

    <div class="mb-8 border-b border-slate-200">
        <nav class="flex space-x-8">
            <a href="/tenant/documents" class="border-b-2 border-indigo-500 py-4 px-1 text-sm font-black text-indigo-600">
                Toate Documentele
            </a>
            <a href="/tenant/documents/inventory/add" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:text-slate-700 hover:border-slate-300">
                Nou Inventar Șofer
            </a>
        </nav>
    </div>

    <!-- History List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Tip / Număr</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-widest"><?php echo __('vehicle'); ?></th>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-widest"><?php echo __('drivers'); ?></th>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-widest"><?php echo __('event_date'); ?></th>
                        <th class="px-6 py-4 text-right text-xs font-black text-slate-500 uppercase tracking-widest">Acțiuni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p><?php echo __('no_events_recorded'); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $report): 
                            $isInv = ($report['report_type'] ?? 'handover') === 'inventory';
                            $viewUrl = $isInv ? "/tenant/documents/inventory/view/{$report['id']}" : "/tenant/documents/handover/view/{$report['id']}";
                        ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black uppercase <?php echo $isInv ? 'text-blue-600' : 'text-indigo-600'; ?>">
                                            <?php echo $isInv ? 'Inventar' : 'Predare'; ?>
                                        </span>
                                        <span class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($report['document_number']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($report['license_plate']): ?>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($report['license_plate']); ?></span>
                                            <span class="text-xs text-slate-500"><?php echo htmlspecialchars($report['make'] . ' ' . $report['model']); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 italic">Standalone Inventory</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-slate-700"><?php echo htmlspecialchars($report['driver_name']); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-slate-500 font-mono"><?php echo date('d.m.Y H:i', strtotime($report['created_at'])); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo $viewUrl; ?>" target="_blank" class="inline-flex items-center <?php echo $isInv ? 'text-blue-600 bg-blue-50' : 'text-indigo-600 bg-indigo-50'; ?> px-3 py-1.5 rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Printează
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
                </tbody>
            </table>
        </div>
    </div>
</div>
