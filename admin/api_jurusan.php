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
        if (isset($_GET['kodejurusan'])) {
            $kodejurusan = $_GET['kodejurusan'];
            $data = $db->tampil_data_jurusan_by_id($kodejurusan); // Panggil fungsi baru di koneksi.php
            if ($data) {
                $response = array('status' => 'success', 'data' => $data);
            } else {
                $response['message'] = 'Data tidak ditemukan.';
            }
        } else {
            $response['message'] = 'ID Jurusan tidak disertakan.';
        }
        break;

    // KASUS: MEMPERBARUI DATA DARI MODAL EDIT
    case 'update':
        if (isset($_POST['kodejurusan']) && isset($_POST['namajurusan'])) {
            $kodejurusan = $_POST['kodejurusan'];
            $namajurusan = $_POST['namajurusan'];
            
            // Panggil fungsi update baru di koneksi.php
            $result = $db->update_data_jurusan($kodejurusan, $namajurusan);
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
        if (isset($_POST['kodejurusan'])) {
            $kodejurusan = $_POST['kodejurusan'];
            
            // Panggil fungsi hapus baru di koneksi.php
            $result = $db->hapus_data_jurusan($kodejurusan);
            if ($result) {
                $response = array('status' => 'success', 'message' => 'Data berhasil dihapus.');
            } else {
                $response['message'] = 'Gagal menghapus data.';
            }
        } else {
            $response['message'] = 'ID Jurusan tidak disertakan.';
        }
        break;
}

// Kembalikan respon dalam format JSON
echo json_encode($response);
?>