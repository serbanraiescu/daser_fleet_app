<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Edit Driver</h1>
    <a href="/tenant/drivers" class="text-slate-600 hover:text-slate-900 flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Drivers
    </a>
</div>

<?php if (isset($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-2xl">
    <form action="/tenant/drivers/edit/<?php echo $driver['id']; ?>" method="POST" class="p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" required value="<?php echo $driver['name']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" required value="<?php echo $driver['email']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Personal Data & Documentation</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">CNP</label>
                    <input type="text" name="cnp" maxlength="13" value="<?php echo $driver['cnp']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">ID Expiry Date</label>
                    <input type="date" name="id_expiry" value="<?php echo $driver['id_expiry']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Driver License Series</label>
                    <input type="text" name="license_series" value="<?php echo $driver['license_series']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">License Expiry Date</label>
                    <input type="date" name="license_expiry" value="<?php echo $driver['license_expiry']; ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <label class="flex items-center space-x-3">
                <input type="checkbox" name="active" value="1" <?php echo $driver['active'] ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded">
                <span class="text-sm font-medium text-slate-700">Driver Account is Active</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                Update Driver
            </button>
        </div>
    </form>
</div>
