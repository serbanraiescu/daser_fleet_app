<div class="mb-6">
    <a href="/tenant/documents" class="text-blue-600 hover:text-blue-800 flex items-center mb-2">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Documents
    </a>
    <h1 class="text-2xl font-bold text-slate-800">New Inventory Custody Protocol</h1>
    <p class="text-slate-500 text-sm">Generate an official document for equipment assigned directly to a driver.</p>
</div>

<?php if (isset($_GET['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        Failed to save protocol. Please try again.
    </div>
<?php endif; ?>

<div class="max-w-2xl">
    <form action="/tenant/documents/inventory/add" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-tight">Select Driver</label>
                <select name="driver_id" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50">
                    <option value="">-- Choose a driver --</option>
                    <?php 
                    $selectedDriver = isset($_GET['driver_id']) ? (int)$_GET['driver_id'] : 0;
                    foreach ($drivers as $driver): 
                    ?>
                        <option value="<?php echo $driver['id']; ?>" <?php echo $selectedDriver === (int)$driver['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($driver['name']); ?> (<?php echo htmlspecialchars($driver['email']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="mt-2 text-xs text-slate-500 italic">The protocol will automatically include the equipment currently assigned to this driver's profile.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-tight">Additional Notes / Observations</label>
                <textarea name="notes" rows="4" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Items are new, special instructions for maintenance, etc."></textarea>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Protocol Generation</h3>
                        <div class="mt-1 text-sm text-blue-700">
                            <p>By clicking "Generate Protocol", a unique document number will be assigned. You will be redirected to the print-ready version where you can download or print the PDF for signing.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 px-6 py-4 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition-all shadow-md active:scale-95">
                Generate Inventory Protocol
            </button>
        </div>
    </form>
</div>
