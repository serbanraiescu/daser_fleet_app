<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Fleet Vehicles</h1>
    <a href="/tenant/vehicles/add" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
        + Add Vehicle
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Vehicle</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Plate</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Odometer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Expiries (RCA/ITP)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            <?php foreach ($vehicles as $vehicle): ?>
                <tr class="hover:bg-slate-50 group transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-900"><?php echo $vehicle['make'] . ' ' . $vehicle['model']; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded font-mono text-xs font-bold ring-1 ring-slate-200">
                            <?php echo $vehicle['license_plate']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?php echo number_format($vehicle['current_odometer']); ?> KM
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                        <div>RCA: <?php echo $vehicle['expiry_rca']; ?></div>
                        <div>ITP: <?php echo $vehicle['expiry_itp']; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col space-y-2">
                            <?php
                                $status = $vehicle['status'] ?? 'active';
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-red-100 text-red-800',
                                    'service' => 'bg-orange-100 text-orange-800'
                                ];
                                $color = $statusColors[$status] ?? 'bg-slate-100 text-slate-800';
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full justify-center <?php echo $color; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                            
                            <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <?php if ($status !== 'active'): ?>
                                    <a href="/tenant/vehicles/status/<?php echo $vehicle['id']; ?>/active" title="Set Active" class="p-1 rounded bg-green-50 text-green-600 hover:bg-green-100 border border-green-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </a>
                                <?php endif; ?>

                                <?php if ($status !== 'service'): ?>
                                    <a href="/tenant/vehicles/status/<?php echo $vehicle['id']; ?>/service" title="Set to Service" class="p-1 rounded bg-orange-50 text-orange-600 hover:bg-orange-100 border border-orange-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </a>
                                <?php endif; ?>

                                <?php if ($status !== 'inactive'): ?>
                                    <a href="/tenant/vehicles/status/<?php echo $vehicle['id']; ?>/inactive" title="Set Inactive" class="p-1 rounded bg-red-50 text-red-600 hover:bg-red-100 border border-red-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="/tenant/vehicles/edit/<?php echo $vehicle['id']; ?>" class="text-blue-600 hover:text-blue-900">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($vehicles)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-500 italic">
                        No vehicles found in your fleet.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
