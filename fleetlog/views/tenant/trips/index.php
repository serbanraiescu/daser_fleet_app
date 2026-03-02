<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Fleet Trip Logs</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Driver</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Vehicle</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Start</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">End</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Distance</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            <?php foreach ($trips as $trip): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-slate-900"><?php echo $trip['driver_name'] ?? 'Unknown'; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-slate-600 font-mono"><?php echo $trip['license_plate'] ?? 'Unknown'; ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded bg-slate-100 text-slate-600 border border-slate-200">
                            <?php echo $trip['type'] ?? 'ALTE'; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                        <div><?php echo $trip['start_time']; ?></div>
                        <div class="font-bold"><?php echo number_format($trip['start_km']); ?> KM</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                        <div><?php echo $trip['end_time'] ?? '-'; ?></div>
                        <div class="font-bold"><?php echo $trip['end_km'] ? number_format($trip['end_km']) . ' KM' : '-'; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-bold">
                        <?php echo $trip['end_km'] ? ($trip['end_km'] - $trip['start_km']) . ' KM' : '-'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $trip['status'] === 'open' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800'; ?>">
                            <?php echo ucfirst($trip['status']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($trips)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-500 italic">
                        No trips recorded yet.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
