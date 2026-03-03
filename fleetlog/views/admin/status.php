<div class="space-y-6">
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">System Status</h1>
            <p class="text-slate-500 mt-1">Detailed diagnostic information for platform core.</p>
        </div>
        <button id="runDiagnosticBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-blue-200 transition-all transform active:scale-95 flex items-center space-x-2">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            <span>Run Diagnostic Test</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- DB Card -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 status-card" id="card-database">
            <div class="flex justify-between items-start mb-6">
                <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 icon-box transition-all">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                </div>
                <span class="status-indicator px-3 py-1 bg-slate-100 text-slate-500 rounded-full font-bold text-xs">PENDING</span>
            </div>
            <h3 class="text-xl font-black text-slate-800">Database Connection</h3>
            <p class="text-slate-500 mt-2">MariaDB / MySQL engine connection and query performance.</p>
        </div>

        <!-- Mailer Card -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 status-card" id="card-mailer">
            <div class="flex justify-between items-start mb-6">
                <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 icon-box transition-all">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <span class="status-indicator px-3 py-1 bg-slate-100 text-slate-500 rounded-full font-bold text-xs">PENDING</span>
            </div>
            <h3 class="text-xl font-black text-slate-800">Mail Distribution</h3>
            <p class="text-slate-500 mt-2">SMTP configuration and relay service availability.</p>
        </div>

        <!-- Storage Card -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 status-card" id="card-storage">
            <div class="flex justify-between items-start mb-6">
                <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 icon-box transition-all">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                </div>
                <span class="status-indicator px-3 py-1 bg-slate-100 text-slate-500 rounded-full font-bold text-xs">PENDING</span>
            </div>
            <h3 class="text-xl font-black text-slate-800">File Storage</h3>
            <p class="text-slate-500 mt-2">Disk write permissions for uploads and temp directories.</p>
        </div>

        <!-- Cron Card -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 status-card" id="card-cron">
            <div class="flex justify-between items-start mb-6">
                <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 icon-box transition-all">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="status-indicator px-3 py-1 bg-slate-100 text-slate-500 rounded-full font-bold text-xs">PENDING</span>
            </div>
            <h3 class="text-xl font-black text-slate-800">Background Workers</h3>
            <p class="text-slate-500 mt-2">Cron job execution status and queue processing health.</p>
        </div>
    </div>
</div>

<script>
document.getElementById('runDiagnosticBtn').addEventListener('click', async function() {
    const btn = this;
    const initialText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3 inline-block" viewBox="0 0 24 24">...</svg> Running...';

    // Reset UI
    document.querySelectorAll('.status-card').forEach(card => {
        card.querySelector('.status-indicator').className = 'status-indicator px-3 py-1 bg-slate-100 text-slate-500 rounded-full font-bold text-xs';
        card.querySelector('.status-indicator').innerText = 'TESTING...';
        card.querySelector('.icon-box').className = 'w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 icon-box transition-all';
    });

    try {
        const response = await fetch('/admin/run-self-test', { method: 'POST' });
        const data = await response.json();
        
        if (data.success) {
            Object.entries(data.checks).forEach(([key, ok]) => {
                const card = document.getElementById('card-' + key);
                if (!card) return;

                const indicator = card.querySelector('.status-indicator');
                const iconBox = card.querySelector('.icon-box');

                if (ok) {
                    indicator.innerText = 'HEALTHY';
                    indicator.className = 'status-indicator px-3 py-1 bg-green-100 text-green-700 rounded-full font-bold text-xs';
                    iconBox.className = 'w-14 h-14 bg-green-500 text-white rounded-2xl flex items-center justify-center icon-box transition-all shadow-lg shadow-green-200';
                } else {
                    indicator.innerText = 'CRITICAL ERROR';
                    indicator.className = 'status-indicator px-3 py-1 bg-red-100 text-red-700 rounded-full font-bold text-xs';
                    iconBox.className = 'w-14 h-14 bg-red-500 text-white rounded-2xl flex items-center justify-center icon-box transition-all shadow-lg shadow-red-200';
                }
            });
        }
    } catch (e) {
        alert('Diagnostic failed. Check connection.');
    }

    btn.disabled = false;
    btn.innerHTML = initialText;
});
</script>
