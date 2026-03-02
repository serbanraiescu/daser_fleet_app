<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Start New Trip</h1>

    <?php if (isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            <?php 
                if ($_GET['error'] === 'invalid_km') {
                    echo "<strong>Eroare KM:</strong> Valoarea introdusă nu poate fi mai mică decât ultima înregistrare (" . ($_GET['min'] ?? '0') . " KM).";
                } else {
                    echo "A apărut o eroare la pornirea cursei. Vă rugăm încercați din nou.";
                }
            ?>
        </div>
    <?php endif; ?>

    <form action="/driver/start-trip" method="POST" class="space-y-6" x-data="{ 
        selectedVehicleId: '<?php echo htmlspecialchars($selectedVehicleId ?? '', ENT_QUOTES, 'UTF-8'); ?>',
        vehicles: <?php echo htmlspecialchars(json_encode($vehicles), ENT_QUOTES, 'UTF-8'); ?>,
        startKm: '',
        get currentVehicle() {
            return this.vehicles.find(v => v.id == this.selectedVehicleId);
        },
        get isKmInvalid() {
            if (!this.currentVehicle || !this.startKm) return false;
            return parseInt(this.startKm) < parseInt(this.currentVehicle.current_odometer);
        }
    }">
        <div x-data="{ 
            scannerOpen: false, 
            html5QrCode: null,
            startScanner() {
                this.scannerOpen = true;
                this.$nextTick(() => {
                    this.html5QrCode = new Html5Qrcode('reader-start');
                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                    this.html5QrCode.start({ facingMode: 'environment' }, config, (decodedText) => {
                        if (decodedText.includes('qr=')) {
                            window.location.href = decodedText;
                        } else {
                            window.location.href = '/driver/start-trip?qr=' + decodedText;
                        }
                        this.stopScanner();
                    });
                });
            },
            stopScanner() {
                if (this.html5QrCode) {
                    this.html5QrCode.stop().then(() => {
                        this.html5QrCode.clear();
                        this.scannerOpen = false;
                    }).catch(err => {
                        this.scannerOpen = false;
                    });
                } else {
                    this.scannerOpen = false;
                }
            }
        }">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-semibold text-slate-700">Select Vehicle</label>
                <button type="button" @click="startScanner()" class="inline-flex items-center text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v-4m6 0h-2m-6 0H4m0 4v2m0 4v4m0-4h2m16-4v-2m0-4V4m0 4h-2M4 4h2"></path></svg>
                    SCAN QR
                </button>
            </div>
            <select name="vehicle_id" x-model="selectedVehicleId" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <option value="">-- Choose vehicle --</option>
                <?php foreach ($vehicles as $vehicle): ?>
                    <?php if ($vehicle['status'] === 'active'): ?>
                        <option value="<?php echo $vehicle['id']; ?>">
                            <?php echo $vehicle['license_plate']; ?> - <?php echo $vehicle['make']; ?> <?php echo $vehicle['model']; ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>

            <!-- Scanner Modal -->
            <div x-show="scannerOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-90 p-4">
                <div class="w-full max-w-sm bg-white rounded-2xl overflow-hidden shadow-2xl">
                    <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800">Scan Vehicle QR</h3>
                        <button type="button" @click="stopScanner()" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div id="reader-start" class="w-full aspect-square bg-black"></div>
                    <div class="p-4 bg-slate-50 text-center text-sm text-slate-500">
                        Point camera at the vehicle QR code
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-semibold text-slate-700">Start KM (Current Odometer)</label>
                <template x-if="currentVehicle">
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">
                        Last DB: <span x-text="numberFormat(currentVehicle.current_odometer)"></span> KM
                    </span>
                </template>
            </div>
            <input type="number" name="start_km" x-model="startKm" required 
                   :class="isKmInvalid ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-slate-300 focus:ring-blue-500 focus:border-blue-500'"
                   class="w-full p-4 bg-white border rounded-xl shadow-sm outline-none transition-all" 
                   placeholder="e.g. 125430">
            <template x-if="isKmInvalid">
                <p class="mt-2 text-xs font-bold text-red-600">⚠️ KM nu pot fi mai puțini decât ultima înregistrare!</p>
            </template>
        </div>

        <script>
            function numberFormat(number) {
                return new Intl.NumberFormat('ro-RO').format(number);
            }
        </script>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Trip Type</label>
            <select name="type" class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <?php foreach ($tripTypes as $type): ?>
                    <option value="<?php echo \htmlspecialchars($type); ?>"><?php echo \htmlspecialchars($type); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Notes (Optional)</label>
            <textarea name="notes" rows="3" class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Any specific details..."></textarea>
        </div>

        <button type="submit" class="w-full p-4 bg-blue-600 text-white rounded-xl shadow-lg font-bold text-lg hover:bg-blue-700 transition-all active:scale-95">
            Launch Trip
        </button>
        
        <a href="/driver/dashboard" class="block text-center text-slate-500 font-medium py-2">Cancel</a>
    </form>
</div>
