<div class="mb-6">
    <a href="/admin/tenants" class="text-blue-600 hover:underline text-sm flex items-center mb-2">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Tenants
    </a>
    <h1 class="text-2xl font-bold text-slate-800">Edit Tenant: <?php echo $tenant['name']; ?></h1>
</div>

<div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
    <form action="/admin/tenants/edit/<?php echo $tenant['id']; ?>" method="POST" class="space-y-6">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Company Name</label>
                <input type="text" name="name" value="<?php echo $tenant['name']; ?>" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Admin Email</label>
                <input type="email" name="email" value="<?php echo $tenant['email']; ?>" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
            </div>

            <div class="space-y-4">
                <label class="block text-sm font-bold text-slate-700 mb-2">CUI / VAT Number</label>
                <div class="flex space-x-2">
                    <input type="text" name="cui" id="cui_input" value="<?php echo $tenant['cui']; ?>" required
                           class="flex-1 px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                    <button type="button" onclick="fetchAnafData()" id="anaf_btn"
                            class="px-4 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Preluare ANAF
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Reg. Com.</label>
                <input type="text" name="reg_com" id="reg_com" value="<?php echo $tenant['reg_com'] ?? ''; ?>"
                       class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all" placeholder="J40/1234/2020">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Adresă Sediu Social</label>
                <textarea name="address" id="address" rows="2"
                          class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all"><?php echo $tenant['address'] ?? ''; ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Județ</label>
                    <input type="text" name="county" id="county" value="<?php echo $tenant['county'] ?? ''; ?>"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Localitate</label>
                    <input type="text" name="city" id="city" value="<?php echo $tenant['city'] ?? ''; ?>"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Contact Phone</label>
                    <input type="text" name="contact_phone" value="<?php echo $tenant['contact_phone'] ?? ''; ?>"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all" placeholder="e.g. +40722123456">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Notification Phone (SMS)</label>
                    <input type="text" name="notification_phone" value="<?php echo $tenant['notification_phone'] ?? ''; ?>"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all" placeholder="e.g. +40722987654">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Account Status</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all cursor-pointer">
                    <option value="active" <?php echo $tenant['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="suspended" <?php echo $tenant['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                </select>
            </div>

            <div class="p-6 bg-amber-50 rounded-2xl border border-amber-200/50 space-y-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="block text-sm font-bold text-amber-900">Raport Detaliat Administrator</span>
                        <span class="block text-xs text-amber-700/70">Trimite zilnic și lunar detalii complete către dashboard-ul central.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="send_to_admin_enabled" value="1" <?php echo ($tenant['send_to_admin_enabled'] ?? 0) ? 'checked' : ''; ?> class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                    </label>
                </div>
                
                <div class="pt-4 border-t border-amber-200/30 flex justify-end">
                    <a href="/admin/tenants/test-report/<?php echo $tenant['id']; ?>" 
                       class="text-xs font-bold text-amber-700 hover:text-amber-900 flex items-center bg-white px-3 py-2 rounded-lg border border-amber-200/50 hover:bg-amber-100 transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Trimite Raport Test ACUM pe mail
                    </a>
                </div>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end space-x-4">
            <a href="/admin/tenants" class="px-6 py-3 text-slate-600 font-bold hover:text-slate-800 transition-colors">Cancel</a>
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md">
                Save Changes
            </button>
        </div>
    </form>
</div>

<script>
async function fetchAnafData() {
    const cuiInput = document.getElementById('cui_input');
    const btn = document.getElementById('anaf_btn');
    const cui = cuiInput.value.replace(/\D/g, '');

    if (!cui) {
        alert('Te rog introdu un CUI valid.');
        return;
    }

    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Preluare...';

    try {
        const response = await fetch(`/admin/tenants/anaf-fetch?cui=${cui}`);
        const data = await response.json();

        if (data.success) {
            document.querySelector('input[name="name"]').value = data.company_name;
            document.getElementById('reg_com').value = data.reg_com;
            document.getElementById('address').value = data.address;
            document.getElementById('county').value = data.county;
            document.getElementById('city').value = data.city;
            
            // Highlight success
            btn.classList.replace('bg-indigo-600', 'bg-green-600');
            setTimeout(() => btn.classList.replace('bg-green-600', 'bg-indigo-600'), 2000);
        } else {
            alert('Eroare ANAF: ' + (data.error || 'CUI negăsit.'));
        }
    } catch (e) {
        alert('Eroare conexiune: ' + e.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}
</script>
