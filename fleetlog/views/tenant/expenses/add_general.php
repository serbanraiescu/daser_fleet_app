<?php require dirname(__DIR__) . '/partials/header.php'; ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="/tenant/expenses" class="text-blue-600 hover:underline text-sm flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Expenses
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Add Vehicle Expense</h1>
    </div>
</div>

<div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
    <form action="/tenant/expenses/add" method="POST" class="space-y-6">
        
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Select Vehicle <span class="text-red-500">*</span></label>
            <select name="vehicle_id" id="vehicle_id" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all appearance-none cursor-pointer">
                <option value="" disabled selected>Select a vehicle...</option>
                <?php foreach ($vehicles as $vec): ?>
                    <option value="<?php echo $vec['id']; ?>" data-odometer="<?php echo $vec['current_odometer']; ?>">
                        <?php echo htmlspecialchars($vec['license_plate'] . ' - ' . $vec['make'] . ' ' . $vec['model']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Expense Category</label>
                <select name="expense_type" id="expense_type" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all appearance-none cursor-pointer">
                    <option value="maintenance">🛠️ Maintenance & Service</option>
                    <option value="insurance">🛡️ Insurance (RCA, CASCO)</option>
                    <option value="tax">🧾 Tax (ITP, Rovinieta, Tolls)</option>
                    <option value="consumable">🛢️ Consumables (Oil, Liquids, AdBlue)</option>
                    <option value="other">📦 Other</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Description / Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Schimb Ulei, RCA Generali, Filtre" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all placeholder-slate-400">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Cost (RON) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="cost" required placeholder="0.00" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all font-mono">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Date <span class="text-red-500">*</span></label>
                <input type="date" name="expense_date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-md font-bold text-slate-800 mb-4">Odometer & Reminders</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Current Odometer (KM)</label>
                    <input type="number" id="odometer_at_expense" name="odometer_at_expense" value="" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all font-mono text-slate-500" readonly title="Updated automatically depending on vehicle">
                    <p class="text-xs text-slate-500 mt-1">Recorded at the time of this expense.</p>
                </div>
                
                <div id="service_reminder_container" class="bg-blue-50 p-4 rounded-xl border border-blue-100 hidden">
                    <label class="block text-sm font-bold text-blue-800 mb-2">Next Service Due At (KM)</label>
                    <input type="number" name="next_service_km" id="next_service_km" placeholder="Optional" class="w-full px-4 py-3 rounded-xl border border-blue-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-300 outline-none transition-all font-mono">
                    <p class="text-xs text-blue-600 mt-2">Optional. Set a reminder for the next oil change or revision. You will be alerted when the vehicle nears this kilometer limit.</p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Additional Notes / Findings</label>
            <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all placeholder-slate-400" placeholder="Mechanic observations, warranty details, etc..."></textarea>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-100">
            <a href="/tenant/expenses" class="px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md focus:ring-4 focus:ring-blue-200">
                Save Expense Record
            </button>
        </div>
    </form>
</div>

<script>
    const typeSelect = document.getElementById('expense_type');
    const reminderContainer = document.getElementById('service_reminder_container');
    const nextServiceInput = document.getElementById('next_service_km');
    
    // Auto-fill odometer when a vehicle is selected
    const vehicleSelect = document.getElementById('vehicle_id');
    const odometerInput = document.getElementById('odometer_at_expense');

    vehicleSelect.addEventListener('change', function() {
        if(this.selectedIndex > 0) {
            const od = this.options[this.selectedIndex].getAttribute('data-odometer');
            odometerInput.value = od;
        }
    });

    function toggleReminder() {
        if (typeSelect.value === 'maintenance' || typeSelect.value === 'consumable') {
            reminderContainer.classList.remove('hidden');
            reminderContainer.classList.add('block');
        } else {
            reminderContainer.classList.add('hidden');
            reminderContainer.classList.remove('block');
            nextServiceInput.value = ''; // clear it so we don't accidentally save it
        }
    }

    typeSelect.addEventListener('change', toggleReminder);
    
    // Initial check
    toggleReminder();
</script>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
