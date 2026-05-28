<?php
require_once __DIR__ . '/../../src/config/autoload.php';

use App\Middleware\Auth;
use App\Models\Module;
use App\Models\ErrorLog;

Auth::requireAuth();
$employee = Auth::getEmployee();

$moduleId = (int)($_GET['id'] ?? 0);
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['q'] ?? '');

if ($moduleId === 0) {
    header('Location: /index.php');
    exit;
}

$moduleModel = new Module();
$errorModel = new ErrorLog();

$module = $moduleModel->findById($moduleId);
if (!$module) {
    header('Location: /index.php');
    exit;
}

$errors = $errorModel->getByModule($moduleId, $page, $search);
$total = $errorModel->countByModule($moduleId, $search);
$perPage = $errorModel->getPerPage();
$totalPages = (int)ceil($total / $perPage);

$pageTitle = $module['name_lo'] . ' — SAP B1 Error Log';

include __DIR__ . '/../../src/views/components/header.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-gray-900">

    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6 animate-fade-in-up">
        <a href="/index.php" class="hover:text-gray-700 transition-colors">Dashboard</a>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-600 font-medium"><?= htmlspecialchars($module['name_lo']) ?></span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7 animate-fade-in-up Associations-1">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:<?= htmlspecialchars($module['color'], ENT_QUOTES, 'UTF-8') ?>15">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="color:<?= htmlspecialchars($module['color'], ENT_QUOTES, 'UTF-8') ?>">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($module['name_lo']) ?></h1>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($module['name_en']) ?> · <?= $total ?> ລາຍການ</p>
            </div>
        </div>
        <a href="/errors/add.php?module_id=<?= $module['id'] ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            ເພີ່ມ Error
        </a>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 mb-6 animate-fade-in-up Associations-2">
        <form method="GET" action="" class="flex gap-2 flex-1 max-w-md">
            <input type="hidden" name="id" value="<?= $moduleId ?>">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </div>
                <input type="text" name="q" class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 shadow-sm transition-all" placeholder="ຄົ້ນຫາ error..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-xl transition-colors">ຄົ້ນ</button>
            <?php if ($search): ?>
            <a href="?id=<?= $moduleId ?>" class="bg-gray-50 border border-gray-200 text-gray-500 hover:text-gray-700 text-sm font-medium px-4 py-2 rounded-xl transition-colors flex items-center">ລ້າງ</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($errors)): ?>
    <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm animate-fade-in-up Associations-3">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
        <p class="text-gray-500 text-sm">ຍັງບໍ່ມີ Error ໃນ module ນີ້</p>
        <?php if ($search): ?>
        <p class="text-gray-400 text-xs mt-1">ບໍ່ພົບຜົນລັດການຄົ້ນຫາ "<?= htmlspecialchars($search) ?>"</p>
        <?php endif; ?>
    </div>
    <?php else: ?>

    <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden animate-fade-in-up Associations-3">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/70">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Error Message</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">ວັນທີ</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">ລາຍງານໂດຍ</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">ສະຖານະ</th>
                        <th class="px-4 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($errors as $err): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="text-sm font-mono text-gray-800 truncate max-w-xs md:max-w-md"><?= htmlspecialchars($err['error_message']) ?></p>
                        </td>
                        <td class="px-4 py-4 hidden sm:table-cell">
                            <p class="text-xs text-gray-500"><?= $err['occurred_at'] ?></p>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell">
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($err['employee_name']) ?></p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full <?= $err['status'] === 'resolved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' ?>">
                                <?= $err['status'] === 'resolved' ? 'ແກ້ໄຂແລ້ວ' : 'ລໍຖ້າ' ?>
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <button onclick="openDetailModal(<?= $err['id'] ?>)" class="text-xs text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 font-medium px-3 py-1.5 rounded-lg transition-all">
                                ລາຍລະອຽດ
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-6 animate-fade-in-up">
        <p class="text-xs text-gray-500 font-medium">
            ສະແດງ <?= (($page-1)*$perPage)+1 ?>–<?= min($page*$perPage, $total) ?> ຈາກ <?= $total ?> ລາຍການ
        </p>
        <div class="flex items-center gap-1">
            <?php if ($page > 1): ?>
            <a href="?id=<?= $moduleId ?>&page=<?= $page-1 ?>&q=<?= urlencode($search) ?>" class="p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200 bg-white shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <a href="?id=<?= $moduleId ?>&page=<?= $p ?>&q=<?= urlencode($search) ?>" class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all <?= $p === $page ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm shadow-indigo-500/10' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
            <a href="?id=<?= $moduleId ?>&page=<?= $page+1 ?>&q=<?= urlencode($search) ?>" class="p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200 bg-white shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</main>

<?php include __DIR__ . '/../../src/views/components/footer.php'; ?>