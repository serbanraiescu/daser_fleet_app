<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 italic uppercase tracking-tighter">Driver Activity</h1>
        <p class="text-slate-500">Driver performance and vehicle usage analysis.</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-2">
        <?php if ($period === 'monthly' || $period === 'yearly'): ?>
            <form class="flex items-center space-x-2 mr-4 bg-white p-1 rounded-xl border border-slate-200">
                <input type="hidden" name="period" value="<?php echo $period; ?>">
                
                <?php if ($period === 'monthly'): ?>
                    <select name="month" class="text-sm font-bold bg-transparent outline-none px-2 cursor-pointer border-r border-slate-100">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <?php $mPadded = str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                            <option value="<?php echo $mPadded; ?>" <?php echo $selected_month == $mPadded ? 'selected' : ''; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                <?php endif; ?>

                <select name="year" class="text-sm font-bold bg-transparent outline-none px-2 cursor-pointer">
                    <?php for ($y = date('Y'); $y >= 2024; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo $selected_year == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>

                <button type="submit" class="p-1 px-3 bg-slate-800 text-white rounded-lg text-xs font-bold hover:bg-black transition-colors">
                    Filtrează
                </button>
            </form>
        <?php endif; ?>

        <div class="flex items-center bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
            <?php foreach (['daily' => 'Azi', 'weekly' => 'Săptămână', 'monthly' => 'Lună', 'yearly' => 'An'] as $p => $label): ?>
                <a href="?period=<?php echo $p; ?>" class="px-4 py-2 text-sm font-bold rounded-lg transition-all <?php echo $period === $p ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50'; ?>">
                    <?php echo $label; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php 
function renderDriverTable($drivers, $period, $selected_month, $selected_year, $isArchived = false) {
    if (empty($drivers)) {
        echo '<div class="py-10 text-center text-slate-400 italic bg-white rounded-2xl border border-slate-200 border-dashed">Nu s-a găsit activitate pentru această categorie.</div>';
        return;
    }
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden <?php echo $isArchived ? 'opacity-70 grayscale-[0.3]' : ''; ?>">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="<?php echo $isArchived ? 'bg-slate-100' : 'bg-slate-50'; ?>">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Șofer</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Distanță</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Alimentat</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Consum Mediu</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Curse</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Daune</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Vehicule Diferite</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Acțiuni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($drivers as $d): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full <?php echo $isArchived ? 'bg-slate-200 text-slate-600' : 'bg-indigo-100 text-indigo-700'; ?> flex items-center justify-center font-bold text-xs mr-3">
                                    <?php echo strtoupper(substr($d['name'] ?? 'U', 0, 1)); ?>
                                </div>
                                <div class="font-bold text-slate-900"><?php echo $d['name']; ?></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-lg font-black text-slate-800 whitespace-nowrap"><?php echo number_format($d['total_km'] ?? 0); ?></span>
                            <span class="text-xs text-slate-400 font-medium uppercase ml-1">KM</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="whitespace-nowrap">
                                <span class="text-sm font-bold text-slate-700"><?php echo number_format($d['total_liters'] ?? 0, 1); ?></span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase ml-1">Litri</span>
                            </div>
                            <div class="text-[10px] text-slate-400 font-medium whitespace-nowrap"><?php echo number_format($d['total_fuel_cost'] ?? 0, 2); ?> RON</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                                $avg = 0;
                                if (($d['total_km'] ?? 0) > 0 && ($d['total_liters'] ?? 0) > 0) {
                                    $avg = ($d['total_liters'] / $d['total_km']) * 100;
                                }
                            ?>
                            <div class="flex items-center">
                                <span class="text-lg font-black <?php echo $avg > 12 ? 'text-amber-600' : 'text-emerald-600'; ?>">
                                    <?php echo $avg > 0 ? number_format($avg, 2) : '---'; ?>
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase ml-1">L/100KM</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            <?php echo $d['trip_count']; ?> curse
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold <?php echo ($d['damage_count'] ?? 0) > 0 ? 'text-red-600' : 'text-slate-400'; ?>">
                            <?php echo $d['damage_count'] ?? 0; ?> daune
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full font-bold text-xs">
                                <?php echo $d['vehicle_count']; ?> vehicule
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="/tenant/reports/driver/<?php echo $d['id']; ?>?period=<?php echo $period; ?>&month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>" class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-2 rounded-lg transition-all hover:shadow-md border border-indigo-100">
                                Datalii
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php 
} 
?>

<div class="space-y-12">
    <!-- Active Drivers -->
    <div>
        <div class="flex items-center mb-4 gap-3 px-2">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <h2 class="text-lg font-black text-slate-800 uppercase tracking-tighter italic">Șoferi Activi</h2>
        </div>
        <?php renderDriverTable($activeDrivers, $period, $selected_month, $selected_year); ?>
    </div>

    <!-- Archived Drivers -->
    <?php if (!empty($archivedDrivers)): ?>
    <div>
        <div class="flex items-center mb-4 gap-3 px-2">
            <div class="p-2 bg-slate-100 text-slate-500 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
            </div>
            <h2 class="text-lg font-black text-slate-500 uppercase tracking-tighter italic">Arhivă Șoferi</h2>
        </div>
        <?php renderDriverTable($archivedDrivers, $period, $selected_month, $selected_year, true); ?>
    </div>
    <?php endif; ?>
</div>
