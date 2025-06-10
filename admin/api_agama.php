<?php
// Set header sebagai JSON
header('Content-Type: application/json');

include "koneksi.php";
$db = new database();

// Siapkan array untuk respon
$response = array('status' => 'error', 'message' => 'Aksi tidak valid.');

// Cek aksi yang diminta (bisa dari GET atau POST)
$aksi = isset($_REQUEST['aksi']) ? $_REQUEST['aksi'] : '';

switch ($aksi) {
    // KASUS: MENGAMBIL SATU DATA UNTUK MODAL EDIT
    case 'get_single':
        if (isset($_GET['idagama'])) {
            $idagama = $_GET['idagama'];
            $data = $db->tampil_data_agama_by_id($idagama); // Panggil fungsi baru di koneksi.php
            if ($data) {
                $response = array('status' => 'success', 'data' => $data);
            } else {
                $response['message'] = 'Data tidak ditemukan.';
            }
        } else {
            $response['message'] = 'ID Agama tidak disertakan.';
        }
        break;

    // KASUS: MEMPERBARUI DATA DARI MODAL EDIT
    case 'update':
        if (isset($_POST['idagama']) && isset($_POST['namaagama'])) {
            $idagama = $_POST['idagama'];
            $namaagama = $_POST['namaagama'];
            
            // Panggil fungsi update baru di koneksi.php
            $result = $db->update_data_agama($idagama, $namaagama);
            if ($result) {
                $response = array('status' => 'success', 'message' => 'Data berhasil diperbarui.');
            } else {
                $response['message'] = 'Gagal memperbarui data.';
            }
        } else {
            $response['message'] = 'Data tidak lengkap.';
        }
        break;

    // KASUS: MENGHAPUS DATA
    case 'hapus':
        if (isset($_POST['idagama'])) {
            $idagama = $_POST['idagama'];
            
            // Panggil fungsi hapus baru di koneksi.php
            $result = $db->hapus_data_agama($idagama);
            if ($result) {
                $response = array('status' => 'success', 'message' => 'Data berhasil dihapus.');
            } else {
                $response['message'] = 'Gagal menghapus data.';
            }
        } else {
            $response['message'] = 'ID Agama tidak disertakan.';
        }
        break;
}

// Kembalikan respon dalam format JSON
echo json_encode($response);
?>