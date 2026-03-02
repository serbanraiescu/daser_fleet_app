<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Email Templates</h1>
    <p class="text-slate-500">Customize the messages sent for alerts and notifications.</p>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        Template updated successfully!
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($templates as $t): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col hover:border-blue-300 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?php echo $t['slug']; ?></span>
            </div>
            
            <h3 class="font-bold text-slate-800 text-lg mb-2"><?php echo $t['name']; ?></h3>
            <p class="text-sm text-slate-500 mb-6 flex-1">
                Subject: <span class="text-slate-700 italic">"<?php echo $t['subject']; ?>"</span>
            </p>
            
            <div class="pt-4 border-t flex justify-end">
                <a href="/admin/email-templates/edit/<?php echo $t['id']; ?>" class="text-blue-600 font-bold hover:text-blue-900 flex items-center">
                    Edit Template
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
