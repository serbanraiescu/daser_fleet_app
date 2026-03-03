<div class="mb-6">
    <a href="/admin/tenants" class="text-blue-600 hover:underline text-sm flex items-center mb-2">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Tenants
    </a>
    <h1 class="text-2xl font-bold text-slate-800">Add New Tenant</h1>
    <p class="text-slate-500">Create a new company account and its primary administrator.</p>
</div>

<div class="max-w-4xl grid grid-cols-1 md:grid-cols-2 gap-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <form action="/admin/tenants/add" method="POST" class="space-y-6">
            <h2 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Company Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Company Name</label>
                    <input type="text" name="name" placeholder="e.g. Daser Logistics" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">CUI / VAT Number</label>
                    <input type="text" name="cui" placeholder="RO12345678" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Contact Phone</label>
                        <input type="text" name="contact_phone" placeholder="+40..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Notification Phone</label>
                        <input type="text" name="notification_phone" placeholder="+40..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                    </div>
                </div>
            </div>

            <h2 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 mt-8">Admin Account</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Admin Full Name</label>
                    <input type="text" name="admin_name" placeholder="Contact Person Name" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Login Email</label>
                    <input type="email" name="email" placeholder="admin@company.com" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Initial Password</label>
                    <input type="password" name="password" placeholder="••••••••" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>
            </div>

            <div class="pt-6 flex items-center justify-end space-x-4 border-t mt-6">
                <a href="/admin/tenants" class="px-6 py-3 text-slate-600 font-bold hover:text-slate-800 transition-colors">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md">
                    Create Tenant
                </button>
            </div>
        </form>
    </div>

    <div class="space-y-6">
        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
            <h3 class="font-bold text-blue-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                What happens next?
            </h3>
            <ul class="text-sm text-blue-700 space-y-3">
                <li class="flex items-start">
                    <span class="mr-2 font-bold">•</span>
                    <span>The system will create a new tenant database entry.</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 font-bold">•</span>
                    <span>A <strong>Tenant Admin</strong> user will be created with the provided credentials.</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2 font-bold">•</span>
                    <span>The new admin will be able to log in and start adding vehicles and drivers.</span>
                </li>
            </ul>
        </div>
        
        <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100">
            <h3 class="font-bold text-amber-800 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Important Security Note
            </h3>
            <p class="text-sm text-amber-700">
                Please ensure you provide a strong initial password. The tenant admin can change it later from their settings.
            </p>
        </div>
    </div>
</div>
