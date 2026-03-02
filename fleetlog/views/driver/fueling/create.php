<div class="max-w-md mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Log Fueling</h1>
        <a href="/driver/dashboard" class="text-slate-600 hover:text-slate-900 flex items-center text-sm font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back
        </a>
    </div>

    <form action="/driver/fueling" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ 
        selectedVehicleId: '<?php echo htmlspecialchars($selectedVehicleId ?? '', ENT_QUOTES, 'UTF-8'); ?>'
    }">
        <?php $isLocked = $isLocked ?? false; ?>
        <?php if ($isLocked): ?>
            <input type="hidden" name="vehicle_id" value="<?php echo htmlspecialchars($selectedVehicleId ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <?php endif; ?>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
            <div x-data="{ 
                scannerOpen: false, 
                html5QrCode: null,
                startScanner() {
                    this.scannerOpen = true;
                    this.$nextTick(() => {
                        this.html5QrCode = new Html5Qrcode('reader-fueling');
                        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                        this.html5QrCode.start({ facingMode: 'environment' }, config, (decodedText) => {
                            let qrCode = decodedText;
                            if (decodedText.includes('qr=')) {
                                qrCode = new URL(decodedText).searchParams.get('qr') || decodedText;
                            }
                            window.location.href = '/driver/fueling?qr=' + qrCode;
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
                    <?php if (!$isLocked): ?>
                    <button type="button" @click="startScanner()" class="inline-flex items-center text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v-4m6 0h-2m-6 0H4m0 4v2m0 4v4m0-4h2m16-4v-2m0-4V4m0 4h-2M4 4h2"></path></svg>
                        SCAN QR
                    </button>
                    <?php else: ?>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded">Locked to Active Trip</span>
                    <?php endif; ?>
                </div>
                <select <?php echo $isLocked ? 'disabled' : 'name="vehicle_id"'; ?> x-model="selectedVehicleId" required class="w-full p-4 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all appearance-none <?php echo $isLocked ? 'opacity-75 cursor-not-allowed' : ''; ?>">
                    <option value="">-- Choose vehicle --</option>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <option value="<?php echo $vehicle['id']; ?>">
                            <?php echo htmlspecialchars($vehicle['license_plate'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($vehicle['make'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
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
                        <div id="reader-fueling" class="w-full aspect-square bg-black"></div>
                        <div class="p-4 bg-slate-50 text-center text-sm text-slate-500">
                            Point camera at the vehicle QR code
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Current Odometer (KM)</label>
                <input type="number" name="odometer" required placeholder="e.g. 285600" class="w-full p-4 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Liters</label>
                    <input type="number" step="0.01" name="liters" required placeholder="0.00" class="w-full p-4 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Total Price</label>
                    <input type="number" step="0.01" name="total_price" required placeholder="0.00" class="w-full p-4 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="flex items-center space-x-3 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <input type="checkbox" name="is_full" id="is_full" value="1" class="w-6 h-6 text-blue-600 border-slate-300 rounded focus:ring-blue-500 transition-all">
                <label for="is_full" class="text-sm font-bold text-blue-800">S-a făcut plinul? (Full Tank)</label>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Receipt Photo (Optional)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-blue-400 transition-colors cursor-pointer relative">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <div class="flex text-sm text-slate-600">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                <span>Upload a file</span>
                                <input name="receipt_photo" type="file" class="sr-only" accept="image/*">
                            </label>
                        </div>
                        <p class="text-xs text-slate-500">PNG, JPG, GIF up to 10MB</p>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full p-4 bg-blue-600 text-white rounded-2xl font-bold shadow-lg hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center space-x-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Save Fuel Log</span>
        </button>
    </form>
</div>
