<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Driver Activity</h1>
        <p class="text-slate-500">Driver performance and vehicle usage analysis.</p>
    </div>
    
    <div class="flex items-center bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
        <?php foreach (['daily' => 'Azi', 'weekly' => 'Săptămână', 'monthly' => 'Lună', 'yearly' => 'An'] as $p => $label): ?>
            <a href="?period=<?php echo $p; ?>" class="px-4 py-2 text-sm font-bold rounded-lg transition-all <?php echo $period === $p ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50'; ?>">
                <?php echo $label; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Șofer</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Distanță Condusă</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Curse Efectuate</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Vehicule Diferite</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($drivers as $d): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs mr-3">
                                <?php echo strtoupper(substr($d['name'], 0, 1)); ?>
                            </div>
                            <div class="font-bold text-slate-900"><?php echo $d['name']; ?></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-lg font-black text-slate-800"><?php echo number_format($d['total_km'] ?? 0); ?></span>
                        <span class="text-xs text-slate-400 font-medium uppercase ml-1">KM</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?php echo $d['trip_count']; ?> curse
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full font-bold text-xs">
                            <?php echo $d['vehicle_count']; ?> vehicule
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($drivers)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">Nu s-a găsit activitate pentru perioada selectată.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
