<?php
require_once __DIR__ . '/../../src/config/autoload.php';

use App\Middleware\Auth;
use App\Models\Module;
use App\Models\Employee;
use App\Models\ErrorLog;

Auth::requireAuth();
$employee = Auth::getEmployee();

$moduleModel = new Module();
$modules = $moduleModel->getAll();

$preselectedModule = (int)($_GET['module_id'] ?? 0);
$errors = [];
$success = false;

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
    if ($errorMessage === '') $errors['error_message'] = 'ກະລຸນາໃສ่ Error Message';
    if ($symptom === '') $errors['symptom'] = 'ກະລຸນາອະທິບາຍອາການ';

    $imagePath = null;
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
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $imagePath = $filename;
            }
        }
    }

    if (empty($errors)) {
        $empModel = new Employee();
        $emp = $empModel->findOrCreate($employee['name']);

        $errorModel = new ErrorLog();
        $created = $errorModel->create([
            'module_id' => $moduleId,
            'employee_id' => $emp['id'],
            'occurred_at' => $occurredAt,
            'error_message' => $errorMessage,
            'symptom' => $symptom,
            'cause' => $cause ?: null,
            'solution' => $solution ?: null,
            'video_url' => $videoUrl ?: null,
            'image_path' => $imagePath,
            'status' => $status,
        ]);

        header("Location: /errors/module.php?id={$moduleId}&added=1");
        exit;
    }
}

$pageTitle = 'ເພີ່ມ Error ໃໝ່ — SAP B1 Error Log';

include dirname(dirname(__DIR__)) . '/src/views/components/header.php';

$inputClass = "w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all shadow-sm";
?>

<main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-gray-900">

    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6 animate-fade-in-up">
        <a href="/index.php" class="hover:text-gray-700 transition-colors">Dashboard</a>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-600 font-medium">ເພີ່ມ Error ໃໝ່</span>
    </div>

    <div class="mb-7 animate-fade-in-up Associations-1">
        <h1 class="text-xl font-bold text-gray-900">ລາຍງານ Error ໃໝ່</h1>
        <p class="text-sm text-gray-500 mt-0.5">ລາຍງານໂດຍ: <span class="text-indigo-600 font-medium"><?= htmlspecialchars($employee['name']) ?></span></p>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="mb-5 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-sm flex items-start gap-2 animate-scale-in">
        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        <span>ກະລຸນາກວດຂໍ້ມູນໃຫ້ຄົບຖ້ວນ</span>
    </div>
    <?php endif; ?>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="" enctype="multipart/form-data" class="space-y-5 animate-fade-in-up Associations-2">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Module <span class="text-rose-500">*</span></label>
                    <select name="module_id" class="<?= $inputClass ?> <?= isset($errors['module_id']) ? 'border-rose-500 focus:ring-rose-500' : '' ?>">
                        <option value="">ເລືອກ Module...</option>
                        <?php foreach ($modules as $mod): ?>
                        <option value="<?= $mod['id'] ?>" <?= ((int)($_POST['module_id'] ?? $preselectedModule) === (int)$mod['id']) ? 'selected' : '' ?>>
                           <?= htmlspecialchars($mod['name_en']) ?> - <?= htmlspecialchars($mod['name_lo']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['module_id'])): ?>
                    <p class="text-xs text-rose-500 mt-1"><?= $errors['module_id'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ວັນທີທີ່ເຈີບັນຫາ <span class="text-rose-500">*</span></label>
                    <input type="date" name="occurred_at" class="<?= $inputClass ?> <?= isset($errors['occurred_at']) ? 'border-rose-500 focus:ring-rose-500' : '' ?>"
                           value="<?= htmlspecialchars($_POST['occurred_at'] ?? date('Y-m-d')) ?>">
                    <?php if (isset($errors['occurred_at'])): ?>
                    <p class="text-xs text-rose-500 mt-1"><?= $errors['occurred_at'] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Error Message <span class="text-rose-500">*</span></label>
                <input type="text" name="error_message" class="<?= $inputClass ?> font-mono <?= isset($errors['error_message']) ? 'border-rose-500 focus:ring-rose-500' : '' ?>"
                       placeholder="Error: SAP Business One Cannot add or update..."
                       value="<?= htmlspecialchars($_POST['error_message'] ?? '') ?>">
                <?php if (isset($errors['error_message'])): ?>
                <p class="text-xs text-rose-500 mt-1"><?= $errors['error_message'] ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ອາການ <span class="text-rose-500">*</span></label>
                <textarea name="symptom" rows="3" class="<?= $inputClass ?> <?= isset($errors['symptom']) ? 'border-rose-500 focus:ring-rose-500' : '' ?>"
                          placeholder="ອະທິບາຍອາການທີ່ເກີດຂຶ້ນ..."><?= htmlspecialchars($_POST['symptom'] ?? '') ?></textarea>
                <?php if (isset($errors['symptom'])): ?>
                <p class="text-xs text-rose-500 mt-1"><?= $errors['symptom'] ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ສາເຫດ</label>
                <textarea name="cause" rows="3" class="<?= $inputClass ?>" placeholder="ສາເຫດທີ່ເຮັດໃຫ້ເກີດ error..."><?= htmlspecialchars($_POST['cause'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ວິທີແກ້ໄຂ</label>
                <textarea name="solution" rows="4" class="<?= $inputClass ?>" placeholder="ຂັ້ນຕອນການແກ້ໄຂ..."><?= htmlspecialchars($_POST['solution'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ລິ້ງວິດີໂອ (ຖ້າມີ)</label>
                <input type="url" name="video_url" class="<?= $inputClass ?>"
                       placeholder="https://www.youtube.com/watch?v=..."
                       value="<?= htmlspecialchars($_POST['video_url'] ?? '') ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຮູບໜ້າ Error (ຖ້າມີ)</label>
                <div class="mt-1">
                    <label class="flex flex-col items-center justify-center w-full h-28 border border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-indigo-500 hover:bg-indigo-50/40 transition-all">
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                            <p class="text-xs text-gray-600 font-medium">ກົດເພື່ອອັບໂຫລດຮູບ</p>
                            <p class="text-xs text-gray-400">PNG, JPG, WebP ສູງສຸດ 5MB</p>
                        </div>
                        <input type="file" name="screenshot" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </label>
                    <div class="hidden mt-2">
                        <img id="imagePreview" src="" alt="Preview" class="max-h-40 rounded-xl border border-gray-200">
                    </div>
                </div>
                <?php if (isset($errors['screenshot'])): ?>
                <p class="text-xs text-rose-500 mt-1"><?= $errors['screenshot'] ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ສະຖານະ</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                        <input type="radio" name="status" value="pending" <?= (($_POST['status'] ?? 'pending') === 'pending') ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 accent-indigo-600">
                        <span>ກຳລັງດຳເນີນການ</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                        <input type="radio" name="status" value="resolved" <?= (($_POST['status'] ?? '') === 'resolved') ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 accent-indigo-600">
                        <span>ແກ້ໄຂແລ້ວ</span>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 mt-6"></div>

            <div class="flex items-center gap-3 justify-end">
                <a href="/index.php" class="text-sm font-medium text-gray-500 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 px-4 py-2.5 rounded-xl transition-all">ຍົກເລີກ</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    ບັນທຶກ Error
                </button>
            </div>
        </form>
    </div>
</main>

<?php include dirname(dirname(__DIR__)) . '/src/views/components/footer.php'; ?>