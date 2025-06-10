<?php
session_start();
header('Content-Type: application/json'); // Penting untuk response AJAX

// Cek sesi sekali lagi, pastikan ada 'id'
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Sesi tidak valid.']);
    exit;
}

include "koneksi.php";
$db = new database();

$userId = $_SESSION['id']; // Ambil ID dari session
$response = ['success' => false, 'message' => 'Tidak ada file yang diunggah atau error.'];

// Pastikan menggunakan nama 'profile_picture_file' seperti di FormData JS
if (isset($_FILES['profile_picture_file']) && $_FILES['profile_picture_file']['error'] == 0) {
    $file = $_FILES['profile_picture_file'];

    // --- Validasi ---
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024;

    if (!in_array($file['type'], $allowedTypes)) {
        $response['message'] = 'Tipe file tidak diizinkan (Hanya JPG, PNG, GIF).';
        echo json_encode($response);
        exit;
    }

    if ($file['size'] > $maxSize) {
        $response['message'] = 'Ukuran file terlalu besar (Maksimal 2 MB).';
        echo json_encode($response);
        exit;
    }

    // --- Proses Upload ---
    $uploadDir = '../uploads/profile_pictures/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            $response['message'] = 'Gagal membuat direktori upload.';
            echo json_encode($response);
            exit;
        }
    }
     if (!is_writable($uploadDir)) {
        $response['message'] = 'Direktori upload tidak bisa ditulisi.';
        echo json_encode($response);
        exit;
    }


    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $newFileName = 'user_' . $userId . '_' . time() . '.' . $extension;
    $uploadPath = $uploadDir . $newFileName;

    // Ambil path gambar lama untuk dihapus
    $oldPicturePath = $db->get_profile_picture($userId);
    $defaultPicture = 'dist/assets/img/avatar.png'; // Pastikan sama

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // --- Update Database ---
        if ($db->update_profile_picture($userId, $uploadPath)) {
            // Hapus gambar lama jika ada dan bukan gambar default
            if ($oldPicturePath && $oldPicturePath !== $defaultPicture && file_exists($oldPicturePath)) {
                 @unlink($oldPicturePath);
            }

            $_SESSION['foto'] = $uploadPath; // Update session juga

            $response['success'] = true;
            $response['message'] = 'Foto profil berhasil diunggah.';
            $response['new_path'] = $uploadPath;
        } else {
            @unlink($uploadPath); // Hapus jika gagal update DB
            $response['message'] = 'Gagal memperbarui database.';
        }
    } else {
        $response['message'] = 'Gagal memindahkan file. Cek izin folder.';
    }
} else {
     $errorCode = $_FILES['profile_picture_file']['error'] ?? 'Tidak diketahui';
     $response['message'] = 'Error upload PHP: ' . $errorCode;
}

echo json_encode($response);
exit;
?>