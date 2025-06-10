<?php
class database{
    var $host = "localhost";
    var $username = "root";
    var $password = "";
    var $database = "sekolah";

    function __construct(){
        $this-> koneksi = mysqli_connect($this->host, $this->username, $this->password);
        $cekdb = mysqli_select_db($this->koneksi, $this->database);
    }

    function data_siswa(){
        $data = mysqli_query($this->koneksi, "SELECT 
            s.id_siswa, 
            CASE 
                WHEN s.jeniskelamin = 'L' THEN 'Laki-Laki' 
                WHEN s.jeniskelamin = 'P' THEN 'Perempuan' 
                ELSE 'Tidak Diketahui' 
            END AS 
            jeniskelamin, 
            s.nisn, 
            s.nama, 
            j.namajurusan AS kodejurusan, 
            s.kelas, 
            s.alamat, 
            a.namaagama AS agama, 
            s.nohp 
        FROM siswa s
        JOIN jurusan j ON s.kodejurusan = j.kodejurusan
        JOIN agama a ON s.agama = a.idagama");
        $hasil = [];
        while ($row = mysqli_fetch_array($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    function tampil_data_siswa(){
        $data = mysqli_query($this->koneksi, "SELECT * FROM siswa");
        $hasil = [];
        while ($row = mysqli_fetch_array($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    function tampil_data_jurusan(){
        $data = mysqli_query($this->koneksi, "SELECT * FROM jurusan");
        $hasil = [];
        while ($row = mysqli_fetch_array($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }
    
    function tampil_data_agama(){
        $data = mysqli_query($this->koneksi, "select * from agama");
        $hasil = [];
        while ($row = mysqli_fetch_array($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }
    
    function tampil_data_user(){
        $data = mysqli_query($this->koneksi, "select * from users");
        $hasil = [];
        while ($row = mysqli_fetch_array($data)) {
            $hasil[] = $row;
        }
        return $hasil;
    }

    function tambah_siswa(
    $nisn, $nama, $jeniskelamin, $kodejurusan, $kelas, $alamat, $agama, $nohp){
        mysqli_query($this->koneksi, "INSERT INTO siswa (nisn, nama, jeniskelamin, kodejurusan, kelas, alamat, agama, nohp) VALUES (
        '$nisn', '$nama', '$jeniskelamin', '$kodejurusan', '$kelas', '$alamat', '$agama', '$nohp')");
    }

    function tambah_jurusan(
        $namajurusan){
        mysqli_query($this->koneksi, "INSERT INTO jurusan (namajurusan) VALUES (
        '$namajurusan')");
    }
    
    function tambah_agama(
        $namaagama){
        mysqli_query($this->koneksi, "INSERT INTO agama (namaagama) VALUES (
        '$namaagama')");
    }

    function tambah_user(
    $nama, $username, $password, $role){
        mysqli_query($this->koneksi, "INSERT INTO users (nama, username, password, role) VALUES (
        '$nama', '$username', '$password', '$role')");
    }

    // Hitung total siswa
    function total_siswa() {
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total FROM siswa");
        $hasil = mysqli_fetch_assoc($data);
        return $hasil['total'];
    }

    // Hitung total agama
    function total_agama() {
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total FROM agama");
        $hasil = mysqli_fetch_assoc($data);
        return $hasil['total'];
    }

    // Hitung total jurusan
    function total_jurusan() {
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total FROM jurusan");
        $hasil = mysqli_fetch_assoc($data);
        return $hasil['total'];
    }
    
    function total_user() {
        $data = mysqli_query($this->koneksi, "SELECT COUNT(*) as total FROM users");
        $hasil = mysqli_fetch_assoc($data);
        return $hasil['total'];
    }

    function tampil_data_jurusan_by_id($kodejurusan) {
        $stmt = $this->koneksi->prepare("SELECT * FROM jurusan WHERE kodejurusan = ?");
        $stmt->bind_param("s", $kodejurusan);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    function update_data_jurusan($kodejurusan, $namajurusan) {
        $stmt = $this->koneksi->prepare("UPDATE jurusan SET namajurusan = ? WHERE kodejurusan = ?");
        $stmt->bind_param("ss", $namajurusan, $kodejurusan);
        return $stmt->execute();
    }

    function hapus_data_jurusan($kodejurusan) {
        $stmt = $this->koneksi->prepare("DELETE FROM jurusan WHERE kodejurusan = ?");
        $stmt->bind_param("s", $kodejurusan);
        return $stmt->execute();
    }

    function tampil_data_agama_by_id($idagama) {
        $stmt = $this->koneksi->prepare("SELECT * FROM agama WHERE idagama = ?");
        $stmt->bind_param("s", $idagama);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    function update_data_agama($idagama, $namaagama) {
        $stmt = $this->koneksi->prepare("UPDATE agama SET namaagama = ? WHERE idagama = ?");
        $stmt->bind_param("ss", $namaagama, $idagama);
        return $stmt->execute();
    }

    function hapus_data_agama($idagama) {
        $stmt = $this->koneksi->prepare("DELETE FROM agama WHERE idagama = ?");
        $stmt->bind_param("s", $idagama);
        return $stmt->execute();
    }

    function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($this->koneksi, $query);
        
        if(mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            if($password == $user['password']) {
                return $user;
            }
        }
        
        return false;
    }

    function tampil_data_siswa_by_nisn($nisn) {
        $stmt = $this->koneksi->prepare("SELECT * FROM siswa WHERE nisn = ?");
        $stmt->bind_param("s", $nisn);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    function update_data_siswa($nisn, $nama, $jeniskelamin, $nohp, $kodejurusan, $kelas, $agama, $alamat) {
        $stmt = $this->koneksi->prepare("UPDATE siswa SET nama = ?, jeniskelamin = ?, nohp = ?, kodejurusan = ?, kelas = ?, agama = ?, alamat = ? WHERE nisn = ?");
        // Perhatikan urutan 's' (string) sesuai jumlah kolom
        $stmt->bind_param("ssssssss", $nama, $jeniskelamin, $nohp, $kodejurusan, $kelas, $agama, $alamat, $nisn);
        return $stmt->execute();
    }

    function hapus_data_siswa($nisn) {
        $stmt = $this->koneksi->prepare("DELETE FROM siswa WHERE nisn = ?");
        $stmt->bind_param("s", $nisn);
        return $stmt->execute();
    }

    function tampil_data_user_by_id($id) {
        $stmt = $this->koneksi->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    function update_data_user($id, $nama, $username, $password, $role) {
        $stmt = $this->koneksi->prepare("UPDATE users SET nama = ?, username = ?, password = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssss", $nama, $username, $password, $role, $id);
        return $stmt->execute();
    }

    function hapus_data_user($id) {
        $stmt = $this->koneksi->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    /**
     * @param int $userId ID pengguna.
     * @return string|null Path foto atau null jika tidak ada.
     */
    function get_profile_picture($userId) {
        $userId = (int)$userId; // Pastikan integer
        $query = mysqli_query($this->koneksi, "SELECT foto FROM users WHERE id = $userId");
        if ($query && mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_array($query);
            return $data['foto'];
        }
        return null; // Atau return path ke gambar default
    }

    /**
     * @param int $userId ID pengguna.
     * @param string $filePath Path foto baru.
     * @return bool True jika berhasil, false jika gagal.
     */
    function update_profile_picture($userId, $filePath) {
        $userId = (int)$userId; // Pastikan integer
        // Gunakan prepared statement untuk keamanan
        $stmt = $this->koneksi->prepare("UPDATE users SET foto = ? WHERE id = ?");
        if ($stmt === false) {
             error_log("Prepare failed: " . $this->koneksi->error);
             return false;
        }
        $stmt->bind_param("si", $filePath, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    function get_gender_distribution() {
        $query = "SELECT 
            CASE 
                WHEN jeniskelamin = 'L' THEN 'Laki-Laki' 
                WHEN jeniskelamin = 'P' THEN 'Perempuan' 
                ELSE 'Tidak Diketahui' 
            END AS gender, 
            COUNT(*) as total 
            FROM siswa 
            GROUP BY jeniskelamin";
        
        $result = mysqli_query($this->koneksi, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
}
?>