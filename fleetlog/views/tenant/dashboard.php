<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <a href="/tenant/vehicles" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
        <div class="text-slate-500 text-sm font-medium">Total Vehicles</div>
        <div class="text-3xl font-bold text-slate-900 mt-1"><?php echo $stats['vehicles_count']; ?></div>
    </a>
    <a href="/tenant/drivers" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
        <div class="text-slate-500 text-sm font-medium">Active Drivers</div>
        <div class="text-3xl font-bold text-slate-900 mt-1"><?php echo $stats['drivers_count']; ?></div>
    </a>
    <a href="/tenant/trips" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
        <div class="text-slate-500 text-sm font-medium">In Trip Now</div>
        <div class="text-3xl font-bold text-blue-600 mt-1"><?php echo $stats['active_trips']; ?></div>
    </a>
    <a href="/tenant/damages" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
        <div class="text-slate-500 text-sm font-medium">Recent Damages (30d)</div>
        <div class="text-3xl font-bold <?php echo $stats['recent_damages'] > 0 ? 'text-red-600' : 'text-slate-900'; ?> mt-1">
            <?php echo $stats['recent_damages']; ?>
        </div>
    </a>
</div>
