<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Report Damage/Incident</h1>

    <form action="/driver/report-damage" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ 
        selectedVehicleId: '<?php echo htmlspecialchars($selectedVehicleId ?? '', ENT_QUOTES, 'UTF-8'); ?>'
    }">
        <?php $isLocked = $isLocked ?? false; ?>
        <?php if ($isLocked): ?>
            <input type="hidden" name="vehicle_id" value="<?php echo htmlspecialchars($selectedVehicleId ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <?php endif; ?>

        <div x-data="{ 
            scannerOpen: false, 
            html5QrCode: null,
            startScanner() {
                this.scannerOpen = true;
                this.$nextTick(() => {
                    this.html5QrCode = new Html5Qrcode('reader-damage');
                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                    this.html5QrCode.start({ facingMode: 'environment' }, config, (decodedText) => {
                        let qrCode = decodedText;
                        if (decodedText.includes('qr=')) {
                            qrCode = new URL(decodedText).searchParams.get('qr') || decodedText;
                        }
                        window.location.href = '/driver/report-damage?qr=' + qrCode;
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
                <label class="block text-sm font-semibold text-slate-700">Vehicle</label>
                <?php if (!$isLocked): ?>
                <button type="button" @click="startScanner()" class="inline-flex items-center text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v-4m6 0h-2m-6 0H4m0 4v2m0 4v4m0-4h2m16-4v-2m0-4V4m0 4h-2M4 4h2"></path></svg>
                    SCAN QR
                </button>
                <?php else: ?>
                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded">Locked to Active Trip</span>
                <?php endif; ?>
            </div>
            <select <?php echo $isLocked ? 'disabled' : 'name="vehicle_id"'; ?> x-model="selectedVehicleId" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all <?php echo $isLocked ? 'bg-slate-100 opacity-75' : ''; ?>">
                <option value="">-- Select Vehicle --</option>
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?php echo $vehicle['id']; ?>">
                        <?php echo htmlspecialchars($vehicle['license_plate'], ENT_QUOTES, 'UTF-8'); ?>
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
                    <div id="reader-damage" class="w-full aspect-square bg-black"></div>
                    <div class="p-4 bg-slate-50 text-center text-sm text-slate-500">
                        Point camera at the vehicle QR code
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Category</label>
                <select name="category" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    <option value="zgarietura">Zgârietură</option>
                    <option value="lovitura">Lovitură</option>
                    <option value="parbriz">Parbriz</option>
                    <option value="roata">Roată</option>
                    <option value="mecanic">Mecanic</option>
                    <option value="altul">Altul</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Severity</label>
                <select name="severity" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    <option value="low">Low</option>
                    <option value="med">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
            <textarea name="description" rows="3" required class="w-full p-4 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Describe what happened..."></textarea>
        </div>

        <div class="p-4 bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Photos (Max 6, Max 2MB each)</label>
            <input type="file" name="photos[]" multiple accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <button type="submit" class="w-full p-4 bg-amber-500 text-white rounded-xl shadow-lg font-bold text-lg hover:bg-amber-600 transition-all active:scale-95 text-center">
            Submit Report
        </button>
        
        <a href="/driver/dashboard" class="block text-center text-slate-500 font-medium py-2">Cancel</a>
    </form>
</div>
