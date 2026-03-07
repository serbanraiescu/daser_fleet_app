<div class="space-y-6 animate-fadeIn">
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">System Overview</h1>
            <p class="text-slate-500 mt-1">Global performance metrics for FleetLog platform.</p>
        </div>
        <div class="flex items-center space-x-2 bg-green-50 text-green-700 px-4 py-2 rounded-full font-bold text-sm">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span>SYSTEM LIVE</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Tenants -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-blue-500 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div class="text-4xl font-black text-slate-800"><?php echo $stats['tenants']; ?></div>
                <div class="text-slate-500 font-bold uppercase text-xs tracking-wider mt-1">Companii / Tenants</div>
            </div>
        </div>

        <!-- Vehicles -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-indigo-500 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                </div>
                <div class="text-4xl font-black text-slate-800"><?php echo $stats['vehicles']; ?></div>
                <div class="text-slate-500 font-bold uppercase text-xs tracking-wider mt-1">Vehicule Active</div>
            </div>
        </div>

        <!-- Emails -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div class="flex items-baseline space-x-2">
                    <div class="text-4xl font-black text-slate-800"><?php echo $stats['emails_lifetime']; ?></div>
                    <div class="text-emerald-600 font-bold text-sm">+<?php echo $stats['emails_today']; ?> azi</div>
                </div>
                <div class="text-slate-500 font-bold uppercase text-xs tracking-wider mt-1">Email-uri Trimise</div>
            </div>
        </div>

        <!-- SMS -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-amber-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <div class="flex items-baseline space-x-2">
                    <div class="text-4xl font-black text-slate-800"><?php echo $stats['sms_lifetime']; ?></div>
                    <div class="text-amber-600 font-bold text-sm">+<?php echo $stats['sms_today']; ?> azi</div>
                </div>
                <div class="text-slate-500 font-bold uppercase text-xs tracking-wider mt-1">SMS Trimise</div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Health Card -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-8 rounded-3xl text-white shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z"/></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-xl font-bold opacity-80 uppercase tracking-widest text-xs mb-6">System Health Score</h3>
                <div class="flex items-end space-x-4 mb-8">
                    <span class="text-7xl font-black leading-none"><?php echo $stats['health']; ?>%</span>
                    <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm font-bold border border-green-500/30 mb-2">OPTIMAL</span>
                </div>
                <div class="w-full bg-slate-700/50 rounded-full h-4 mb-8">
                    <div class="bg-gradient-to-r from-blue-400 to-emerald-400 h-4 rounded-full shadow-lg shadow-blue-500/20" style="width: <?php echo $stats['health']; ?>%"></div>
                </div>
                <div class="flex space-x-6">
                    <div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total Uptime</div>
                        <div class="text-xl font-bold"><?php echo $stats['uptime']; ?></div>
                    </div>
                    <div class="border-l border-slate-700 h-10"></div>
                    <div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Status</div>
                        <div class="text-xl font-bold text-emerald-400">All Systems Go</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
            <h3 class="text-xl font-black text-slate-800 mb-6">Quick Management</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="/admin/tenants" class="flex items-center p-4 bg-slate-50 hover:bg-blue-50 rounded-2xl border border-slate-100 hover:border-blue-200 transition-all group">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mr-3 group-hover:bg-blue-500 group-hover:text-white transition-all text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m16-10V7a4 4 0 00-8 0v4m-2 0h12"></path></svg>
                    </div>
                    <span class="font-bold text-slate-700">Tenants</span>
                </a>
                <a href="/admin/status" class="flex items-center p-4 bg-slate-50 hover:bg-emerald-50 rounded-2xl border border-slate-100 hover:border-emerald-200 transition-all group">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mr-3 group-hover:bg-emerald-500 group-hover:text-white transition-all text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="font-bold text-slate-700">Health Check</span>
                </a>
                <a href="/admin/email-logs" class="flex items-center p-4 bg-slate-50 hover:bg-emerald-50 rounded-2xl border border-slate-100 hover:border-emerald-200 transition-all group">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mr-3 group-hover:bg-emerald-500 group-hover:text-white transition-all text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="font-bold text-slate-700">Email Logs</span>
                </a>
                <a href="/admin/sms-logs" class="flex items-center p-4 bg-slate-50 hover:bg-amber-50 rounded-2xl border border-slate-100 hover:border-amber-200 transition-all group">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mr-3 group-hover:bg-amber-500 group-hover:text-white transition-all text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <span class="font-bold text-slate-700">SMS Gateway</span>
                </a>
                <a href="/admin/settings" class="flex items-center p-4 bg-slate-50 hover:bg-slate-200 rounded-2xl border border-slate-100 hover:border-slate-300 transition-all group">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mr-3 group-hover:bg-slate-800 group-hover:text-white transition-all text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <span class="font-bold text-slate-700">Settings</span>
                </a>
            </div>
        </div>
    </div>
</div>
