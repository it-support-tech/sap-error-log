<?php
// folder.php
require_once 'config/database.php';
require_once 'includes/header.php';

$module_key = $_GET['module'] ?? '';
$search = $_GET['search'] ?? '';

$modules = [
    "Administration" => "ແອດມິນ (Administration)",
    "Financials" => "ການເງິນ ການບັນຊີ (Financials)",
    "Sales" => "ການຂາຍ (Sales - A/R)",
    "Purchasing" => "ຈັດຊື້ (Purchasing - A/P)",
    "Business Partners" => "ຄູ່ຮ່ວມທຸລະກິດ (Business Partners)",
    "Banking" => "ການທະນາຄານ (Banking)",
    "Inventory" => "ສາງ (Inventory)",
    "Fixed Asset" => "ຊັບສິນ (Resources)"
];

$title_display = "All Articles";
if (array_key_exists($module_key, $modules)) {
    $title_display = $modules[$module_key];
    $stmt = $pdo->prepare("SELECT * FROM error_logs WHERE module = ? ORDER BY created_at DESC");
    $stmt->execute([$module_key]);
} elseif (!empty($search)) {
    $title_display = "Search Results for \"" . htmlspecialchars($search) . "\"";
    $stmt = $pdo->prepare("SELECT * FROM error_logs WHERE error_message ILIKE ? OR cause ILIKE ? OR symptoms ILIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM error_logs ORDER BY created_at DESC");
}

$logs = $stmt->fetchAll();
?>

<div class="w-3/4 bg-white p-8 pr-12 border-r border-gray-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
    <div class="mb-4 text-sm">
        <a href="index.php" class="text-[#0000ee] hover:underline">Solution home</a>
        <?php if ($module_key): ?>
            <span class="text-gray-400"> / SAP Business One</span>
        <?php endif; ?>
    </div>

    <h2 class="text-3xl text-gray-800 font-normal mb-1"><?= htmlspecialchars($title_display) ?></h2>
    <p class="text-gray-400 text-xs mb-4"><?= $module_key ? htmlspecialchars($module_key) : '' ?></p>

    <div class="space-y-6 mt-6">
        <?php if (count($logs) > 0): ?>
            <?php foreach ($logs as $log): ?>
                <div class="flex items-start gap-4 pb-6 border-b border-gray-100 last:border-0">
                    <i class="fa-solid fa-file-lines text-gray-300 text-3xl mt-1"></i>
                    <div>
                        <h4 class="text-base font-bold text-[#0000ee] hover:underline mb-1">
                            <a href="article.php?id=<?= $log['id'] ?>"><?= htmlspecialchars($log['error_message']) ?></a>
                        </h4>
                        <p class="text-sm text-gray-500 line-clamp-2 max-w-[700px] mb-2">
                            Error : <?= htmlspecialchars($log['error_message']) ?> ສາເຫດ: <?= htmlspecialchars($log['cause'] ?? 'ບໍ່ມີ') ?>
                        </p>
                        <span class="text-xs text-gray-400">
                            Modified on: <?= date('D, d M, Y \a\t g:i A', strtotime($log['created_at'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-500 italic">No articles found.</p>
        <?php endif; ?>
    </div>
</div>

<div class="w-1/4 bg-[#fbfbfb]"></div>

<?php require_once 'includes/footer.php'; ?>