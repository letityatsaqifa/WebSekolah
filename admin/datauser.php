<?php
  session_start();

  header("Cache-Control: no-cache, no-store, must-revalidate"); // Untuk HTTP/1.1
  header("Pragma: no-cache"); // Untuk HTTP/1.0
  header("Expires: 0"); // Untuk Proxies

  // Redirect ke halaman login jika belum login
  if(!isset($_SESSION['username'])) {
      header("Location: login.php");
      exit;
  }

  include "koneksi.php";
  $db = new database();

  $userId = $_SESSION['id']; // Menggunakan 'id'
  $profilePicturePath = $db->get_profile_picture($userId);

  // Jika tidak ada gambar atau path tidak valid, gunakan default
  $defaultPicture = 'dist/assets/img/avatar.png'; // Ganti jika perlu
  $profilePicturePath = ($profilePicturePath && file_exists($profilePicturePath)) ? $profilePicturePath : $defaultPicture;

  // Simpan path ke session (opsional, tapi bisa berguna)
  $_SESSION['foto'] = $profilePicturePath;
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE 4 | Simple Tables</title>
    <style>
      body {
        font-size: 15px;
      }
      .table {
        font-size: 14px;
      }
      .submit {
        display: block;
        width: 150px;
        padding: 10px;
        margin: 20px auto;
        background-color: #B9B28A;
        color: black;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        text-align: center;
        position: relative;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
      }
      .submit:hover {
        background-color: #504B38;
        color: #fff;
        transform: scale(1.05);
      }
      .submit::after {
        position: absolute;
        top: -25px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 12px;
        color: #504B38;
        opacity: 0;
        transition: opacity 0.3s;
      }
      .submit:hover::after {
        opacity: 1;
      }
      th {
        background-color: #504B38;
        color: white;
      }
      tr:nth-child(even) {
        background-color: #f9f9f9;
      }
      .option{
        width: 100px;
        margin: 20px auto;
        padding: 6px;
        text-align: center;
        text-decoration: none;
        color: #000;
        font-weight: bold;
        border-radius: 8px;
        background-color: #B9B28A;
      }
      .option:hover {
        background-color: #d8d2b2;
      }
      .add-button {
        display: block;
        width: 150px;
        margin: 20px auto;
        padding: 10px;
        text-align: center;
        background-color: #B9B28A;
        color: white;
        border-radius: 15px;
        text-decoration: none;
        font-weight: bold;
        margin: 20px 0 20px auto;
      }
      .add-button:hover {
        background-color: #d8d2b2;
      }
      #myTable .paging .searching .ordering .info .lengthChange{
        margin: 30px;
        padding: 8px 12px;
        text-align: center;
      }
      #myTable .btn {
          margin: 2px;
      }
      .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }
      
      #myTable {
        width: 100% !important;
      }
      
      /* Mobile-specific styles */
      @media (max-width: 768px) {
        body {
          font-size: 14px;
        }
        
        .table {
          font-size: 13px;
        }
        
        .card-body {
          padding: 0.5rem;
        }
        
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_filter input {
          font-size: 13px;
        }
        
        .dataTables_wrapper .dataTables_length select {
          padding: 0.2rem 0.5rem;
        }
      }
      
      /* Ensure buttons remain usable on mobile */
      .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
      }
    </style>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- jQuery (Wajib) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE 4 | Simple Tables" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS."
    />
    <meta
      name="keywords"
      content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard"
    />
    <!--end::Primary Meta Tags-->
    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="dist/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body sticky-top">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="index.php" class="nav-link">Home</a></li>
          </ul>
          <!--end::Start Navbar Links-->
          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img
                  src="<?php echo htmlspecialchars($profilePicturePath); ?>" class="user-image rounded-circle shadow"
                  alt="User Image"
                  id="userImageNav" />
                <span class="d-none d-md-inline"><?php echo $_SESSION['nama']; ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header text-bg-primary">
                  <img
                    src="<?php echo htmlspecialchars($profilePicturePath); ?>" class="rounded-circle shadow changeable" alt="User Image"
                    id="userImageHeader" />
                  <input type="file" id="profilePictureInput" style="display: none;" accept="image/jpeg, image/png, image/gif">
                  <p>
                    <?php echo $_SESSION['nama']; ?>
                    <small>Role: <?php echo $_SESSION['role']; ?></small>
                  </p>
                </li>
                <!--end::User Image-->
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <a href="profile.php" class="btn btn-default btn-flat">Profile</a>
                  <a href="#" class="btn btn-default btn-flat float-end" onclick="confirmLogout()">Sign out</a>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
      <?php include "sidebar.php"; ?>
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Data Users</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Data Users</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <!-- /.col -->
              <div class="col-md-12">
                <!-- /.card -->
                <div class="card mb-4">
                  <!-- /.card-header -->
                  <div class="card-body p-0 table-responsive">
                    <table id="myTable" class="display nowrap" style="width:100%">
                      <thead>
                        <tr>
                          <th style="width: 15px">Id</th>
                          <th>Nama Lengkap</th>
                          <th>Username</th>
                          <th>Role</th>
                          <th style="width: 150px">Label</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          foreach($db->tampil_data_user() as $x){
                          ?>
                          <tr>
                              <td><?php echo $x['id']; ?></td>
                              <td><?php echo $x['nama']; ?></td>
                              <td><?php echo $x['username']; ?></td>
                              <td><?php echo $x['role']; ?></td>
                              <td class="op">
                                <button type="button" class="btn btn-warning btn-sm btn-edit-user" data-id="<?php echo $x['id']; ?>" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                
                                <button type="button" class="btn btn-danger btn-sm btn-hapus-user" data-id="<?php echo $x['id']; ?>">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                              </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
                <a href="tambahuser.php" class="add-button">Tambah Data</a>
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editUserModalLabel">Edit Data User</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="editUserForm">
                <input type="hidden" id="edit_id" name="id">
                <div class="row mb-3">
                  <label for="edit_nama" class="col-sm-2 col-form-label">Nama Lengkap</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="edit_nama" name="nama" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="edit_username" class="col-sm-2 col-form-label">Username</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" id="edit_username" name="username" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="edit_password" class="col-sm-2 col-form-label">Password</label>
                  <div class="col-sm-10">
                      <input type="password" class="form-control" id="edit_password" name="password" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="edit_role" class="col-sm-2 col-form-label">Role</label>
                  <div class="col-sm-10">
                      <select class="form-select" id="edit_role" name="role" required>
                          <option value="Admin">Admin</option>
                          <option value="Guru">Guru</option>
                          <option value="Siswa">Siswa</option>
                      </select>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              <button type="button" class="btn btn-primary" id="saveUserButton">Simpan Perubahan</button>
            </div>
          </div>
        </div>
      </div>
      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; 2014-2024&nbsp;
          <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="dist/js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::Script-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      $(document).ready(function() {
    // Inisialisasi DataTables pertama kali saat dokumen siap
    var table = $('#myTable').DataTable({
        "responsive": true,
        "scrollX": true,
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10,
        "language": {
            "paginate": {
                "previous": "<i class='bi bi-chevron-left'></i>",
                "next": "<i class='bi bi-chevron-right'></i>"
            },
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "infoFiltered": "(disaring dari _MAX_ total data)"
        },
        "dom": '<"top"lf>rt<"bottom"ip><"clear">'
    });

    // Handler untuk tombol edit
    $('#myTable tbody').on('click', '.btn-edit-user', function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'api_user.php',
            type: 'GET',
            data: { aksi: 'get_single', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var user = response.user;
                    $('#edit_id').val(user.id);
                    $('#edit_nama').val(user.nama);
                    $('#edit_username').val(user.username);
                    $('#edit_password').val(user.password);
                    $('#edit_role').val(user.role);
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Gagal mengambil data. Periksa koneksi atau file API.', 'error');
            }
        });
    });

    // Handler untuk tombol simpan perubahan
    $('#saveUserButton').on('click', function() {
        var formData = $('#editUserForm').serialize() + '&aksi=update';

        $.ajax({
            url: 'api_user.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#saveUserButton').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                $('#saveUserButton').prop('disabled', true);
            },
            complete: function() {
                $('#saveUserButton').html('Simpan Perubahan');
                $('#saveUserButton').prop('disabled', false);
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#editUserModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Alih-alih table.ajax.reload(), muat ulang seluruh halaman.
                        // Ini akan merender ulang body tabel PHP dan menginisialisasi ulang DataTables.
                        location.reload();
                    });

                    // Jika mengedit pengguna yang sedang login, perbarui UI
                    if (response.is_current_user) {
                        $('.user-name').text(response.updated_user.nama);
                        $('.user-role').text('Role: ' + response.updated_user.role);
                        sessionStorage.setItem('userData', JSON.stringify(response.updated_user));
                    }

                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                console.error("Response:", jqXHR.responseText);
                Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data.', 'error');
            }
        });
    });

    // Handler untuk tombol hapus
    $('#myTable tbody').on('click', '.btn-hapus-user', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data user ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'api_user.php',
                    type: 'POST',
                    data: { aksi: 'hapus', id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Untuk penghapusan, Anda bisa langsung menghapus baris dari DataTables
                            table.row(row).remove().draw(false);
                            Swal.fire('Terhapus!', 'Data user berhasil dihapus.', 'success');
                        } else { Swal.fire('Gagal!', response.message, 'error'); }
                    },
                    error: function() { Swal.fire('Error!', 'Gagal menghubungi server.', 'error'); }
                });
            }
        });
    });
});
    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
      const userImageHeader = document.getElementById('userImageHeader');
      const userImageNav = document.getElementById('userImageNav');
      const profilePictureInput = document.getElementById('profilePictureInput');

      // Tambahkan event listener untuk kedua gambar
      [userImageHeader, userImageNav].forEach(element => {
          if (element) {
              element.addEventListener('click', () => {
                  profilePictureInput.click();
              });
          }
      });

      if (profilePictureInput) {
          profilePictureInput.addEventListener('change', (event) => {
              const file = event.target.files[0];
              if (file) {
                  uploadProfilePicture(file);
              }
          });
      }

      function uploadProfilePicture(file) {
          const formData = new FormData();
          formData.append('profile_picture_file', file);

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
                      text: 'Foto profil berhasil diperbarui.',
                      timer: 1500,
                      showConfirmButton: false
                  }).then(() => {
                      // Refresh gambar dengan timestamp untuk menghindari cache
                      const newPath = data.new_path + '?t=' + new Date().getTime();
                      if(userImageHeader) userImageHeader.src = newPath;
                      if(userImageNav) userImageNav.src = newPath;
                  });
              } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Gagal!',
                      text: data.message || 'Terjadi kesalahan saat mengunggah foto.'
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
              profilePictureInput.value = '';
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
  <!--end::Body-->
</html>