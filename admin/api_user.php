<?php
header('Content-Type: application/json');

// Pastikan tidak ada output sebelum ini
if (ob_get_length()) ob_clean();

include "koneksi.php";
$db = new database();

$response = array('status' => 'error', 'message' => 'Aksi tidak valid.');

$aksi = isset($_REQUEST['aksi']) ? $_REQUEST['aksi'] : '';

switch ($aksi) {
    case 'get_single':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $data_user = $db->tampil_data_user_by_id($id); 
            
            if ($data_user) {
                $response = array(
                    'status' => 'success', 
                    'user' => $data_user,
                );
            } else {
                $response['message'] = 'Data user tidak ditemukan.';
            }
        } else {
            $response['message'] = 'ID tidak disertakan.';
        }
        break;

    case 'update':
        if (isset($_POST['id'])) {
            session_start();
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $role = $_POST['role'];

            $result = $db->update_data_user($id, $nama, $username, $password, $role);
            if ($result) {
                if ($id == $_SESSION['id']) {
                    $_SESSION['nama'] = $nama;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;
                    
                    if(isset($_SESSION['foto'])) {
                        $_SESSION['foto'] = $db->get_profile_picture($id);
                    }
                }
                
                $response = array(
                    'status' => 'success', 
                    'message' => 'Data user berhasil diperbarui.',
                    'updated_user' => array(
                        'id' => $id,
                        'nama' => $nama,
                        'username' => $username,
                        'role' => $role
                    ),
                    'is_current_user' => ($id == $_SESSION['id'])
                );
            } else {
                $response['message'] = 'Gagal memperbarui data user di database.';
            }
        }
        break;

    case 'hapus':
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $result = $db->hapus_data_user($id);
            if ($result) {
                $response = array('status' => 'success', 'message' => 'Data user berhasil dihapus.');
            } else {
                $response['message'] = 'Gagal menghapus data user.';
            }
        } else {
            $response['message'] = 'Id untuk hapus tidak disertakan.';
        }
        break;
}

// Pastikan tidak ada output setelah ini
die(json_encode($response));
?>