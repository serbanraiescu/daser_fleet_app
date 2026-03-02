<div class="max-w-md mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Driver Deck</h1>
        <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-semibold uppercase tracking-wide">
            <?php echo $hasOpenTrip ? 'In Trip' : 'Available'; ?>
        </span>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 gap-4">
        <?php if (!$hasOpenTrip): ?>
            <a href="/driver/start-trip" class="flex flex-col items-center justify-center p-8 bg-blue-600 text-white rounded-2xl shadow-lg hover:bg-blue-700 transition-all active:scale-95">
                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span class="text-lg font-bold">Start New Trip</span>
            </a>
            
            <a href="/driver/handover" class="flex flex-col items-center justify-center p-8 bg-slate-800 text-white rounded-2xl shadow-lg hover:bg-slate-900 transition-all active:scale-95">
                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                <span class="text-lg font-bold">Vehicle Handover</span>
            </a>
        <?php else: ?>
            <a href="/driver/end-trip" class="flex flex-col items-center justify-center p-8 bg-red-600 text-white rounded-2xl shadow-lg hover:bg-red-700 transition-all active:scale-95">
                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3 3L22 4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg>
                <span class="text-lg font-bold">End Current Trip</span>
            </a>
        <?php endif; ?>

        <a href="/driver/report-damage" class="flex flex-col items-center justify-center p-8 bg-amber-500 text-white rounded-2xl shadow-lg hover:bg-amber-600 transition-all active:scale-95">
            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span class="text-lg font-bold">Report Damage</span>
        </a>
    </div>

    <!-- Active Status Info -->
    <?php if ($hasOpenTrip): ?>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Active Trip Info</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-slate-600">Vehicle</span>
                    <span class="font-bold text-slate-900"><?php echo $activeTrip['license_plate'] ?? 'Unknown'; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Started At</span>
                    <span class="font-medium text-slate-900"><?php echo date('H:i', strtotime($activeTrip['start_time'])); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Start KM</span>
                    <span class="font-medium text-slate-900"><?php echo $activeTrip['start_km']; ?> km</span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
