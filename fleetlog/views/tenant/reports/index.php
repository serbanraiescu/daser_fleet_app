<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Fleet Analysis</h1>
    <p class="text-slate-500">Overview of your fleet performance and detailed logs.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Vehicle Reports -->
    <a href="/tenant/reports/vehicle" class="group bg-white p-8 rounded-2xl shadow-sm border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
            </div>
            <svg class="w-6 h-6 text-slate-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Vehicle Reports</h3>
        <p class="text-slate-600 text-sm">Analyze distance, fuel consumption (L/100km), and fuel costs per vehicle.</p>
    </a>

    <!-- Driver Reports -->
    <a href="/tenant/reports/driver" class="group bg-white p-8 rounded-2xl shadow-sm border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <svg class="w-6 h-6 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Driver Activity</h3>
        <p class="text-slate-600 text-sm">Monitor KM driven, vehicles used, and total trips per driver.</p>
    </a>
</div>

<div class="mt-12 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    <h3 class="text-lg font-bold text-slate-800 mb-4">Data Exports</h3>
    <div class="flex flex-wrap gap-4">
        <a href="/tenant/reports/export-trips" class="flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-all font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export All Trips (CSV)
        </a>
    </div>
</div>
