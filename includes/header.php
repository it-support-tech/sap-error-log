<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'solutions';

if ($current_page === 'edit.php') {
    $current_tab = 'edit';
} elseif ($current_page !== 'index.php' && $current_page !== 'edit.php') {
    $current_tab = 'solutions';
}

if (($current_page === 'create.php' || $current_page === 'edit.php') && !isset($_SESSION['username'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        if (!empty(trim($_POST['username']))) {
            $_SESSION['username'] = trim($_POST['username']);
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login - SAP Business One Solutions</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded shadow-md w-96 text-center border-t-4 border-[#2eb85c]">
            <div class="flex justify-center font-black text-4xl tracking-tighter mb-2">
                <span class="text-[#333333]">N</span><span class="text-[#333333]">T</span><span class="text-[#c2593b]">P</span>
            </div>
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Internal Error Log System</h2>
            <form method="POST">
                <div class="mb-4 text-left">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Enter your name to continue</label>
                    <input type="text" name="username" required placeholder="e.g. John Doe" class="w-full border border-gray-300 rounded p-2 outline-none focus:ring-1 focus:ring-[#2eb85c]">
                </div>
                <button type="submit" name="login" class="w-full bg-[#2eb85c] hover:bg-green-600 text-white font-semibold py-2 rounded transition">LOGIN</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NTP Trading Petroleum Company Limited - Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
    </style>
</head>
<body class="bg-white text-gray-800 min-h-screen">

    <header class="w-full border-b border-gray-200">
        <div class="max-w-[1200px] mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="flex font-black text-4xl tracking-tighter">
                    <span class="text-[#333333]">N</span><span class="text-[#333333]">T</span><span class="text-[#c2593b]">P</span>
                </div>
                <h1 class="text-xl text-gray-500 font-medium tracking-wide">NTP Trading Petroleum Company Limited</h1>
            </div>
            <div class="flex flex-col items-end gap-1">
                <?php if (isset($_SESSION['username'])): ?>
                    <span class="text-sm text-gray-500">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                    <a href="?logout=true" class="text-xs text-red-500 hover:underline">Logout</a>
                <?php else: ?>
                    <span class="text-sm text-gray-500">Welcome</span>
                    <a href="create.php" class="bg-[#2eb85c] hover:bg-green-600 text-white text-sm font-semibold py-1.5 px-6 rounded shadow-sm">LOGIN</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <nav class="w-full border-b border-gray-200">
        <div class="max-w-[1200px] mx-auto px-6 flex text-sm font-medium">
            <a href="index.php?tab=home" class="px-4 py-3 <?= $current_tab === 'home' ? 'text-gray-800 border-b-2 border-b-[#2eb85c]' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 transition-colors' ?>">Home</a>
            <a href="index.php?tab=solutions" class="px-4 py-3 <?= $current_tab === 'solutions' ? 'text-gray-800 border-b-2 border-b-[#2eb85c]' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 transition-colors' ?>">Solutions</a>
            <a href="edit.php" class="px-4 py-3 <?= $current_tab === 'edit' ? 'text-gray-800 border-b-2 border-b-[#2eb85c]' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50 transition-colors' ?>">Edit Logs</a>
        </div>
    </nav>

    <div class="w-full bg-[#f8f9fa] border-b border-gray-200 border-t-2 border-t-[#2eb85c]">
        <div class="max-w-[1200px] mx-auto px-6 py-4 flex justify-between items-center">
            <form method="GET" action="folder.php" class="relative w-1/3 min-w-[300px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Enter your search term here..." class="w-full pl-10 pr-4 py-2 border border-transparent bg-white rounded shadow-sm focus:outline-none focus:border-gray-300 focus:ring-1 focus:ring-gray-200 text-sm">
            </form>
            <div class="flex items-center gap-6 text-sm text-gray-600">
                <a href="create.php" class="flex items-center gap-2 hover:text-gray-900 font-medium text-[#2eb85c]">
                    <i class="fa-solid fa-square-plus text-lg"></i> New Error Log
                </a>
            </div>
        </div>
    </div>

    <main class="w-full bg-[#fbfbfb]">
        <div class="max-w-[1200px] mx-auto flex min-h-[600px]">