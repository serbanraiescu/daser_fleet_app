<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'FleetLog'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal" x-data="{ sidebarOpen: false }">

    <!-- Impersonation Indicator -->
    <?php if (\FleetLog\Core\Auth::isImpersonating()): ?>
        <div class="bg-yellow-100 border-b border-yellow-200 py-2 px-4 flex justify-between items-center text-sm font-medium text-yellow-800">
            <span>
                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Impersonating Tenant: <strong><?php echo \FleetLog\Core\Auth::tenantId(); ?></strong>
            </span>
            <a href="/admin/stop-impersonation" class="bg-yellow-800 text-white px-3 py-1 rounded hover:bg-yellow-900 transition-colors">Stop</a>
        </div>
    <?php endif; ?>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-2xl font-semibold uppercase tracking-wider">FleetLog</span>
                <button @click="sidebarOpen = false" class="lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <nav class="mt-4 px-4 space-y-1">
                <?php if (\FleetLog\Core\RBAC::isSuperAdmin() && !\FleetLog\Core\Auth::isImpersonating()): ?>
                    <a href="/admin/tenants" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Tenants
                    </a>
                    <a href="/admin/email-templates" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Email Templates
                    </a>
                    <a href="/admin/settings" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        System Settings
                    </a>
                <?php endif; ?>

                <?php 
                    $isSuperAdmin = \FleetLog\Core\RBAC::isSuperAdmin();
                    $isImpersonating = \FleetLog\Core\Auth::isImpersonating();
                    $showTenantLinks = (\FleetLog\Core\RBAC::isTenantAdmin() || ($isSuperAdmin && $isImpersonating));
                ?>

                <?php if ($showTenantLinks): ?>
                    <a href="/tenant/dashboard" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    <a href="/tenant/vehicles" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Vehicles
                    </a>
                    <a href="/tenant/drivers" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Drivers
                    </a>
                    <a href="/tenant/trips" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7"></path></svg>
                        Trip Logs
                    </a>
                    <a href="/tenant/fuelings" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Fueling Logs
                    </a>
                    <a href="/tenant/damages" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Damages
                    </a>
                    <a href="/tenant/reports" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 10-8 0v2m8-2v2m4-6h6m-3-3v6m-9-3h3m2 0h2M3 21h18M3 7h18"></path></svg>
                        Reports
                    </a>
                    <a href="/tenant/settings" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group border-t border-slate-800 mt-2 pt-2">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Settings
                    </a>
                <?php endif; ?>

                <?php if (\FleetLog\Core\RBAC::isDriver()): ?>
                    <a href="/driver/dashboard" class="flex items-center px-4 py-2 hover:bg-slate-800 rounded-lg group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Driver Deck
                    </a>
                <?php endif; ?>

                <a href="/logout" class="flex items-center px-4 py-2 hover:bg-red-800 rounded-lg group mt-10">
                    <svg class="w-5 h-5 mr-3 text-red-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow px-6 py-3 flex items-center justify-between border-b border-slate-200">
                <button @click="sidebarOpen = true" class="lg:hidden p-1 text-slate-500 hover:bg-slate-100 rounded">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-slate-600"><?php echo $currentUser['name'] ?? 'Guest'; ?></span>
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center border border-slate-300">
                        <span class="text-xs font-bold text-slate-500"><?php echo strtoupper(substr($currentUser['name'] ?? 'G', 0, 1)); ?></span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6">
                <!-- Damage Notification Bar -->
                <?php if (isset($newDamagesCount) && $newDamagesCount > 0): ?>
                    <div class="mb-6 bg-red-600 shadow-lg rounded-2xl p-4 flex items-center justify-between text-white animate-pulse">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-red-500 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold">Atenție: Daune noi raportate!</h4>
                                <p class="text-red-100 text-sm">Aveți <?php echo $newDamagesCount; ?> raportări noi care necesită atenție.</p>
                            </div>
                        </div>
                        <a href="/tenant/damages" class="px-6 py-2 bg-white text-red-600 font-bold rounded-xl hover:bg-red-50 transition-colors shadow-sm">
                            Vezi Rapoarte
                        </a>
                    </div>
                <?php endif; ?>

                <?php echo $content; ?>
            </main>
        </div>
    </div>
</body>
</html>
