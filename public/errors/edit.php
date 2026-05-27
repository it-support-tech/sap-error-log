<?php
require_once dirname(dirname(__DIR__)) . '/src/config/autoload.php';

use App\Middleware\Auth;
use App\Models\Module;
use App\Models\ErrorLog;

Auth::requireAuth();
$employee = Auth::getEmployee();

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    header('Location: /index.php');
    exit;
}

$errorModel = new ErrorLog();
$errorLog = $errorModel->findById($id);  

if (!$errorLog) {
    header('Location: /index.php');
    exit;
}

$moduleModel = new Module();
$modules = $moduleModel->getAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleId = (int)($_POST['module_id'] ?? 0);
    $occurredAt = trim($_POST['occurred_at'] ?? '');
    $errorMessage = trim($_POST['error_message'] ?? '');
    $symptom = trim($_POST['symptom'] ?? '');
    $cause = trim($_POST['cause'] ?? '');
    $solution = trim($_POST['solution'] ?? '');
    $videoUrl = trim($_POST['video_url'] ?? '');
    $status = $_POST['status'] === 'resolved' ? 'resolved' : 'pending';

    if ($moduleId === 0) $errors['module_id'] = 'ກະລຸນາເລືອກ Module';
    if ($occurredAt === '') $errors['occurred_at'] = 'ກະລຸນາໃສ່ວັນທີ';
    if ($errorMessage === '') $errors['error_message'] = 'ກະລຸນາໃສ່ Error Message';
    if ($symptom === '') $errors['symptom'] = 'ກະລຸນາອະທິບາຍອາການ';

    $imagePath = $errorLog['image_path'];  
    if (!empty($_FILES['screenshot']['name'])) {
        $file = $_FILES['screenshot'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed)) {
            $errors['screenshot'] = 'ຮູບຕ້ອງເປັນ JPG, PNG, GIF ຫຼື WebP';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $errors['screenshot'] = 'ຂະໜາດຮູບໃຫຍ່ເກີນໄປ (ສູງສຸດ 5MB)';
        } else {
            $filename = uniqid('err_', true) . '.' . $ext;
            $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/screenshots/';
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $imagePath = $filename;  
            }
        }
    }

    if (empty($errors)) {
        $errorModel->update($id, [
            'module_id' => $moduleId,
            'occurred_at' => $occurredAt,
            'error_message' => $errorMessage,
            'symptom' => $symptom,
            'cause' => $cause ?: null,
            'solution' => $solution ?: null,
            'video_url' => $videoUrl ?: null,
            'image_path' => $imagePath,
            'status' => $status,
        ]);

        header("Location: /errors/module.php?id={$moduleId}&updated=1");
        exit;
    }
}

$pageTitle = 'ແກ້ໄຂ Error — SAP B1 Error Log';
include dirname(dirname(__DIR__)) . '/src/views/components/header.php';

$inputClass = "w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all shadow-sm";
?>

<main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-gray-900">
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6 animate-fade-in-up">
        <a href="/index.php" class="hover:text-gray-700 transition-colors">Dashboard</a>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-600 font-medium">ແກ້ໄຂຂໍ້ມູນ Error</span>
    </div>

    <div class="mb-7 animate-fade-in-up">
        <h1 class="text-xl font-bold text-gray-900">ແກ້ໄຂລາຍງານ Error</h1>
        <p class="text-sm text-gray-500 mt-0.5">ແກ້ໄຂໂດຍ: <span class="text-indigo-600 font-medium"><?= htmlspecialchars($employee['name']) ?></span></p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="" enctype="multipart/form-data" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Module <span class="text-rose-500">*</span></label>
                    <select name="module_id" class="<?= $inputClass ?>">
                        <?php foreach ($modules as $mod): ?>
                        <option value="<?= $mod['id'] ?>" <?= ((int)$errorLog['module_id'] === (int)$mod['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mod['name_lo']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ວັນທີເກີດບັນຫາ <span class="text-rose-500">*</span></label>
                    <input type="date" name="occurred_at" class="<?= $inputClass ?>" value="<?= htmlspecialchars($errorLog['occurred_at']) ?>">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Error Message <span class="text-rose-500">*</span></label>
                <input type="text" name="error_message" class="<?= $inputClass ?> font-mono" value="<?= htmlspecialchars($errorLog['error_message']) ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ອາການທີ່ພົບ <span class="text-rose-500">*</span></label>
                <textarea name="symptom" rows="3" class="<?= $inputClass ?>"><?= htmlspecialchars($errorLog['symptom']) ?></textarea>
            </div>

            <div class="bg-indigo-50/40 p-4 rounded-xl border border-indigo-100/80 space-y-4">
                <h3 class="text-xs font-bold text-indigo-700 uppercase tracking-wider">ຂໍ້ມູນສາເຫດ & ວິທີແກ້ໄຂ (ອັບເດດຕາມຫຼັງໄດ້)</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ສາເຫດ (Cause)</label>
                    <textarea name="cause" rows="3" class="<?= $inputClass ?>" placeholder="ສາເຫດທີ່ເກີດບັນຫາ..."><?= htmlspecialchars($errorLog['cause'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ວິທີແກ້ໄຂ (Solution)</label>
                    <textarea name="solution" rows="4" class="<?= $inputClass ?>" placeholder="ໃສ່ຂັ້ນຕອນວິທີການແກ້ໄຂ..."><?= htmlspecialchars($errorLog['solution'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລິ້ງວິດີໂອອະທິບາຍ (ຖ້າມີ)</label>
                    <input type="url" name="video_url" class="<?= $inputClass ?>" placeholder="https://..." value="<?= htmlspecialchars($errorLog['video_url'] ?? '') ?>">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ສະຖານະລະບົບ</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="radio" name="status" value="pending" <?= $errorLog['status'] === 'pending' ? 'checked' : '' ?> class="w-4 h-4 accent-indigo-600">
                        <span class="text-amber-600 font-medium">⏳ ກຳລັງດຳເນີນການ (Pending)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="radio" name="status" value="resolved" <?= $errorLog['status'] === 'resolved' ? 'checked' : '' ?> class="w-4 h-4 accent-indigo-600">
                        <span class="text-emerald-600 font-medium">✅ ແກ້ໄຂແລ້ວ (Resolved)</span>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 flex items-center gap-3 justify-end">
                <a href="/errors/module.php?id=<?= $errorLog['module_id'] ?>" class="text-sm font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 px-4 py-2.5 rounded-xl transition-all">ຍົກເລີກ</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl shadow-sm transition-all">
                    บันທຶກການອັບເດດ
                </button>
            </div>
        </form>
    </div>
</main>

<?php include dirname(dirname(__DIR__)) . '/src/views/components/footer.php'; ?>