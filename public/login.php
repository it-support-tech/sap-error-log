<?php
require_once __DIR__ . '/../src/config/autoload.php';

use App\middleware\Auth;
use App\models\Employee;

Auth::start();

if (Auth::check()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $error = 'ກະລຸນາໃສ່ຊື່ຂອງທ່ານ';
    } else {
        $emp = new Employee();
        $employee = $emp->findOrCreate($name);
        Auth::login($employee['id'], $employee['name']);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ເຂົ້າສູ່ລະບົບ — SAP Business One Error Log</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="bg-gray-50 font-lao flex items-center justify-center min-h-screen">

<div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full" style="background:radial-gradient(circle,rgba(99,102,241,0.05),transparent 70%);filter:blur(40px)"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 rounded-full" style="background:radial-gradient(circle,rgba(139,92,246,0.04),transparent 70%);filter:blur(40px)"></div>
</div>

<div class="w-full max-w-sm px-4 animate-fade-in-up z-10">
    <div class="text-center mb-8">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-xl shadow-indigo-500/20">
            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">SAP Business One Error Log</h1>
        <p class="text-gray-500 text-sm mt-1">ລະບົບຈັດການ Error SAP Business One</p>
    </div>

    <div class="bg-white border border-gray-200/80 shadow-sm rounded-2xl p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">ໃສ່ຊື່ເພື່ອເຂົ້າໃຊ້ງານ</h2>

        <?php if ($error): ?>
        <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ພະນັກງານ <span class="text-rose-500">*</span></label>
                <input type="text" name="name" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm" placeholder="ໃສ່ຊື່ຂອງທ່ານ..." autofocus
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl px-4 py-3 text-sm flex items-center justify-center gap-2 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
                ເຂົ້າສູ່ລະບົບ
            </button>
        </form>
    </div>
</div>

<script src="/js/app.js"></script>
</body>
</html>