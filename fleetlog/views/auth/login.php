<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'FleetLog'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md" x-data="{ loginMode: 'password' }">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">FleetLog</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" name="email" type="email" required>
            </div>
            
            <div class="mb-6" x-show="loginMode === 'password'">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" :required="loginMode === 'password'">
            </div>

            <div class="mb-6" x-show="loginMode === 'pin'" x-cloak>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="pin">PIN de Acces</label>
                <input class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-center text-2xl tracking-[1em]" id="pin" name="pin" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="****" :required="loginMode === 'pin'">
                <p class="text-xs text-gray-500 mt-1 italic text-center">Șoferii pot folosi PIN-ul setat în profil.</p>
            </div>

            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Ține-mă minte
                    </label>
                </div>
                <button type="button" @click="loginMode = (loginMode === 'password' ? 'pin' : 'password')" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    <span x-show="loginMode === 'password'">Login cu PIN</span>
                    <span x-show="loginMode === 'pin'">Login cu Parolă</span>
                </button>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full" type="submit">
                    Sign In
                </button>
            </div>
        </form>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
