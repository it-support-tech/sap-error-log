<?php
// index.php
require_once 'config/database.php';
require_once 'includes/header.php';

$modules = [
    "Administration" => "ການຈັດການລະບົບ (Administration)",
    "Financials" => "ການເງິນ ການບັນຊີ (Financials)",
    "Sales" => "ການຂາຍ (Sales - A/R)",
    "Purchasing" => "ຈັດຊື້ (Purchasing - A/P)",
    "Business Partners" => "ຄູ່ຮ່ວມທຸລະກິດ (Business Partners)",
    "Banking" => "ການທະນາຄານ (Banking)",
    "Inventory" => "ສາງ (Inventory)",
    "Fixed Asset" => "ຊັບສິນ (Fixed Asset)"
];

$logs_by_module = [];
$counts = [];

foreach ($modules as $key => $title) {
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM error_logs WHERE module = ?");
    $count_stmt->execute([$key]);
    $counts[$key] = $count_stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT id, error_message FROM error_logs WHERE module = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$key]);
    $logs_by_module[$key] = $stmt->fetchAll();
}
?>

<div class="w-3/4 bg-white p-8 pr-12 border-r border-gray-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
    <div class="mb-6">
        <a href="index.php" class="text-[#0000ee] text-sm hover:underline">Solution home</a>
    </div>

    <h2 class="text-3xl text-gray-800 font-normal mb-4">SAP Business One</h2>
    <div class="w-full h-px bg-[#2eb85c] mb-8"></div>

    <div class="grid grid-cols-2 gap-x-12 gap-y-10">
        <?php foreach ($modules as $key => $title): ?>
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <a href="folder.php?module=<?= urlencode($key) ?>" class="hover:text-[#0000ee]"><?= htmlspecialchars($title) ?></a>
                    <span class="text-gray-400 font-normal text-base">(<?= $counts[$key] ?>)</span>
                </h3>
                <ul class="space-y-3 text-sm text-gray-700">
                    <?php if (!empty($logs_by_module[$key])): ?>
                        <?php foreach ($logs_by_module[$key] as $log): ?>
                            <li class="flex items-start gap-3 hover:text-[#0000ee] cursor-pointer group">
                                <i class="fa-solid fa-book-open text-gray-400 mt-0.5 group-hover:text-[#0000ee]"></i>
                                <a href="article.php?id=<?= $log['id'] ?>" class="truncate w-full block">
                                    <?= htmlspecialchars($log['error_message']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-gray-400 italic">No articles found in this module.</li>
                    <?php endif; ?>
                </ul>
                <a href="folder.php?module=<?= urlencode($key) ?>" class="block mt-4 text-sm text-[#0000ee] hover:underline">View all <?= $counts[$key] ?></a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="w-1/4 bg-[#fbfbfb]"></div>

<?php require_once 'includes/footer.php'; ?>