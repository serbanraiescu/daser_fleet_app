<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare Reușită</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-2xl border border-slate-100 text-center">
        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-8">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight mb-4 tracking-tighter">Înregistrare Reușită!</h1>
        <p class="text-slate-600 mb-8 leading-relaxed">Datele tale au fost trimise către <strong><?php echo htmlspecialchars($tenant_name); ?></strong>.</p>
        <div class="bg-blue-50 p-6 rounded-2xl mb-10 text-sm text-blue-700 font-medium leading-relaxed">
            Contul tău este acum <strong>în curs de aprobare</strong>. Vei putea de loga imediat ce administratorul îți activează profilul.
        </div>
        <a href="/login" class="inline-flex items-center justify-center w-full bg-slate-800 hover:bg-slate-900 text-white font-black py-4 px-8 rounded-2xl transition-all hover:scale-[1.02] active:scale-[0.98] uppercase tracking-widest text-xs">
            Mergi la Pagina de Login
        </a>
    </div>
</body>
</html>
