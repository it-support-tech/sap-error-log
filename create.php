<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_log'])) {
    $imagePath = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $originalName = basename($_FILES['image']['name']);
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        $cleanName = preg_replace("/[^a-zA-Z0-9.\-_]/", "_", pathinfo($originalName, PATHINFO_FILENAME));
        $fileName = time() . '_' . $cleanName . '.' . $fileExtension;
        
        $imagePath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    $sql = "INSERT INTO error_logs (module, found_date, cause, symptoms, error_message, solution, video_link, image_path, status, reported_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['module'], $_POST['found_date'], $_POST['cause'], $_POST['symptoms'], 
        $_POST['error_message'], $_POST['solution'], $_POST['video_link'], $imagePath, 
        $_POST['status'], $_SESSION['username']
    ]);
    $success = true;
}
?>

<div class="w-3/4 bg-white p-8 pr-12 border-r border-gray-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
    <div class="mb-6">
        <a href="index.php" class="text-[#0000ee] text-sm hover:underline">Solution home</a> / <span class="text-gray-500 text-sm">New Error Log</span>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-3xl text-gray-800 font-normal">Add SAP Error Log</h2>
        <?php if ($success): ?>
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded text-sm"><i class="fa-solid fa-check"></i> Saved successfully!</span>
        <?php endif; ?>
    </div>
    <div class="w-full h-px bg-[#2eb85c] mb-8"></div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Module</label>
                <select name="module" required class="w-full border border-gray-300 p-2 rounded outline-none focus:ring-1 focus:ring-[#2eb85c]">
                    <option value="">Select Module...</option>
                    <option value="Administration">ຈັດການລະບົບ (Administration)</option>
                    <option value="Financials">ການເງິນ ການບັນຊີ (Financials)</option>
                    <option value="Sales">ການຂາຍ (Sales - A/R)</option>
                    <option value="Purchasing">ຈັດຊື້ (Purchasing - A/P)</option>
                    <option value="Business Partners">ຄູ່ຮ່ວມທຸລະກິດ (Business Partners)</option>
                    <option value="Banking">Janທະນາຄານ (Banking)</option>
                    <option value="Inventory">ຈັດການສາງ (Inventory)</option>
                    <option value="Fixed Asset">ຊັບສິນ (Fixed Asset)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">ວັນທີພົບບັນຫາ</label>
                <input type="date" name="found_date" required class="w-full border border-gray-300 p-2 rounded outline-none focus:ring-1 focus:ring-[#2eb85c]">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">ສາເຫດທີ່ພົບບັນຫາ</label>
            <textarea name="cause" rows="2" placeholder="ສາເຫດທີ່ເຮັດໃຫ້ເກີດ error ຕົວຢ່າງ...&#10ກຳລັງເຮັດໜ້າວຽກໃດ&#10ຂັ້ນຕອນໃດ" class="w-full border border-gray-300 p-2 rounded outline-none focus:ring-1 focus:ring-[#2eb85c]"></textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">ອາການ</label>
            <textarea name="symptoms" rows="2" placeholder="ອະທິບາຍອາການທີ່ເກີດຂື້ນເຊັ່ນວ່າ...&#10ບໍ່ສາມາດບັນທຶກເອກະສານໄດ້&#10ສະກຸນເງິນບໍ່ຂື້ນ" class="w-full border border-gray-300 p-2 rounded outline-none focus:ring-1 focus:ring-[#2eb85c]"></textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Error Message</label>
            <input type="text" name="error_message" required placeholder="ກ໋ອບປີ້ Error message ມາວາງໃສ່" class="w-full border border-gray-300 p-2 rounded outline-none focus:ring-1 focus:ring-[#2eb85c]">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">ວິທີແກ້ໄຂ</label>
            <textarea name="solution" rows="4" class="w-full border border-gray-300 p-2 rounded outline-none focus:ring-1 focus:ring-[#2eb85c]"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">ວິດີໂອ</label>
                <input type="url" name="video_link" placeholder="https://" class="w-full border border-gray-300 p-2 rounded outline-none focus:ring-1 focus:ring-[#2eb85c]">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">ຮູບພາບທີ່ແຄັບຈໍ</label>
                <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 p-1.5 rounded outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2"> ສະຖານະ</label>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="Pending" checked class="accent-[#2eb85c] w-4 h-4">
                    <span>Pending</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="Fixed" class="accent-[#2eb85c] w-4 h-4">
                    <span>Fixed</span>
                </label>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100">
            <button type="submit" name="add_log" class="bg-[#2eb85c] hover:bg-green-600 text-white font-semibold py-2 px-8 rounded shadow">
                Save Error Log
            </button>
        </div>
    </form>
</div>

<div class="w-1/4 bg-[#fbfbfb]"></div>

<?php require_once 'includes/footer.php'; ?>