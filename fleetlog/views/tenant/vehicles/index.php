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
                <tr class="hover:bg-slate-50">
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
                        <?php
                            $status = $vehicle['status'] ?? 'active';
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'inactive' => 'bg-red-100 text-red-800',
                                'service' => 'bg-orange-100 text-orange-800'
                            ];
                            $color = $statusColors[$status] ?? 'bg-slate-100 text-slate-800';
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
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
