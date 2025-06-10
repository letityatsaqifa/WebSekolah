<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";
$db = new database();

$userId = $_SESSION['id'];
$profilePicturePath = $db->get_profile_picture($userId);

$defaultPicture = 'dist/assets/img/avatar.png';
$profilePicturePath = ($profilePicturePath && file_exists($profilePicturePath)) ? $profilePicturePath : $defaultPicture;
$_SESSION['foto'] = $profilePicturePath;
?>

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE 4 | Profile</title>
    <style>
        .profile-card {
            max-width: 800px;
            margin: 0 auto;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, #504B38 0%, #B9B28A 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s;
            margin-bottom: 15px;
        }
        .profile-picture:hover {
            transform: scale(1.05);
        }
        .profile-name {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0 5px;
        }
        .profile-role {
            font-size: 16px;
            opacity: 0.9;
        }
        .profile-body {
            padding: 30px;
            background-color: white;
        }
        .profile-info {
            margin-bottom: 25px;
        }
        .info-label {
            font-weight: bold;
            color: #504B38;
            display: block;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            padding: 10px 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid #B9B28A;
        }
        .upload-btn {
            display: block;
            width: 200px;
            margin: 20px auto 0;
            padding: 10px;
            background-color: #B9B28A;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .upload-btn:hover {
            background-color: #504B38;
        }
        .hidden-file-input {
            display: none;
        }
        .password-note {
            font-size: 14px;
            color: #777;
            margin-top: 5px;
            font-style: italic;
        }
    </style>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE 4 | Profile" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard" />
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard" />
    <!--end::Primary Meta Tags-->
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="dist/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body sticky-top">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block"><a href="index.php" class="nav-link">Home</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="<?php echo htmlspecialchars($profilePicturePath); ?>" class="user-image rounded-circle shadow" alt="User Image" id="userImageNav" />
                            <span class="d-none d-md-inline"><?php echo $_SESSION['nama']; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="<?php echo htmlspecialchars($profilePicturePath); ?>" class="rounded-circle shadow changeable" alt="User Image" id="userImageHeader" />
                                <input type="file" id="profilePictureInput" class="hidden-file-input" accept="image/jpeg, image/png, image/gif">
                                <p>
                                    <?php echo $_SESSION['nama']; ?>
                                    <small>Role: <?php echo $_SESSION['role']; ?></small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <a href="profile.php" class="btn btn-default btn-flat">Profile</a>
                                <a href="#" class="btn btn-default btn-flat float-end" onclick="confirmLogout()">Sign out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <!--end::Header-->
        <?php include "sidebar.php"; ?>
        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">Profile Pengguna</h3></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Profile</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="profile-card">
                                <div class="profile-header">
                                    <img src="<?php echo htmlspecialchars($profilePicturePath); ?>" class="profile-picture" id="profilePictureMain" alt="Foto Profil">
                                    <input type="file" id="profilePictureInputMain" class="hidden-file-input" accept="image/jpeg, image/png, image/gif">
                                    <div class="profile-name"><?php echo $_SESSION['nama']; ?></div>
                                    <small class="profile-role">Role: <?php echo $_SESSION['role']; ?></small>
                                </div>
                                <div class="profile-body">
                                    <div class="profile-info">
                                        <span class="info-label">Nama Lengkap</span>
                                        <div class="info-value"><?php echo $_SESSION['nama']; ?></div>
                                    </div>
                                    <div class="profile-info">
                                        <span class="info-label">Username</span>
                                        <div class="info-value"><?php echo $_SESSION['username']; ?></div>
                                    </div>
                                    <div class="profile-info">
                                        <span class="info-label">Password</span>
                                        <div class="info-value">
                                            ********
                                        </div>
                                    </div>
                                    <div class="profile-info">
                                        <span class="info-label">Role</span>
                                        <div class="info-value"><?php echo $_SESSION['role']; ?></div>
                                    </div>
                                    <button class="upload-btn" id="uploadBtn">Ubah Foto Profil</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">Anything you want</div>
            <strong>Copyright &copy; 2014-2024 <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.</strong>
            All rights reserved.
        </footer>
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="dist/js/adminlte.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Elemen gambar profil
            const profilePictureMain = document.getElementById('profilePictureMain');
            const userImageHeader = document.getElementById('userImageHeader');
            const userImageNav = document.getElementById('userImageNav');
            
            // Input file
            const profilePictureInput = document.getElementById('profilePictureInput');
            const profilePictureInputMain = document.getElementById('profilePictureInputMain');
            const uploadBtn = document.getElementById('uploadBtn');
            
            // Event listener untuk semua gambar profil
            [profilePictureMain, userImageHeader, userImageNav].forEach(element => {
                if (element) {
                    element.addEventListener('click', () => {
                        profilePictureInputMain.click();
                    });
                }
            });
            
            // Event listener untuk tombol upload
            if (uploadBtn) {
                uploadBtn.addEventListener('click', () => {
                    profilePictureInputMain.click();
                });
            }
            
            // Event listener untuk input file utama
            if (profilePictureInputMain) {
                profilePictureInputMain.addEventListener('change', (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        uploadProfilePicture(file);
                    }
                });
            }
            
            // Event listener untuk input file di dropdown
            if (profilePictureInput) {
                profilePictureInput.addEventListener('change', (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        uploadProfilePicture(file);
                    }
                });
            }
            
            // Fungsi untuk upload foto profil
            function uploadProfilePicture(file) {
                const formData = new FormData();
                formData.append('profile_picture_file', file);
                
                // Validasi ukuran file (maksimal 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File terlalu besar',
                        text: 'Ukuran file maksimal 2MB'
                    });
                    return;
                }
                
                // Validasi tipe file
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format tidak didukung',
                        text: 'Hanya file JPG, PNG, atau GIF yang diperbolehkan'
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Mengunggah...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                
                fetch('upload_profile_picture.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Foto profil berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Refresh semua gambar profil dengan timestamp untuk menghindari cache
                            const newPath = data.new_path + '?t=' + new Date().getTime();
                            if (profilePictureMain) profilePictureMain.src = newPath;
                            if (userImageHeader) userImageHeader.src = newPath;
                            if (userImageNav) userImageNav.src = newPath;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan saat mengunggah foto'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan: ' + error.message
                    });
                })
                .finally(() => {
                    // Reset input file
                    if (profilePictureInputMain) profilePictureInputMain.value = '';
                    if (profilePictureInput) profilePictureInput.value = '';
                });
            }
        });
    </script>
    <script>
      function confirmLogout() {
          Swal.fire({
              title: 'Konfirmasi Logout',
              text: "Apakah Anda yakin ingin keluar?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Ya, Keluar',
              cancelButtonText: 'Tidak'
          }).then((result) => {
              if (result.isConfirmed) {
                  // Jika user memilih Ya, lakukan logout via AJAX
                  logoutUser();
              }
          });
      }

      function logoutUser() {
          // Tampilkan loading
          Swal.fire({
              title: 'Memproses...',
              text: 'Sedang keluar dari sistem',
              allowOutsideClick: false,
              didOpen: () => {
                  Swal.showLoading();
              }
          });

          // Kirim request AJAX ke logout.php
          fetch('logout.php', {
              method: 'GET',
              credentials: 'same-origin' // Untuk mengirim session cookie
          })
          .then(response => {
              if (response.redirected) {
                  // Jika redirect terjadi, arahkan ke halaman login
                  window.location.href = response.url;
              } else {
                  return response.text();
              }
          })
          .then(data => {
              // Jika tidak ada redirect, coba parse response
              try {
                  const jsonData = JSON.parse(data);
                  if (jsonData.success) {
                      window.location.href = '../login.php';
                  } else {
                      Swal.fire('Error', jsonData.message || 'Gagal logout', 'error');
                  }
              } catch (e) {
                  // Jika response bukan JSON, mungkin halaman HTML
                  window.location.href = '../login.php';
              }
          })
          .catch(error => {
              Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
          });
      }
    </script>
</body>
</html>