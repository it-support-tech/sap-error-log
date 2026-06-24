<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM error_logs WHERE id = ?");
$stmt->execute([$id]);
$log = $stmt->fetch();

if (!$log) {
    echo "<div class='p-8 w-full text-center text-red-500'>Error: ບໍ່ພົບຂໍ້ມູນລາຍການນີ້ໃນລະບົບ.</div>";
    require_once 'includes/footer.php';
    exit;
}

$related_stmt = $pdo->prepare("SELECT id, error_message FROM error_logs WHERE module = ? AND id != ? LIMIT 8");
$related_stmt->execute([$log['module'], $log['id']]);
$related_articles = $related_stmt->fetchAll();
?>

<div class="w-3/4 bg-white p-8 pr-12 border-r border-gray-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
    <div class="mb-6 text-sm">
        <a href="index.php" class="text-[#0000ee] hover:underline">Solution home</a> / 
        <a href="folder.php?module=<?= urlencode($log['module']) ?>" class="text-[#0000ee] hover:underline">SAP Business One</a> / 
        <span class="text-gray-500"><?= htmlspecialchars($log['module']) ?></span>
    </div>

    <div class="flex justify-between items-start gap-4 mb-2">
        <h2 class="text-2xl text-gray-800 font-bold leading-snug">SAP Business One : Error : <?= htmlspecialchars($log['error_message']) ?></h2>
        <button onclick="window.print()" class="text-gray-400 text-sm hover:text-gray-600 flex items-center gap-1 border border-gray-200 px-2 py-1 rounded shadow-sm">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>

    <div class="text-xs text-gray-400 mb-6 space-y-1">
        <div>Created by: <span class="font-medium text-gray-600">NTP Support (Reported by: <?= htmlspecialchars($log['reported_by']) ?>)</span></div>
        <div>Found Date: <span class="font-medium text-gray-600"><?= htmlspecialchars($log['found_date']) ?></span></div>
        <div>Status: <span class="px-1.5 py-0.5 rounded font-bold text-[10px] <?= $log['status'] === 'Fixed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>"><?= $log['status'] ?></span></div>
    </div>

    <div class="space-y-6 text-sm text-gray-800 leading-relaxed">
        
        <?php if (!empty($log['image_path'])): ?>
            <div class="my-6 border border-gray-200 rounded-lg p-2 bg-gray-50 shadow-sm max-w-2xl">
                <img src="<?= htmlspecialchars($log['image_path']) ?>" alt="SAP Error Screenshot" class="w-full h-auto rounded border border-gray-300">
            </div>
            <div class="mb-6">
                <a href="<?= htmlspecialchars($log['image_path']) ?>" target="_blank" class="text-blue-600 hover:underline text-xs font-medium inline-flex items-center gap-1">
                    <i class="fa-solid fa-image"></i> View Actual Uploaded Screenshot Attachment
                </a>
            </div>
        <?php endif; ?>

        <div>
            <h4 class="font-bold text-gray-900 text-base">ສາເຫດ :</h4>
            <div class="pl-4 text-gray-700">
                <?= htmlspecialchars($log['cause']) ?>
            </div>
        </div>

        <div>
            <h4 class="font-bold text-gray-900 text-base mb-2">ອາການພົບບັນຫາ :</h4>
            <div class="pl-4 text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-200 italic">
                <?= htmlspecialchars($log['symptoms']) ?>
            </div>
        </div>

        <div>
            <h4 class="font-bold text-gray-900 text-base mb-2">ວິທີການແກ້ໄຂບັນຫາ :</h4>
            <div class="pl-4 text-gray-700 whitespace-pre-line">
                <?= htmlspecialchars($log['solution']) ?>
            </div>
        </div>

        <?php if (!empty($log['video_link'])): ?>
            <div class="pt-4 border-t border-gray-100">
                <h4 class="font-bold text-gray-900 text-sm mb-2">ວິດີໂອປະກອບການແກ້ໄຂ :</h4>
                <a href="<?= htmlspecialchars($log['video_link']) ?>" target="_blank" class="text-[#0000ee] hover:underline flex items-center gap-2">
                    <i class="fa-solid fa-video text-red-500"></i> ກົດທີ່ນີ້ເພື່ອເບິ່ງວິດີໂອແກ້ໄຂບັນຫາ
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="w-1/4 bg-[#fbfbfb] p-6 border-l border-gray-50">
    <h4 class="font-bold text-sm text-gray-800 mb-4 tracking-wide uppercase">Related Articles</h4>
    <ul class="space-y-4 text-xs text-gray-700">
        <?php if (count($related_articles) > 0): ?>
            <?php foreach ($related_articles as $rel): ?>
                <li class="flex items-start gap-2 group">
                    <i class="fa-solid fa-file-lines text-gray-400 mt-0.5"></i>
                    <a href="article.php?id=<?= $rel['id'] ?>" class="hover:text-[#0000ee] leading-tight line-clamp-2">SAP Business One : Error : <?= htmlspecialchars($rel['error_message']) ?></a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="text-gray-400 italic">No related articles found.</li>
        <?php endif; ?>
    </ul>
</div>

<?php require_once 'includes/footer.php'; ?>