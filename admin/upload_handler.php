<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManager;

// Ο TinyMCE στέλνει το αρχείο με το όνομα 'file'
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];

    $uploadPath = PROJECT_ROOT . 'assets/uploads/blog/';
    $uploadedName = $file['name'] ?? '';
    $extension = strtolower(pathinfo($uploadedName, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($extension, $allowed, true)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Μη επιτρεπτός τύπος αρχείου.']);
        exit();
    }

    // ensure upload folder exists
    if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true)) {
        header('HTTP/1.1 500 Server Error');
        echo json_encode(['error' => 'Αδύνατο να δημιουργηθεί ο φάκελος αποθήκευσης.']);
        exit();
    }

    // ασφαλές όνομα αρχείου
    $filename = 'content_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;

    // pick image driver (gd preferred)
    if (extension_loaded('gd')) {
        $manager = ImageManager::gd();
    } elseif (extension_loaded('imagick')) {
        $manager = ImageManager::imagick();
    } else {
        header('HTTP/1.1 500 Server Error');
        echo json_encode(['error' => 'Δεν βρέθηκε υποστηριζόμενος image driver (gd ή imagick).']);
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
        $img->save($uploadPath . $filename, 80);

        header('Content-Type: application/json');
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $location = $protocol . rtrim($host, '/') . rtrim($BASE_URL, '/') . '/assets/uploads/blog/' . $filename;
        echo json_encode(['location' => $location]);
    } catch (Exception $e) {
        header('HTTP/1.1 500 Server Error');
        echo json_encode(['error' => 'Σφάλμα Upload: ' . $e->getMessage()]);
    }
}
