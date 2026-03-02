<div class="mb-6">
    <a href="/admin/email-templates" class="text-blue-600 hover:underline text-sm flex items-center mb-2">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Templates
    </a>
    <h1 class="text-2xl font-bold text-slate-800">Edit Template: <?php echo $template['name']; ?></h1>
</div>

<div class="max-w-4xl grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <form action="/admin/email-templates/edit/<?php echo $template['id']; ?>" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Subject</label>
                    <input type="text" name="subject" value="<?php echo htmlspecialchars($template['subject']); ?>" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Body (Plain Text)</label>
                    <textarea name="body" rows="12" required
                              class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all font-mono text-sm"><?php echo htmlspecialchars($template['body']); ?></textarea>
                </div>

                <div class="pt-6 border-t flex items-center justify-end space-x-4">
                    <a href="/admin/email-templates" class="text-slate-600 font-bold">Cancel</a>
                    <button type="submit" class="px-10 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md">
                        Save Template
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
            <h3 class="font-bold text-indigo-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Available Placeholders
            </h3>
            <p class="text-sm text-indigo-700 mb-4">You can use the following variables in the subject or body. They will be replaced automatically:</p>
            <div class="space-y-2">
                <?php 
                    $placeholders = explode(',', $template['placeholders']);
                    foreach ($placeholders as $p): 
                ?>
                    <code class="block text-xs font-bold text-indigo-900 bg-white p-2 rounded border border-indigo-200"><?php echo trim($p); ?></code>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
            <h3 class="font-bold text-slate-800 mb-2">Tips</h3>
            <ul class="text-xs text-slate-600 space-y-2 list-disc pl-4">
                <li>Keep the body concise and clear.</li>
                <li>Ensure placeholders are typed exactly as shown.</li>
                <li>These emails are currently sent in plain text for maximum compatibility.</li>
            </ul>
        </div>
    </div>
</div>
