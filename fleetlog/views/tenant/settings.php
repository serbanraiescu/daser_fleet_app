<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Firm Settings</h1>
    <p class="text-slate-500">Configure your fleet management preferences.</p>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        Settings updated successfully!
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-lg">
    <form action="/tenant/settings" method="POST" class="p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Firm Timezone</label>
            <select name="timezone" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <?php foreach ($timezones as $tz): ?>
                    <option value="<?php echo $tz; ?>" <?php echo $tz === ($tenant['timezone'] ?? 'Europe/Bucharest') ? 'selected' : ''; ?>>
                        <?php echo $tz; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="mt-1 text-xs text-slate-500 italic">All reports and logs will use this timezone.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Interface Language</label>
            <select name="language" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="ro" <?php echo ($tenant['language'] ?? 'ro') === 'ro' ? 'selected' : ''; ?>>Română</option>
                <option value="en" <?php echo ($tenant['language'] ?? 'ro') === 'en' ? 'selected' : ''; ?>>English</option>
            </select>
            <p class="mt-1 text-xs text-slate-500 italic">The administrative interface will use this language.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Custom Trip Types</label>
            <textarea name="trip_types" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="CURSE, NAVETA, SERVICE, ALTE"><?php echo \htmlspecialchars($tenant['trip_types'] ?? ''); ?></textarea>
            <p class="mt-1 text-xs text-slate-500 italic">Separate types with commas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Contact Phone</label>
                <input type="text" name="contact_phone" value="<?php echo \htmlspecialchars($tenant['contact_phone'] ?? ''); ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Notification Phone</label>
                <input type="text" name="notification_phone" value="<?php echo \htmlspecialchars($tenant['notification_phone'] ?? ''); ?>" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Additional Notification Emails</label>
            <textarea name="notification_emails" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="manager@company.com, office@company.com"><?php echo \htmlspecialchars($tenant['notification_emails'] ?? ''); ?></textarea>
            <p class="mt-1 text-xs text-slate-500 italic">Separate multiple emails with commas. These recipients will also receive expiry alerts.</p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                Save Settings
            </button>
        </div>
    </form>
</div>
