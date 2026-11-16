<?php
/**
 * Super Admin Password Reset Script
 * ⚠️  ȘTERGE ACEST FIȘIER DUPĂ UTILIZARE!
 */

require_once __DIR__ . '/../fleetlog/core/Autoloader.php';
require_once __DIR__ . '/../fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;

Autoloader::register();
EnvLoader::load(__DIR__ . '/../fleetlog/.env');

$message = '';
$error = '';
$adminInfo = null;

// Fetch current super admin info
try {
    $adminInfo = DB::fetchAll("SELECT id, name, email, role FROM users WHERE role = 'super_admin' ORDER BY id ASC");
} catch (\Throwable $e) {
    $error = "Eroare conexiune DB: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId    = (int)($_POST['user_id'] ?? 0);
    $newPass   = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if (empty($newPass) || strlen($newPass) < 6) {
        $error = "Parola trebuie să aibă cel puțin 6 caractere.";
    } elseif ($newPass !== $confirmPass) {
        $error = "Parolele nu coincid.";
    } elseif ($userId <= 0) {
        $error = "Selectează un utilizator valid.";
    } else {
        try {
            $hash = password_hash($newPass, PASSWORD_BCRYPT);
            DB::query("UPDATE users SET password = ? WHERE id = ? AND role = 'super_admin'", [$hash, $userId]);
            $message = "✅ Parola a fost resetată cu succes pentru utilizatorul cu ID: $userId. Poți șterge acum acest fișier.";
        } catch (\Throwable $e) {
            $error = "Eroare la resetare: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Reset Super Admin Password</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 40px; width: 100%; max-width: 480px; }
        .warning { background: #7f1d1d; border: 1px solid #ef4444; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; color: #fca5a5; font-size: 13px; font-weight: 600; }
        h1 { color: #f1f5f9; font-size: 20px; font-weight: 800; margin-bottom: 6px; }
        p.sub { color: #94a3b8; font-size: 13px; margin-bottom: 28px; }
        label { display: block; color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
        select, input[type=password] { width: 100%; background: #0f172a; border: 1px solid #475569; border-radius: 10px; padding: 12px 16px; color: #f1f5f9; font-size: 14px; font-weight: 600; margin-bottom: 20px; outline: none; }
        select:focus, input:focus { border-color: #6366f1; }
        button { width: 100%; background: #6366f1; color: white; font-weight: 800; font-size: 14px; padding: 14px; border: none; border-radius: 10px; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #4f46e5; }
        .success { background: #14532d; border: 1px solid #22c55e; border-radius: 10px; padding: 14px 18px; margin-top: 20px; color: #86efac; font-size: 14px; font-weight: 600; }
        .error { background: #7f1d1d; border: 1px solid #ef4444; border-radius: 10px; padding: 14px 18px; margin-top: 20px; color: #fca5a5; font-size: 14px; font-weight: 600; }
        .users-info { background: #0f172a; border: 1px solid #334155; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; }
        .users-info .user-row { display: flex; align-items: center; gap: 10px; padding: 6px 0; border-bottom: 1px solid #1e293b; color: #cbd5e1; font-size: 13px; }
        .users-info .user-row:last-child { border-bottom: none; }
        .badge { background: #312e81; color: #a5b4fc; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }
    </style>
</head>
<body>
<div class="card">
    <div class="warning">
        ⚠️ ATENȚIE: Șterge imediat acest fișier după utilizare!<br>
        <code style="font-size:11px; opacity:0.8">tmp/reset_admin_password.php</code>
    </div>

    <h1>Reset Parolă Super Admin</h1>
    <p class="sub">Setează o parolă nouă pentru contul de administrator principal.</p>

    <?php if (!empty($adminInfo)): ?>
    <div class="users-info">
        <div style="font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase; margin-bottom:10px;">Conturi găsite:</div>
        <?php foreach ($adminInfo as $a): ?>
        <div class="user-row">
            <span class="badge">ID: <?= $a['id'] ?></span>
            <strong><?= htmlspecialchars($a['name']) ?></strong>
            <span style="color:#64748b"><?= htmlspecialchars($a['email']) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST">
        <label>Selectează Contul</label>
        <select name="user_id">
            <?php foreach ($adminInfo ?? [] as $a): ?>
                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?> (<?= htmlspecialchars($a['email']) ?>)</option>
            <?php endforeach; ?>
        </select>

        <label>Parolă Nouă</label>
        <input type="password" name="new_password" placeholder="Minimum 6 caractere" required>

        <label>Confirmă Parola</label>
        <input type="password" name="confirm_password" placeholder="Repetă parola" required>

        <button type="submit">Resetează Parola</button>
    </form>

    <?php if ($message): ?><div class="success"><?= $message ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error">❌ <?= htmlspecialchars($error) ?></div><?php endif; ?>
</div>
</body>
</html>
