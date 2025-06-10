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

  if(isset($_POST['simpan'])){
      $db->tambah_siswa(
          $_POST['nisn'],
          $_POST['nama'],
          $_POST['jeniskelamin'],
          $_POST['kodejurusan'],
          $_POST['kelas'],
          $_POST['alamat'],
          $_POST['agama'],
          $_POST['nohp']
      );
      $_SESSION['success_message'] = "Data siswa berhasil ditambahkan";
      header("location:datasiswa.php");
      exit();
  }

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
                <!--begin::Horizontal Form-->
                <div class="card card-warning card-outline mb-4">
                  <!--begin::Header-->
                  <div class="card-header">
                    <div class="card-title">Tambah Data Siswa</div>
                  </div>
                  <!--end::Header-->
                  <!--begin::Form-->
                  <form action="" method="post" class="form" id="formTambahSiswa">
                    <!--begin::Body-->
                    <div class="card-body">
                      <div class="row mb-3">
                        <label for="nisn" class="col-sm-2 col-form-label"
                          >NISN</label
                        >
                        <div class="col-sm-10">
                          <input
                            type="text"
                            class="form-control"
                            id="nisn"
                            name="nisn"
                            required
                          />
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label for="nama" class="col-sm-2 col-form-label"
                          >Nama</label
                        >
                        <div class="col-sm-10">
                          <input
                            type="text"
                            class="form-control"
                            id="nama"
                            name="nama"
                            required
                          />
                        </div>
                      </div>
                      <fieldset class="row mb-3">
                        <legend for="jeniskelamin" class="col-form-label col-sm-2 pt-0">Jenis Kelamin</legend>
                        <div class="col-sm-10">
                          <div class="form-check">
                            <input
                              class="form-check-input"
                              type="radio"
                              name="jeniskelamin"
                              value="L"
                              required
                            />
                            <label class="form-check-label" for="gridRadios1">
                              Laki - Laki
                            </label>
                          </div>
                          <div class="form-check">
                            <input
                              class="form-check-input"
                              type="radio"
                              name="jeniskelamin"
                              value="P"
                              required
                            />
                            <label class="form-check-label" for="gridRadios2">
                              Perempuan
                            </label>
                          </div>
                        </div>
                      </fieldset>
                      <div class="row mb-3">
                        <label for="kodejurusan" class="col-sm-2 col-form-label"
                          >Jurusan</label
                        >
                        <div class="col-sm-10">
                          <input
                            type="text"
                            class="form-control"
                            id="kodejurusan" 
                            name="kodejurusan"
                            required
                          />
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label for="kelas" class="col-sm-2 col-form-label"
                          >Kelas</label
                        >
                        <div class="col-sm-10">
                          <select
                            class="form-select"
                            id="kelas" 
                            name="kelas"
                            required
                          ><option value=""></option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                          </select>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label for="alamat" class="col-sm-2 col-form-label"
                          >Alamat</label
                        >
                        <div class="col-sm-10">
                          <textarea
                            class="form-control"
                            name="alamat" 
                            id="alamat"
                            required
                          ></textarea>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label for="agama" class="col-sm-2 col-form-label"
                          >Agama</label
                        >
                        <div class="col-sm-10">
                          <input
                            type="text"
                            class="form-control"
                            id="agama" 
                            name="agama"
                            required
                          />
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label for="nohp" class="col-sm-2 col-form-label">No HP</label>
                        <div class="col-sm-10">
                          <input
                            type="text"
                            class="form-control"
                            id="nohp" 
                            name="nohp"
                            required
                            pattern="\d{13}"
                            maxlength="13"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            title="Nomor HP harus terdiri dari 13 angka"
                          />
                        </div>
                      </div>
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">
                      <button type="submit" class="btn btn-warning" name = "simpan">Simpan</button
                      >
                      <a href="datasiswa.php" class="btn float-end btn-secondary">Cancel</a>
                    </div>
                    <!--end::Footer-->
                  </form>
                  <!--end::Form-->
                </div>
                <!--end::Horizontal Form-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
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
    <!--end::OverlayScrollbars Configure-->
    <!--end::Script-->
    <script>
      if (window.history.replaceState) {
          window.history.replaceState(null, null, window.location.href);
      }

      window.addEventListener('pageshow', function(event) {
          const form = document.getElementById("formTambahSiswa");
          if (form) {
              form.reset();
          }
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