<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Înregistrare Șofer'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-2xl w-full bg-white p-8 md:p-12 rounded-3xl shadow-2xl border border-slate-100 my-8">
        <div class="flex flex-col items-center mb-10 text-center">
            <h1 class="text-4xl font-black text-slate-800 tracking-tighter uppercase mb-2">DASER <span class="text-blue-600">FLEET</span></h1>
            <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-xs font-black uppercase tracking-widest mb-6">
                Înregistrare Șofer Nou
            </div>
            <h2 class="text-xl font-bold text-slate-700">Compania: <?php echo htmlspecialchars($tenant['name']); ?></h2>
            <p class="text-slate-500 mt-2 text-sm italic">Completează formularul de mai jos pentru a-ți crea contul de șofer.</p>
        </div>
        
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-2xl mb-8 flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium text-sm"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></span>
            </div>
        <?php endif; ?>

        <form action="/join/<?php echo $token; ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informații Personale -->
                <div class="md:col-span-2">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                        <span class="w-8 h-[1px] bg-slate-200 mr-3"></span>
                        Date Personale
                        <span class="flex-grow h-[1px] bg-slate-200 ml-3"></span>
                    </h3>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2" for="name">Nume Complet (Nume și Prenume)</label>
                    <input class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-700" 
                           id="name" name="name" type="text" placeholder="ex: Ion Popescu" required>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2" for="phone">Număr Telefon</label>
                    <input class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-700" 
                           id="phone" name="phone" type="tel" placeholder="07xx xxx xxx">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2" for="cnp">CNP</label>
                    <input class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-700" 
                           id="cnp" name="cnp" type="text" maxlength="13" placeholder="1xxxxxxxxxxxx">
                </div>

                <!-- Documente Identitate -->
                <div class="md:col-span-2">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mt-4 mb-4 flex items-center">
                        <span class="w-8 h-[1px] bg-slate-200 mr-3"></span>
                        Documente și Expirări
                        <span class="flex-grow h-[1px] bg-slate-200 ml-3"></span>
                    </h3>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2" for="id_expiry">Expirare Buletin</label>
                    <input class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-700" 
                           id="id_expiry" name="id_expiry" type="date">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2" for="license_series">Serie Permis Conducere</label>
                    <input class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-700" 
                           id="license_series" name="license_series" type="text" placeholder="B00xxxxxx">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2" for="license_expiry">Expirare Permis</label>
                    <input class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-700" 
                           id="license_expiry" name="license_expiry" type="date">
                </div>

                <!-- Cont Acces -->
                <div class="md:col-span-2">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mt-4 mb-4 flex items-center">
                        <span class="w-8 h-[1px] bg-slate-200 mr-3"></span>
                        Date Acces Cont
                        <span class="flex-grow h-[1px] bg-slate-200 ml-3"></span>
                    </h3>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2" for="email">Adresă Email</label>
                    <input class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-700" 
                           id="email" name="email" type="email" placeholder="nume@exemplu.ro" required>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2" for="password">Parolă Acces (minim 6 caractere)</label>
                    <input class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-700" 
                           id="password" name="password" type="password" minlength="6" required>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 px-8 rounded-2xl shadow-xl shadow-blue-100 transition-all hover:-translate-y-1 active:scale-[0.98] uppercase tracking-widest text-sm">
                    Finalizează Înregistrarea
                </button>
                <p class="text-center text-slate-400 mt-6 text-xs italic">După înregistrare, contul tău va fi trimis spre aprobare administratorului flotei.</p>
            </div>
        </form>
    </div>
</body>
</html>
