<?php
// admin/tinymce_upload.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once PROJECT_ROOT . 'access_control.php';
restrict_access(['admin']);

use Intervention\Image\ImageManager;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file'])) {
    echo json_encode(['error' => 'No file uploaded']);
    exit();
}

$uploadPath = PROJECT_ROOT . 'assets/uploads/blog/';
// ensure upload dir exists
if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true)) {
    echo json_encode(['error' => 'Upload dir missing']);
    exit();
}

$file = $_FILES['file'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($ext, $allowed, true)) {
    echo json_encode(['error' => 'Invalid file type']);
    exit();
}

// safe filename
$name = 'content_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$dest = $uploadPath . $name;

// pick image driver
if (extension_loaded('gd')) {
    $manager = ImageManager::gd();
} elseif (extension_loaded('imagick')) {
    $manager = ImageManager::imagick();
} else {
    echo json_encode(['error' => 'No supported image driver (gd or imagick)']);
    exit();
}

try {
    $img = $manager->read($file['tmp_name']);
    // resize if wider than 1000px
    if ($img->width() > 1000) {
        $img->resize(1000, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    }
    $img->save($dest, 80);

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $url = $protocol . rtrim($host, '/') . rtrim($BASE_URL, '/') . '/assets/uploads/blog/' . $name;
    echo json_encode(['location' => $url]);
    exit();
} catch (Exception $e) {
    echo json_encode(['error' => 'Upload error: ' . $e->getMessage()]);
    exit();
}
