<?php

use App\Middleware\Auth;

$employee = Auth::getEmployee();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'SAP B1 Error Log') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="icon" type="image/png" href="/assets/logo.png">

</head>
<body class="bg-gray-50 font-lao text-gray-900 min-h-screen flex flex-col">

<nav class="fixed top-0 left-0 right-0 z-50 nav-glass border-b border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="/index.php" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl   flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:shadow-indigo-500/50 transition-all duration-300">
                    <img src="/assets/logo.png" alt="SAP Business One Error Log " class= rounded-2xl w-9 h-9 >
                </div>
                <div>
                    <p class="text-sm font-semibold text-white leading-none">SAP B1</p>
                    <p class="text-xs text-gray-400 leading-none mt-0.5">Error Log System</p>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-1">
                <a href="/index.php" class="nav-link <?= $currentPage === 'index' ? 'nav-link-active' : '' ?>">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                    </svg>
                    Dashboard
                </a>
                <a href="/errors/add.php" class="nav-link <?= $currentPage === 'add' ? 'nav-link-active' : '' ?>">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    ເພີ່ມ Error
                </a>
            </div>

            <div class="flex items-center gap-3">
                <?php if ($employee): ?>
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10">
                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-xs font-bold text-white">
                        <?= mb_substr($employee['name'], 0, 1) ?>
                    </div>
                    <span class="text-sm text-gray-300 hidden sm:block"><?= htmlspecialchars($employee['name']) ?></span>
                </div>
                <a href="/logout.php" class="btn-ghost text-xs px-3 py-1.5">ອອກ</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="pt-16">
