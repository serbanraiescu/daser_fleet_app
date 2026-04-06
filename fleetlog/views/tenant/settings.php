<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Firm Settings</h1>
    <p class="text-slate-500">Configure your fleet management preferences.</p>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>
            <?php 
                if ($_GET['success'] === 'token_regenerated') echo "Link de înscriere generat/actualizat cu succes!";
                elseif ($_GET['success'] === 'test_sent') echo "Raportul de test a fost adăugat în coada de trimitere!";
                else echo "Settings updated successfully!";
            ?>
        </span>
    </div>
<?php endif; ?>

<!-- Tabs Navigation -->
<div class="mb-8 border-b border-slate-200">
    <nav class="flex space-x-8" id="settings-tabs">
        <button onclick="switchTab('general')" class="tab-btn border-b-2 border-indigo-500 py-4 px-1 text-sm font-black text-indigo-600 transition-all uppercase tracking-wider" id="tab-general">
            Configurare Generală
        </button>
        <button onclick="switchTab('notifications')" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:text-slate-700 hover:border-slate-300 transition-all uppercase tracking-wider" id="tab-notifications">
            Notificări & Contact
        </button>
        <button onclick="switchTab('inventory')" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:text-slate-700 hover:border-slate-300 transition-all uppercase tracking-wider" id="tab-inventory">
            Gestiune Inventar
        </button>
        <button onclick="switchTab('onboarding')" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:text-slate-700 hover:border-slate-300 transition-all uppercase tracking-wider" id="tab-onboarding">
            Self-Onboarding (BETA)
        </button>
    </nav>
</div>

<form action="/tenant/settings" method="POST" class="space-y-8">
    
    <!-- General Settings Section -->
    <div id="section-general" class="tab-content">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Firm Timezone</label>
                        <select name="timezone" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none font-medium">
                            <?php foreach ($timezones as $tz): ?>
                                <option value="<?php echo $tz; ?>" <?php echo $tz === ($tenant['timezone'] ?? 'Europe/Bucharest') ? 'selected' : ''; ?>>
                                    <?php echo $tz; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-2 text-xs text-slate-400 italic">All reports and logs will use this timezone.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Interface Language</label>
                        <select name="language" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none font-medium">
                            <option value="ro" <?php echo ($tenant['language'] ?? 'ro') === 'ro' ? 'selected' : ''; ?>>Română</option>
                            <option value="en" <?php echo ($tenant['language'] ?? 'ro') === 'en' ? 'selected' : ''; ?>>English</option>
                        </select>
                        <p class="mt-2 text-xs text-slate-400 italic">The administrative interface will use this language.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Custom Trip Types</label>
                    <textarea name="trip_types" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none font-medium" placeholder="LIVRARI, NAVETA, SERVICE, UZ PERSONAL"><?php echo \htmlspecialchars($tenant['trip_types'] ?? ''); ?></textarea>
                    <p class="mt-2 text-xs text-slate-400 italic">Separate types with commas. These appear in the travel logs (foi de parcurs).</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Section -->
    <div id="section-notifications" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Contact Phone</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </span>
                            <input type="text" name="contact_phone" value="<?php echo \htmlspecialchars($tenant['contact_phone'] ?? ''); ?>" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none font-medium">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Notification Phone (SMS Alerts)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            </span>
                            <input type="text" name="notification_phone" value="<?php echo \htmlspecialchars($tenant['notification_phone'] ?? ''); ?>" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none font-medium">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="space-y-1">
                            <span class="block text-sm font-bold text-slate-700">Raport Zilnic prin SMS (ora 21:00)</span>
                            <span class="block text-xs text-slate-400">Primești un rezumat automat cu curse, alimentări și prezența șoferilor.</span>
                        </div>
                        <div class="flex items-center space-x-6">
                            <?php if ($tenant['daily_report_enabled']): ?>
                                <button type="button" onclick="testReport()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-widest flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    Test Acum
                                </button>
                            <?php endif; ?>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="daily_report_enabled" value="1" <?php echo ($tenant['daily_report_enabled'] ?? 0) ? 'checked' : ''; ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Hidden form for testing the report -->
                <form id="form-test-report" action="/tenant/settings/test-report" method="POST" class="hidden"></form>
                
                <script>
                function testReport() {
                    if (confirm('Vrei să primești un raport de test acum pe numărul de notificări?')) {
                        document.getElementById('form-test-report').submit();
                    }
                }
                </script>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Additional Notification Emails</label>
                    <textarea name="notification_emails" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none font-medium" placeholder="manager@company.com, office@company.com"><?php echo \htmlspecialchars($tenant['notification_emails'] ?? ''); ?></textarea>
                    <p class="mt-2 text-xs text-slate-400 italic">Separate multiple emails with commas. These recipients will also receive expiry and damage alerts.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Section -->
    <div id="section-inventory" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-8">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Assignment Mapping</h3>
                        <p class="text-sm text-slate-500">Choose who is responsible for each mandatory item. Items assigned to <b>Driver</b> generate personal alerts.</p>
                    </div>
                </div>
                
                <?php 
                    $eqConfig = json_decode($tenant['equipment_config'] ?? '[]', true);
                    $items = [
                        'triangles' => 'Triunghiuri Reflectorizante',
                        'vest' => 'Vestă Reflectorizantă',
                        'jack' => 'Cric',
                        'medical_kit' => 'Trusă Medicală',
                        'tow_rope' => 'Șufă Tractare',
                        'jumper_cables' => 'Cabluri Curent',
                        'extinguisher' => 'Stingător',
                        'spare_wheel' => 'Roată Rezervă'
                    ];
                ?>
                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Item Name / Echipament</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-slate-400 uppercase tracking-widest">Gestiune Vehicul</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-slate-400 uppercase tracking-widest">Gestiune Șofer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($items as $key => $label): 
                                $val = $eqConfig[$key] ?? 'vehicle';
                            ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-700"><?php echo $label; ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <label class="inline-flex items-center cursor-pointer group">
                                            <input type="radio" name="equipment_config[<?php echo $key; ?>]" value="vehicle" <?php echo $val === 'vehicle' ? 'checked' : ''; ?> class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                            <span class="ml-2 text-xs font-bold text-slate-400 group-hover:text-indigo-600 transition-colors uppercase">Masină</span>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <label class="inline-flex items-center cursor-pointer group">
                                            <input type="radio" name="equipment_config[<?php echo $key; ?>]" value="driver" <?php echo $val === 'driver' ? 'checked' : ''; ?> class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                            <span class="ml-2 text-xs font-bold text-slate-400 group-hover:text-indigo-600 transition-colors uppercase">Șofer</span>
                                        </label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Onboarding Section -->
    <div id="section-onboarding" class="tab-content hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-8 space-y-8">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Driver Self-Onboarding</h3>
                        <p class="text-sm text-slate-500">Permite șoferilor să își creeze singuri contul folosind un link unic.</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="space-y-1">
                        <span class="block text-sm font-bold text-slate-700">Activează Înregistrarea</span>
                        <span class="block text-xs text-slate-400">Dacă e dezactivat, link-ul de mai jos nu va funcționa.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="signup_enabled" value="1" <?php echo ($tenant['signup_enabled'] ?? 0) ? 'checked' : ''; ?> class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <?php if (!empty($tenant['signup_token'])): ?>
                    <div class="space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Link de Înscriere</label>
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-grow flex items-center px-4 py-3 bg-white border border-slate-200 rounded-xl font-mono text-sm text-slate-600 overflow-x-auto">
                                <?php 
                                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                                    $host = $_SERVER['HTTP_HOST'];
                                    $joinLink = "$protocol://$host/join/" . $tenant['signup_token'];
                                    echo $joinLink;
                                ?>
                            </div>
                            <button type="button" onclick="copyToClipboard('<?php echo $joinLink; ?>')" class="px-6 py-3 bg-slate-800 text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-slate-700 transition-all flex-shrink-0">
                                Copiază Link
                            </button>
                        </div>
                        <p class="text-xs text-slate-400 italic">Trimite acest link șoferilor tăi. După înscriere, va trebui să îi aprobi manual din lista de șoferi.</p>
                    </div>
                <?php else: ?>
                    <div class="p-6 bg-amber-50 border border-amber-100 rounded-2xl text-amber-700 text-sm flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        E nevoie de un token pentru a genera link-ul. Apasă pe butonul de mai jos pentru a-l genera.
                    </div>
                <?php endif; ?>

                <div class="pt-6 border-t border-slate-100">
                    <button type="submit" form="form-regenerate" class="<?php echo !empty($tenant['signup_token']) ? 'text-red-600 text-xs' : 'bg-indigo-600 text-white px-8 py-3 rounded-xl text-sm'; ?> font-bold uppercase tracking-widest hover:opacity-80 transition-all flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <?php echo !empty($tenant['signup_token']) ? 'Regenerează Link (Resetare Securitate)' : 'Generează Link de Înscriere'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Footer with Save Button -->
    <div class="sticky bottom-0 bg-slate-50/80 backdrop-blur-md pt-6 pb-6 mt-8 border-t border-slate-200 flex justify-end z-10">
        <button type="submit" class="group relative inline-flex items-center justify-center px-10 py-4 font-black text-white bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition-all hover:-translate-y-0.5 active:scale-[0.98] uppercase tracking-widest text-sm">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            <?php echo __('save_settings') ?? 'Salvează Configurarea'; ?>
        </button>
    </div>
<!-- Main settings form ends here -->
</form>

<!-- Regeneration form moved outside main form to avoid nesting -->
<form id="form-regenerate" action="/tenant/settings/regenerate-token" method="POST" onsubmit="return confirm('Ești sigur că vrei să generezi un nou link? Cel vechi nu va mai funcționa!')" class="hidden"></form>

<script>
function switchTab(tabId) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    
    // Show active content
    const target = document.getElementById('section-' + tabId);
    if (target) target.classList.remove('hidden');
    
    // Update button styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-indigo-500', 'text-indigo-600', 'font-black');
        btn.classList.add('border-transparent', 'text-slate-500', 'font-medium');
    });
    
    // Set active button style
    const activeBtn = document.getElementById('tab-' + tabId);
    if (activeBtn) {
        activeBtn.classList.remove('border-transparent', 'text-slate-500', 'font-medium');
        activeBtn.classList.add('border-indigo-500', 'text-indigo-600', 'font-black');
    }
}

// Check for deep link or persistent tab
window.onload = function() {
    // Priority 1: Hash in URL (e.g. #onboarding)
    const hash = window.location.hash.replace('#', '');
    if (hash && document.getElementById('tab-' + hash)) {
        switchTab(hash);
        return;
    }

    // Priority 2: Persistent tab from localStorage
    const lastTab = localStorage.getItem('last_settings_tab');
    if (lastTab) switchTab(lastTab);
};

// Update localStorage on switch
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.id.replace('tab-', '');
        localStorage.setItem('last_settings_tab', id);
    });
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Link copiat în clipboard!');
    }).catch(err => {
        console.error('Eroare la copiere:', err);
    });
}
</script>
