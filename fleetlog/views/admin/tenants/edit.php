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

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">CUI / VAT Number</label>
                <input type="text" name="cui" value="<?php echo $tenant['cui']; ?>" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Account Status</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all cursor-pointer">
                    <option value="active" <?php echo $tenant['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="suspended" <?php echo $tenant['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                </select>
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
