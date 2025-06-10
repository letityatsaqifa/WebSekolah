<?php
header('Content-Type: application/json');

include "koneksi.php";
$db = new database();

$response = array('status' => 'error', 'message' => 'Aksi tidak valid.');

$aksi = isset($_REQUEST['aksi']) ? $_REQUEST['aksi'] : '';

switch ($aksi) {
    case 'get_single':
        if (isset($_GET['nisn'])) {
            $nisn = $_GET['nisn'];
            $data_siswa = $db->tampil_data_siswa_by_nisn($nisn); 
            $data_jurusan = $db->tampil_data_jurusan();
            
            if ($data_siswa) {
                $response = array(
                    'status' => 'success', 
                    'siswa' => $data_siswa,
                    'jurusan' => $data_jurusan
                );
            } else {
                $response['message'] = 'Data siswa tidak ditemukan.';
            }
        } else {
            $response['message'] = 'NISN tidak disertakan.';
        }
        break;

    case 'update':
        if (isset($_POST['nisn'])) {
            $nisn = $_POST['nisn'];
            $nama = $_POST['nama'];
            $jeniskelamin = $_POST['jeniskelamin'];
            $nohp = $_POST['nohp'];
            $kodejurusan = $_POST['kodejurusan'];
            $kelas = $_POST['kelas'];
            $agama = $_POST['agama'];
            $alamat = $_POST['alamat'];

            $result = $db->update_data_siswa($nisn, $nama, $jeniskelamin, $nohp, $kodejurusan, $kelas, $agama, $alamat);
            if ($result) {
                $response = array('status' => 'success', 'message' => 'Data siswa berhasil diperbarui.');
            } else {
                $response['message'] = 'Gagal memperbarui data siswa di database.';
            }
        } else {
            $response['message'] = 'Data untuk update tidak lengkap.';
        }
        break;

    case 'hapus':
        if (isset($_POST['nisn'])) {
            $nisn = $_POST['nisn'];
            $result = $db->hapus_data_siswa($nisn);
            if ($result) {
                $response = array('status' => 'success', 'message' => 'Data siswa berhasil dihapus.');
            } else {
                $response['message'] = 'Gagal menghapus data siswa.';
            }
        } else {
            $response['message'] = 'NISN untuk hapus tidak disertakan.';
        }
        break;
}

echo json_encode($response);
exit();
?>