<?php
require_once 'config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_log'])) {
    $log_id = intval($_POST['id']);
    $solution = trim($_POST['solution']);
    $video_link = trim($_POST['video_link']);
    $status = trim($_POST['status']);

    $update_stmt = $pdo->prepare("UPDATE error_logs SET solution = ?, video_link = ?, status = ? WHERE id = ?");
    if ($update_stmt->execute([$solution, $video_link, $status, $log_id])) {
        header("Location: edit.php?success=1");
        exit;
    }
}

if (isset($_GET['success'])) {
    $message = "ບັນທຶກການແກ້ໄຂຂໍ້ມູນສຳເລັດຮຽບຮ້ອຍແລ້ວ!";
}
require_once 'includes/header.php';

?>


<div class="w-full bg-white p-8 border-r border-gray-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
    
    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded text-sm font-medium">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($id > 0): ?>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM error_logs WHERE id = ?");
        $stmt->execute([$id]);
        $log = $stmt->fetch();

        if (!$log) {
            echo "<div class='text-red-500 font-bold'>Error: ບໍ່ພົບຂໍ້ມູນລາຍການນີ້ໃນລະບົບ.</div>";
            echo "</div><div class='w-1/4 bg-[#fbfbfb]'></div>";
            require_once 'includes/footer.php';
            exit;
        }
        ?>

        <div class="mb-6">
            <a href="edit.php" class="text-[#0000ee] text-sm hover:underline">&larr; ກັບຄືນໜ້າລາຍການທັງໝົດ</a>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">ແກ້ໄຂ ແລະ ອັບເດດວິທີການແກ້ໄຂ Error Log</h2>

        <div class="grid grid-cols-2 gap-8 mb-8">
            <div class="space-y-4 text-sm bg-gray-50 p-6 rounded-lg border border-gray-200">
                <div>
                    <span class="block text-gray-400 uppercase font-bold text-xs tracking-wider">Module:</span>
                    <span class="text-base font-semibold text-gray-800"><?= htmlspecialchars($log['module']) ?></span>
                </div>
                <div>
                    <span class="block text-gray-400 uppercase font-bold text-xs tracking-wider">Error Message:</span>
                    <span class="text-base font-semibold text-red-600"><?= htmlspecialchars($log['error_message']) ?></span>
                </div>
                <div>
                    <span class="block text-gray-400 uppercase font-bold text-xs tracking-wider">Reported By / Found Date:</span>
                    <span class="text-gray-700 font-medium"><?= htmlspecialchars($log['reported_by']) ?> (<?= htmlspecialchars($log['found_date']) ?>)</span>
                </div>
                <div>
                    <span class="block text-gray-400 uppercase font-bold text-xs tracking-wider">ສາເຫດ (Cause):</span>
                    <p class="text-gray-700 whitespace-pre-line mt-1 bg-white p-3 rounded border"><?= htmlspecialchars($log['cause']) ?></p>
                </div>
                <div>
                    <span class="block text-gray-400 uppercase font-bold text-xs tracking-wider">ອາການພົບບັນຫາ (Symptoms):</span>
                    <p class="text-gray-700 italic whitespace-pre-line mt-1 bg-white p-3 rounded border"><?= htmlspecialchars($log['symptoms']) ?></p>
                </div>
            </div>

            <div>
                <?php if (!empty($log['image_path'])): ?>
                    <span class="block text-gray-400 uppercase font-bold text-xs tracking-wider mb-2">Screenshot ປະກອບ:</span>
                    <div class="border border-gray-200 rounded p-2 bg-gray-50 shadow-sm">
                        <img src="<?= htmlspecialchars($log['image_path']) ?>" alt="Error Screenshot" class="w-full h-auto rounded">
                    </div>
                <?php else: ?>
                    <span class="text-gray-400 italic text-sm">ບໍ່ມີການແນບຮູບພາບ Screenshot.</span>
                <?php endif; ?>
            </div>
        </div>

        <form method="POST" action="edit.php" class="space-y-6 max-w-3xl border-t pt-6">
            <input type="hidden" name="id" value="<?= $log['id'] ?>">

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">ວິທີການແກ້ໄຂບັນຫາ <span class="text-red-500">*</span></label>
                <textarea name="solution" required rows="8" placeholder="ລະບຸວິທີການແກ້ໄຂບັນຫາເປັນຂັ້ນຕອນ 1. 2. 3..." class="w-full border border-gray-300 rounded p-3 text-sm focus:ring-1 focus:ring-[#2eb85c] outline-none"><?= htmlspecialchars($log['solution']) ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-800 mb-2">ລິ້ງວິດີໂອປະກອບ</label>
                <input type="url" name="video_link" value="<?= htmlspecialchars($log['video_link']) ?>" placeholder="https://example.com/video" class="w-full border border-gray-300 rounded p-3 text-sm focus:ring-1 focus:ring-[#2eb85c] outline-none">
            </div>

            <div class="w-1/3">
                <label class="block text-sm font-bold text-gray-800 mb-2">ສະຖານະລະບົບ</label>
                <select name="status" class="w-full border border-gray-300 rounded p-3 text-sm focus:ring-1 focus:ring-[#2eb85c] outline-none bg-white font-medium">
                    <option value="Pending" <?= $log['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Fixed" <?= $log['status'] === 'Fixed' ? 'selected' : '' ?>>Fixed</option>
                </select>
            </div>

            <div class="pt-4 flex gap-4">
                <button type="submit" name="update_log" class="bg-[#2eb85c] hover:bg-green-600 text-white font-semibold py-2.5 px-8 rounded shadow-sm text-sm transition">ບັນທຶກຂໍ້ມູນ</button>
                <a href="edit.php" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2.5 px-6 rounded text-sm transition">ຍົກເລີກ</a>
            </div>
        </form>

    <?php else: ?>
        <?php
        $stmt = $pdo->query("SELECT id, module, error_message, status, reported_by, created_at FROM error_logs ORDER BY created_at DESC");
        $all_logs = $stmt->fetchAll();
        ?>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl text-gray-800 font-normal">ການຈັດການຂໍ້ມູນ Error Logs ທັງໝົດ</h2>
            <span class="text-sm text-gray-500 font-medium">ທັງໝົດ: <?= count($all_logs) ?> ລາຍການ</span>
        </div>
        <div class="w-full h-px bg-[#2eb85c] mb-6"></div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-200 text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200">
                        <th class="p-4 border-r">ID</th>
                        <th class="p-4 border-r">ໂມດູນ</th>
                        <th class="p-4 border-r">ຂໍ້ຄວາມ Error (Error Message)</th>
                        <th class="p-4 border-r text-center">ສະຖານະ</th>
                        <th class="p-4 border-r">ຜູ້ລາຍງານ</th>
                        <th class="p-4 text-center">ຈັດການ</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    <?php if (count($all_logs) > 0): ?>
                        <?php foreach ($all_logs as $row): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-r font-semibold text-gray-500 text-center"><?= $row['id'] ?></td>
                                <td class="p-4 border-r font-medium"><?= htmlspecialchars($row['module']) ?></td>
                                <td class="p-4 border-r text-gray-900 max-w-xs truncate"><?= htmlspecialchars($row['error_message']) ?></td>
                                <td class="p-4 border-r text-center">
                                    <span class="px-2 py-1 rounded-full text-[11px] font-bold <?= $row['status'] === 'Fixed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td class="p-4 border-r"><?= htmlspecialchars($row['reported_by']) ?></td>
                                <td class="p-4 text-center">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="inline-flex items-center gap-1 bg-blue-50 text-blue-600 hover:bg-blue-100 font-semibold py-1 px-3 rounded text-xs border border-blue-200 transition">
                                        <i class="fa-solid fa-pen-to-square"></i> ແກ້ໄຂຄີຂໍ້ມູນ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400 italic">ບໍ່ມີຂໍ້ມູນ Error Log ໃນລະບົບ.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<div class="w-1/4 bg-[#fbfbfb]"></div>

<?php require_once 'includes/footer.php'; ?>