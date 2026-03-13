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
            <div x-data="{ 
                scannerOpen: false, 
                html5QrCode: null,
                startScanner() {
                    this.scannerOpen = true;
                    this.$nextTick(() => {
                        this.html5QrCode = new Html5Qrcode('reader');
                        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                        this.html5QrCode.start({ facingMode: 'environment' }, config, (decodedText) => {
                            // URL format: https://.../driver/start-trip?qr=ABC12345
                            if (decodedText.includes('qr=')) {
                                window.location.href = decodedText;
                            } else {
                                // Fallback for raw codes
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
                <button @click="startScanner()" class="w-full flex flex-col items-center justify-center p-8 bg-blue-600 text-white rounded-2xl shadow-lg hover:bg-blue-700 transition-all active:scale-95 mb-4">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v-4m6 0h-2m-6 0H4m0 4v2m0 4v4m0-4h2m16-4v-2m0-4V4m0 4h-2M4 4h2"></path></svg>
                    <span class="text-lg font-bold">Scan QR to Start Trip</span>
                </button>

                <!-- Scanner Modal -->
                <div x-show="scannerOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-90 p-4">
                    <div class="w-full max-w-sm bg-white rounded-2xl overflow-hidden shadow-2xl">
                        <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800">Scan Vehicle QR</h3>
                            <button @click="stopScanner()" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div id="reader" class="w-full aspect-square bg-black"></div>
                        <div class="p-4 bg-slate-50 text-center text-sm text-slate-500">
                            Point camera at the vehicle QR code
                        </div>
                    </div>
                </div>

                <a href="/driver/start-trip" class="w-full flex items-center justify-center p-4 bg-white text-blue-600 border-2 border-blue-600 rounded-xl font-bold hover:bg-blue-50 transition-all active:scale-95">
                    Select Manual
                </a>
            </div>
            
            <a href="/driver/report-damage" class="flex flex-col items-center justify-center p-8 bg-amber-500 text-white rounded-2xl shadow-lg hover:bg-amber-600 transition-all active:scale-95">
                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="text-lg font-bold">Report Damage</span>
            </a>
        <?php else: ?>
            <a href="/driver/end-trip" class="flex flex-col items-center justify-center p-8 bg-red-600 text-white rounded-2xl shadow-lg hover:bg-red-700 transition-all active:scale-95">
                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3 3L22 4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg>
                <span class="text-lg font-bold">End Current Trip</span>
            </a>

            <div class="grid grid-cols-2 gap-4">
                <a href="/driver/fueling" class="flex flex-col items-center justify-center p-6 bg-indigo-600 text-white rounded-2xl shadow-lg hover:bg-indigo-700 transition-all active:scale-95">
                    <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="text-sm font-bold text-center">Log Fueling</span>
                </a>
                
                <a href="/driver/report-damage" class="flex flex-col items-center justify-center p-6 bg-amber-500 text-white rounded-2xl shadow-lg hover:bg-amber-600 transition-all active:scale-95">
                    <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="text-sm font-bold text-center">Report Damage</span>
                </a>
            </div>
        <?php endif; ?>
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

    <!-- Security Settings (PIN) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200" x-data="{ showPinForm: false }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Security Settings</h3>
            <button @click="showPinForm = !showPinForm" class="text-xs text-blue-600 font-bold uppercase">
                <span x-show="!showPinForm">Set/Change PIN</span>
                <span x-show="showPinForm">Cancel</span>
            </button>
        </div>

        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="mb-4 p-3 bg-green-50 text-green-700 text-xs rounded-lg border border-green-100">
                <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="mb-4 p-3 bg-red-50 text-red-700 text-xs rounded-lg border border-red-100">
                <?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            </div>
        <?php endif; ?>

        <div x-show="showPinForm" x-cloak>
            <form action="/driver/set-pin" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">New Login PIN (4-6 digits)</label>
                    <input type="text" name="pin" inputmode="numeric" pattern="[0-9]*" maxlength="6" required
                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-center text-2xl tracking-[1em] focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <button type="submit" class="w-full py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition-all">
                    Save Secure PIN
                </button>
                <p class="text-[10px] text-slate-400 text-center">Șoferii pot folosi acest PIN pentru o logare mai rapidă pe telefon.</p>
            </form>
        </div>

        <div x-show="!showPinForm" class="flex items-center text-slate-400 italic text-xs">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            <?php echo !empty(Auth::user()['pin']) ? 'PIN is set and active.' : 'No PIN set yet.'; ?>
        </div>
    </div>
</div>
