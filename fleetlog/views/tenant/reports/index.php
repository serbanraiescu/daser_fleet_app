<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-800">Reports & Exports</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Trip Report -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Trip logs</h3>
            <p class="text-slate-500 text-sm mb-6">Detailed history of all vehicle segments, including KM and notes.</p>
            <a href="/tenant/reports/trips/export" class="inline-block w-full text-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                Export CSV
            </a>
        </div>

        <!-- Fuel Report -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Fuel Consumption</h3>
            <p class="text-slate-500 text-sm mb-6">Summary of liters and costs per vehicle based on closed trips.</p>
            <button disabled class="w-full text-center px-4 py-2 bg-slate-100 text-slate-400 font-medium rounded-lg cursor-not-allowed">
                V2 Feature
            </button>
        </div>

        <!-- Damage Report -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Incident Log</h3>
            <p class="text-slate-500 text-sm mb-6">Overview of all reported damages and their current statuses.</p>
            <button disabled class="w-full text-center px-4 py-2 bg-slate-100 text-slate-400 font-medium rounded-lg cursor-not-allowed">
                V2 Feature
            </button>
        </div>
    </div>
</div>
