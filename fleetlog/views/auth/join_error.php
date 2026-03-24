<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Eroare Înregistrare'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-2xl border border-slate-100 text-center">
        <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-8">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tighter mb-4"><?php echo $title; ?></h1>
        <p class="text-slate-600 mb-10 leading-relaxed"><?php echo $message; ?></p>
        <a href="/login" class="inline-flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-8 rounded-2xl transition-all hover:scale-[1.02] active:scale-[0.98] uppercase tracking-widest text-xs shadow-xl shadow-blue-100">
            Înapoi la Logare
        </a>
    </div>
</body>
</html>
