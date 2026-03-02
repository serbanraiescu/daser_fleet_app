<div class="space-y-6 text-slate-800">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <div class="text-sm text-slate-500"><?php echo date('d M Y'); ?></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="text-slate-500 text-sm font-medium">Total Vehicles</div>
            <div class="text-3xl font-bold mt-1"><?php echo $stats['vehicles_count']; ?></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="text-slate-500 text-sm font-medium">Active Drivers</div>
            <div class="text-3xl font-bold mt-1"><?php echo $stats['drivers_count']; ?></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="text-slate-500 text-sm font-medium">Active Trips</div>
            <div class="text-3xl font-bold mt-1 text-blue-600"><?php echo $stats['active_trips']; ?></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="text-slate-500 text-sm font-medium">Weekly Incidents</div>
            <div class="text-3xl font-bold mt-1 <?php echo $stats['recent_damages'] > 0 ? 'text-red-600' : ''; ?>">
                <?php echo $stats['recent_damages']; ?>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold">Recent Activity</h3>
                <a href="/tenant/trips" class="text-xs text-blue-600 hover:underline">View All</a>
            </div>
            <div class="p-6 text-slate-500 text-center italic">
                Logs and reports will appear here as trips are recorded.
            </div>
        </div>
        
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <h3 class="font-bold mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="/tenant/vehicles/add" class="block w-full text-center py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium transition">Add Vehicle</a>
                    <a href="/tenant/drivers/add" class="block w-full text-center py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium transition">Add Driver</a>
                    <a href="/tenant/reports" class="block w-full text-center py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg text-sm font-medium transition">Generate Reports</a>
                </div>
            </div>
        </div>
    </div>
</div>
