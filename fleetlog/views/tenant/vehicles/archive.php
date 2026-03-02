<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="/tenant/vehicles" class="text-blue-600 hover:underline text-sm flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Vehicles
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Archive Vehicle: <?php echo $vehicle['license_plate']; ?></h1>
    </div>
</div>

<div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start">
        <svg class="w-6 h-6 text-red-600 mr-3 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <div>
            <h3 class="text-red-800 font-bold mb-1">Warning: Destructive Action</h3>
            <p class="text-red-700 text-sm">
                Archiving a vehicle will remove it from all driver-facing lists (Start Trip, Damage, Fueling). 
                The vehicle's historical data (past trips, damages, refueling) will be preserved in reports.
                This action is typically used for Total Loss (Dauna Totala), Selling, or Deregistration.
            </p>
        </div>
    </div>

    <form action="/tenant/vehicles/archive/<?php echo $vehicle['id']; ?>" method="POST" class="space-y-6">
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Vehicle Details</label>
            <div class="p-4 bg-slate-50 rounded-xl text-sm text-slate-600">
                <span class="font-bold text-slate-800"><?php echo $vehicle['make'] . ' ' . $vehicle['model']; ?></span>
                <span class="mx-2">•</span>
                <span class="font-mono bg-white px-2 py-1 border rounded"><?php echo $vehicle['license_plate']; ?></span>
                <span class="mx-2">•</span>
                Current Odometer: <?php echo number_format($vehicle['current_odometer']); ?> KM
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Reason for Archiving <span class="text-red-500">*</span></label>
            <p class="text-xs text-slate-500 mb-2">Please explain why this vehicle is being removed from the fleet (e.g., "Dauna totala", "Radiere", "Vândută").</p>
            <textarea name="archive_notes" rows="4" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all placeholder-slate-400" placeholder="Enter explanation here..."></textarea>
        </div>

        <div class="flex items-center space-x-4 pt-4 border-t border-slate-100">
            <button type="submit" class="px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all shadow-md focus:ring-4 focus:ring-red-200">
                Confirm & Archive Vehicle
            </button>
            <a href="/tenant/vehicles" class="px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
