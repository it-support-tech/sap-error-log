<?php
require_once dirname(dirname(__DIR__)) . '/src/config/autoload.php';

use App\Middleware\Auth;
use App\Models\ErrorLog;

Auth::requireAuth();
$employee = Auth::getEmployee();

$query = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

$errorModel = new ErrorLog();
$results = [];
$total = 0;
$totalPages = 0;
$perPage = $errorModel->getPerPage();

if ($query !== '') {
    $results = $errorModel->search($query, $page);
    $total = $errorModel->countSearch($query);
    $totalPages = (int)ceil($total / $perPage);
}

$pageTitle = 'ຄົ້ນຫາ — SAP B1 Error Log';

include dirname(dirname(__DIR__)) . '/src/views/components/header.php';
?>

<main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-gray-900">

    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6 animate-fade-in-up">
        <a href="/index.php" class="hover:text-gray-700 transition-colors">Dashboard</a>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-600 font-medium">ຄົ້ນຫາ</span>
    </div>

    <div class="mb-7 animate-fade-in-up stagger-1">
        <h1 class="text-xl font-bold text-gray-900">ຄົ້ນຫາ Error</h1>
        <?php if ($query): ?>
        <p class="text-sm text-gray-500 mt-0.5">ຜົນລັດ <span class="font-semibold text-gray-800"><?= $total ?></span> ລາຍການ ສຳລັບ "<span class="text-indigo-600 font-medium"><?= htmlspecialchars($query) ?></span>"</p>
        <?php endif; ?>
    </div>

    <form action="" method="GET" class="mb-6 animate-fade-in-up stagger-2">
        <div class="flex gap-2 max-w-lg">
            <div class="relative flex-1 items-center flex">
                <svg class="w-4 h-4 text-gray-400 absolute left-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text" name="q" 
                       class="w-full bg-white border border-gray-300 rounded-xl pl-11 pr-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all shadow-sm" 
                       placeholder="ຄົ້ນຫາ error ທຸກ module..." 
                       value="<?= htmlspecialchars($query) ?>" autofocus>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl shadow-sm transition-all flex-shrink-0">
                ຄົ້ນຫາ
            </button>
        </div>
    </form>

    <?php if ($query && empty($results)): ?>
    <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center shadow-sm animate-fade-in-up stagger-3">
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <p class="text-gray-400 text-sm">ບໍ່ພົບຂໍ້ມູນ Error ທີ່ກ່ຽວຂ້ອງ</p>
    </div>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
    <div class="space-y-3 animate-fade-in-up stagger-3">
        <?php foreach ($results as $err): ?>
        <div class="bg-white border border-gray-200 hover:border-gray-300 rounded-2xl p-4 sm:p-5 flex items-start justify-between gap-4 shadow-sm transition-all">
            <div class="flex items-start gap-3 min-w-0">
                <div class="w-2 h-2 rounded-full flex-shrink-0 mt-2" style="background:<?= htmlspecialchars($err['module_color']) ?>"></div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background:<?= htmlspecialchars($err['module_color']) ?>12;color:<?= htmlspecialchars($err['module_color']) ?>;border:1px solid <?= htmlspecialchars($err['module_color']) ?>25">
                            <?= htmlspecialchars($err['module_name_lo']) ?>
                        </span>
                        <?php if ($err['status'] === 'resolved'): ?>
                            <span class="px-2 py-0.5 text-[11px] font-medium rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>ແກ້ໄຂແລ້ວ
                            </span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 text-[11px] font-medium rounded-full bg-amber-50 text-amber-700 border border-amber-100 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>ລໍຖ້າ
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm font-semibold font-mono text-gray-800 truncate mb-1 break-all"><?= htmlspecialchars($err['error_message']) ?></p>
                    <p class="text-xs text-gray-400 font-medium"><?= htmlspecialchars($err['employee_name']) ?> · <span class="text-gray-500"><?= $err['occurred_at'] ?></span></p>
                </div>
            </div>
            <button onclick="openDetailModal(<?= $err['id'] ?>)" class="text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-xl transition-all flex-shrink-0">
                ລາຍລະອຽດ
            </button>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100 animate-fade-in-up">
        <p class="text-xs font-medium text-gray-400">ໜ້າ <?= $page ?> / <?= $totalPages ?></p>
        <div class="flex items-center gap-1">
            <?php if ($page > 1): ?>
            <a href="?q=<?= urlencode($query) ?>&page=<?= $page-1 ?>" class="p-2 text-gray-500 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <?php endif; ?>
            
            <?php for ($p = max(1, $page-2); $p <= min($totalPages, $page+2); $p++): ?>
            <a href="?q=<?= urlencode($query) ?>&page=<?= $p ?>" 
               class="px-3.5 py-1.5 text-xs font-semibold rounded-xl transition-all <?= $p === $page ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/10' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' ?>">
                <?= $p ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="?q=<?= urlencode($query) ?>&page=<?= $page+1 ?>" class="p-2 text-gray-500 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

</main>

<?php include dirname(dirname(__DIR__)) . '/src/views/components/footer.php'; ?>