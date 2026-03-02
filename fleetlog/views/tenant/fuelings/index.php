<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Fueling Logs</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Vehicle</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Driver</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Odometer</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Fuel Info</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Full?</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Receipt</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            <?php foreach ($fuelings as $log): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?php echo date('d M Y H:i', strtotime($log['created_at'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                        <?php echo $log['license_plate']; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?php echo $log['driver_name']; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?php echo number_format($log['odometer']); ?> KM
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        <?php echo $log['liters']; ?> Liters
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                        <?php echo number_format($log['total_price'], 2); ?> RON
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <?php if ($log['is_full']): ?>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-[10px] font-bold uppercase rounded">Full</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-slate-100 text-slate-400 text-[10px] font-bold uppercase rounded">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <?php if ($log['receipt_photo']): ?>
                            <a href="/<?php echo $log['receipt_photo']; ?>" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm font-medium underline">
                                View Photo
                            </a>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs italic">No photo</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($fuelings)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-slate-500 italic">
                        No fueling logs recorded yet.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
