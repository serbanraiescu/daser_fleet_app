<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Vehicle Performance</h1>
        <p class="text-slate-500">Fleet activity and efficiency metrics.</p>
    </div>
    
    <div class="flex items-center bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
        <?php foreach (['daily' => 'Azi', 'weekly' => 'Săptămână', 'monthly' => 'Lună', 'yearly' => 'An'] as $p => $label): ?>
            <a href="?period=<?php echo $p; ?>" class="px-4 py-2 text-sm font-bold rounded-lg transition-all <?php echo $period === $p ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <?php echo $label; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php
        $totalKm = 0;
        $totalCost = 0;
        $totalTrips = 0;
        foreach ($vehicles as $v) {
            if ($v['start_km'] && $v['end_km']) $totalKm += ($v['end_km'] - $v['start_km']);
            $totalCost += ($v['total_fuel_cost'] ?? 0);
            $totalTrips += $v['trip_count'];
        }
    ?>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-2">Total Distanță</div>
        <div class="text-3xl font-black text-slate-900"><?php echo number_format($totalKm); ?> <span class="text-lg font-normal text-slate-400">KM</span></div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-2">Cost Combustibil</div>
        <div class="text-3xl font-black text-blue-600"><?php echo number_format($totalCost, 2); ?> <span class="text-lg font-normal text-slate-400">RON</span></div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-2">Curse Totale</div>
        <div class="text-3xl font-black text-slate-900"><?php echo $totalTrips; ?></div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Vehicul</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">KM Parcurși</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Curse</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Alimentat</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Cost Comb.</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Consum Mediu</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($vehicles as $v): ?>
                <?php 
                    $dist = ($v['end_km'] && $v['start_km']) ? ($v['end_km'] - $v['start_km']) : 0;
                    $consumption = ($dist > 0 && $v['total_liters']) ? ($v['total_liters'] / $dist * 100) : null;
                ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-bold text-slate-900"><?php echo $v['license_plate']; ?></div>
                        <div class="text-xs text-slate-500"><?php echo $v['make'] . ' ' . $v['model']; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                        <?php echo number_format($dist); ?> KM
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?php echo $v['trip_count']; ?> curse
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?php echo number_format($v['total_liters'] ?? 0, 2); ?> L
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                        <?php echo number_format($v['total_fuel_cost'] ?? 0, 2); ?> RON
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <?php if ($consumption): ?>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-bold text-xs">
                                <?php echo number_format($consumption, 1); ?> L/100km
                            </span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs italic">N/A</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-100 text-amber-800 text-sm">
    <svg class="w-5 h-5 inline-block mr-1 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <strong>Notă:</strong> Consumul mediu este estimat pe baza alimentărilor din perioadă raportate la distanța parcursă între primul și ultimul log de KM al curselor din acea perioadă.
</div>
