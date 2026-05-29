<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
require_once __DIR__ . '/../src/config/autoload.php';

use App\middleware\Auth;
use App\models\Module;
use App\models\ErrorLog;

Auth::requireAuth();
$employee = Auth::getEmployee();

$moduleModel = new Module();
$errorModel = new ErrorLog();

$modules = $moduleModel->getAll();
$recentErrors = $errorModel->getRecentAll(5);

$totalErrors = array_sum(array_column($modules, 'total_errors'));
$totalResolved = array_sum(array_column($modules, 'resolved_count'));
$totalPending = array_sum(array_column($modules, 'pending_count'));

$pageTitle = 'Dashboard — SAP B1 Error Log';

$iconMap = [
    'administration' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43|l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
    'financials' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>',
    'sales' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>',
    'purchasing' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>',
    'inventory' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>',
    'business-partners' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>',
    'banking' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>',
    'Fixed Asset' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />',
];

include dirname(__DIR__) . '/src/views/components/header.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-gray-900">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 animate-fade-in-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 text-sm mt-0.5">ລາຍການ Error ທັງໝົດໃນ SAP Business One</p>
        </div>
        <a href="/errors/add.php" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition-all w-fit">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            ເພີ່ມ Error ໃໝ່
        </a>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-5 animate-fade-in-up Associations-1">
            <p class="text-xs text-gray-500 font-medium mb-1">Error ທັງໝົດ</p>
            <p class="text-2xl font-bold text-gray-900"><?= number_format($totalErrors) ?></p>
        </div>
        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-5 animate-fade-in-up Associations-2">
            <p class="text-xs text-gray-500 font-medium mb-1">ແກ້ໄຂແລ້ວ</p>
            <p class="text-2xl font-bold text-emerald-600"><?= number_format($totalResolved) ?></p>
        </div>
        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-5 animate-fade-in-up Associations-3">
            <p class="text-xs text-gray-500 font-medium mb-1">ລໍຖ້າແກ້ໄຂ</p>
            <p class="text-2xl font-bold text-amber-600"><?= number_format($totalPending) ?></p>
        </div>
    </div>

    <div class="mb-6 animate-fade-in-up Associations-4">
        <form action="/errors/search.php" method="GET" class="relative max-w-lg">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
            </div>
            <input type="text" name="q" class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm transition-all" placeholder="ຄົ້ນຫາ error ໃນທຸກ module..." value="">
        </form>
    </div>

    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 animate-fade-in-up Associations-5">Modules ທັງໝົດ</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-10">
        <?php foreach ($modules as $i => $module): ?>
        <a href="/errors/module.php?id=<?= $module['id'] ?>" class="bg-white border border-gray-200 hover:border-indigo-500/30 hover:shadow-md transition-all p-5 rounded-2xl block no-underline animate-fade-in-up Associations-<?= min($i + 1, 8) ?>">
            <div class="flex items-start justify-between mb-4">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:<?= htmlspecialchars($module['color'], ENT_QUOTES, 'UTF-8') ?>15">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="color:<?= htmlspecialchars($module['color'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= $iconMap[$module['code']] ?? '<path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>' ?>
                    </svg>
                </div>
                <span class="text-2xl font-bold" style="color:<?= htmlspecialchars($module['color'], ENT_QUOTES, 'UTF-8') ?>"><?= $module['total_errors'] ?></span>
            </div>
            <p class="text-sm font-semibold text-gray-900 mb-0.5 leading-snug"><?= htmlspecialchars($module['name_en']) ?></p>
            <p class="text-xs text-gray-400 mb-3"><?= htmlspecialchars($module['name_lo']) ?></p>
            <div class="flex items-center gap-3">
                <span class="text-xs text-emerald-600 flex items-center gap-1 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                    <?= $module['resolved_count'] ?> ແກ້ໄຂ
                </span>
                <?php if ($module['pending_count'] > 0): ?>
                <span class="text-xs text-amber-600 flex items-center gap-1 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                    <?= $module['pending_count'] ?> ລໍ
                </span>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($recentErrors)): ?>
    <div class="animate-fade-in-up">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">ລາຍການລ່າສຸດ</h2>
        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden divide-y divide-gray-100">
            <?php foreach ($recentErrors as $err): ?>
            <div class="px-5 py-4 flex items-center justify-between gap-4 hover:bg-gray-50/80 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:<?= htmlspecialchars($err['module_color'], ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="min-w-0">
                        <p class="text-sm font-mono text-gray-800 truncate"><?= htmlspecialchars($err['error_message']) ?></p>
                        <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($err['module_name_lo']) ?> · <?= htmlspecialchars($err['employee_name']) ?> · <?= $err['occurred_at'] ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full <?= $err['status'] === 'resolved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' ?>">
                        <?= $err['status'] === 'resolved' ? 'ແກ້ໄຂແລ້ວ' : 'ລໍຖ້າ' ?>
                    </span>
                    <button onclick="openDetailModal(<?= $err['id'] ?>)" class="text-xs text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-2.5 py-1.5 rounded-lg transition-colors">ລາຍລະອຽດ</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php include dirname(__DIR__) . '/src/views/components/footer.php'; ?>