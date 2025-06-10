<?php
  session_start();

  header("Cache-Control: no-cache, no-store, must-revalidate"); // Untuk HTTP/1.1
  header("Pragma: no-cache"); // Untuk HTTP/1.0
  header("Expires: 0"); // Untuk Proxies

  if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Admin') {
      header("Location: ../login.php");
      exit;
  }

  // Redirect ke halaman login jika belum login
  if (!isset($_SESSION['username'])) {
      header("Location: ../login.php");
      exit;
  }

  // Pastikan role ada di session
  if (!isset($_SESSION['role'])) {
      // Jika role tidak ada, mungkin logout atau redirect ke error
      header("Location: login.php?error=role_missing");
      exit;
  }

  include "koneksi.php"; // Pastikan file ini ada dan berfungsi
  $db = new database(); // Pastikan class 'database' ada dan berfungsi

  // Ambil data total - ini akan dilihat oleh admin & guru
  $totalSiswa = $db->total_siswa();
  $totalAgama = $db->total_agama();
  $totalJurusan = $db->total_jurusan();
  $totalUser = $db->total_user();
  $genderData = $db->get_gender_distribution();
  $genderLabels = [];
  $genderValuesRaw = [];
  $totalGender = 0;

  foreach ($genderData as $item) {
    $genderLabels[] = $item['gender'];
    $genderValuesRaw[] = $item['total'];
    $totalGender += $item['total'];
  }

  $genderValues = [];
  foreach ($genderValuesRaw as $value) {
      $percentage = $totalGender > 0 ? round(($value / $totalGender) * 100, 1) : 0;
      $genderValues[] = $percentage;
  }

  $json_gender_labels = json_encode($genderLabels);
  $json_gender_values = json_encode($genderValues);

  $chart_categories = ['Siswa', 'Agama', 'Jurusan', 'Users'];
  $chart_data = [$totalSiswa, $totalAgama, $totalJurusan, $totalUser];

  $json_chart_categories = json_encode($chart_categories);
  $json_chart_data = json_encode($chart_data);

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
    <title>AdminLTE v4 | Dashboard</title>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="AdminLTE v4 | Dashboard" />
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
    <!-- apexcharts -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
      integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0="
      crossorigin="anonymous"
    />
    <!-- jsvectormap -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
      integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4="
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
      #gender-chart {
          width: 100%;
          display: flex;
          justify-content: center;
          margin: 0 auto;
          padding: 0;
      }

      #gender-chart .apexcharts-canvas {
          margin: 0 auto;
      }

      @media (max-width: 768px) {  
        .card-gender {
          margin-top: 1.5rem;
        }
      }

      .card-body {
          padding: 1rem;
          overflow: visible;
      }

      .apexcharts-legend {
          padding-top: 15px !important;
          justify-content: center !important;
      }

      .user-image.changeable, .user-header img.changeable {
          cursor: pointer; position: relative;
      }

      .user-image.changeable:hover::after, .user-header img.changeable:hover::after {
          content: "Ganti Foto"; position: absolute; top: 50%; left: 50%;
          transform: translate(-50%, -50%); background-color: rgba(0, 0, 0, 0.6);
          color: white; padding: 5px 10px; border-radius: 5px;
          font-size: 12px; pointer-events: none; text-align: center;
      }
    </style>
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
      <?php include "sidebar.php" ?>
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                 <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
              <!--begin::Col-->
              <div class="col-lg-3 col-6">
                <!--begin::Small Box Widget 1-->
                <div class="small-box text-bg-primary">
                  <div class="inner">
                    <h3><?= $totalSiswa ?></h3>
                    <p>Data Siswa</p>
                  </div>
                  <div class="small-box-icon">
                    <i class="fa-solid fa-user"></i>
                  </div>
                    <path
                      d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z"
                    ></path>
                  </svg>
                  <a
                    href="datasiswa.php"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
                  >
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                  </a>
                </div>
                <!--end::Small Box Widget 1-->
              </div>
              <!--end::Col-->
              <div class="col-lg-3 col-6">
                <!--begin::Small Box Widget 2-->
                <div class="small-box text-bg-success">
                  <div class="inner">
                    <h3><?= $totalAgama ?></h3>
                    <p>Data Agama</p>
                  </div>
                  <div class="small-box-icon">
                    <i class="fa-solid fa-praying-hands"></i>
                  </div>
                    <path
                      d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"
                    ></path>
                  </svg>
                  <a
                    href="dataagama.php"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
                  >
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                  </a>
                </div>
                <!--end::Small Box Widget 2-->
              </div>
              <!--end::Col-->
              <div class="col-lg-3 col-6">
                <!--begin::Small Box Widget 3-->
                <div class="small-box text-bg-warning">
                  <div class="inner">
                    <h3><?= $totalJurusan ?></h3>
                    <p>Data Jurusan</p>
                  </div>
                  <div class="small-box-icon">
                    <i class="fa-solid fa-book"></i>
                  </div>
                    <path
                      d="M6.25 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.25 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 00-1.5 0v2.25H16a.75.75 0 000 1.5h2.25v2.25a.75.75 0 001.5 0v-2.25H22a.75.75 0 000-1.5h-2.25V7.5z"
                    ></path>
                  </svg>
                  <a
                    href="datajurusan.php"
                    class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover"
                  >
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                  </a>
                </div>
                <!--end::Small Box Widget 3-->
              </div>
              <!--end::Col-->
              <div class="col-lg-3 col-6">
                <!--begin::Small Box Widget 4-->
                <div class="small-box text-bg-danger">
                  <div class="inner">
                    <h3><?= $totalUser ?></h3>
                    <p>Data Users</p>
                  </div>
                  <div class="small-box-icon">
                    <i class="fa-solid fa-user-tie"></i>
                  </div>
                    <path
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                      d="M2.25 13.5a8.25 8.25 0 018.25-8.25.75.75 0 01.75.75v6.75H18a.75.75 0 01.75.75 8.25 8.25 0 01-16.5 0z"
                    ></path>
                    <path
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                      d="M12.75 3a.75.75 0 01.75-.75 8.25 8.25 0 018.25 8.25.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z"
                    ></path>
                  </svg>
                  <a
                    href="datauser.php"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
                  >
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                  </a>
                </div>
                <!--end::Small Box Widget 4-->
              </div>
              <div class="row mb-4">
                  <div class="col-md-8">
                      <div class="card h-100">
                          <div class="card-header"><h3 class="card-title">Data Statistik</h3></div>
                          <div class="card-body"><div id="revenue-chart"></div></div>
                      </div>
                  </div>
                  <div class="col-md-4 card-gender">
                      <div class="card h-100">
                          <div class="card-header"><h3 class="card-title">Distribusi Gender Siswa</h3></div>
                          <div class="card-body">
                              <div id="gender-chart" style="min-height: 250px;"></div>
                          </div>
                      </div>
                  </div>
              </div>
              <!--end::Col-->
            </div>
            <!--end::Row-->
            <!-- /.row (main row) -->
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
    <!-- OPTIONAL SCRIPTS -->
    <!-- sortablejs -->
    <script
      src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
      integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ="
      crossorigin="anonymous"
    ></script>
    <!-- sortablejs -->
    <script>
      const connectedSortables = document.querySelectorAll('.connectedSortable');
      connectedSortables.forEach((connectedSortable) => {
        let sortable = new Sortable(connectedSortable, {
          group: 'shared',
          handle: '.card-header',
        });
      });

      const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
      cardHeaders.forEach((cardHeader) => {
        cardHeader.style.cursor = 'move';
      });
    </script>
    <!-- apexcharts -->
    <script
      src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
      integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8="
      crossorigin="anonymous"
    ></script>
    <!-- ChartJS -->
    <script>
    // NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
    // IT'S ALL JUST JUNK FOR DEMO
    // ++++++++++++++++++++++++++++++++++++++++++

    const chartCategories = <?php echo $json_chart_categories; ?>;
    const chartData = <?php echo $json_chart_data; ?>;

    const sales_chart_options = {
        series: [
            {
                name: 'Jumlah Data', // Nama seri, bisa disesuaikan
                data: chartData,
            },
        ],
        chart: {
            height: 300,
            type: 'area',
            toolbar: {
                show: false,
            },
        },
        legend: {
            show: false,
        },
        colors: ['#0d6efd'], // Anda bisa menyesuaikan warna
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: 'smooth',
        },
        xaxis: {
            type: 'category', // Mengubah tipe x-axis menjadi 'category'
            categories: chartCategories,
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy', // Sesuaikan format jika diperlukan, atau hapus jika tidak relevan dengan kategori
            },
        },
    };

    const sales_chart = new ApexCharts(
        document.querySelector('#revenue-chart'),
        sales_chart_options,
        );
        sales_chart.render();

    const genderLabels = <?php echo $json_gender_labels; ?>;
    const genderValues = <?php echo $json_gender_values; ?>;

    const gender_chart_options = {
        series: genderValues,
        chart: {
            type: 'pie',
            width: '100%',
            height: 350,
        },
        labels: genderLabels,
        colors: ['#0d6efd', '#fd7e14'], // Warna untuk Laki-laki dan Perempuan
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: '14px',
            markers: {
                width: 12,
                height: 12,
                radius: 6
            },
            itemMargin: {
                horizontal: 10,
                vertical: 5
            }
        },
        // Sederhanakan plotOptions, hapus startAngle, endAngle, dan customScale
        plotOptions: {
            pie: {
                expandOnClick: true,
                // Pastikan ini adalah pie chart, bukan donut
                donut: {
                    size: '0%'
                },
                dataLabels: {
                    offset: 10,
                    minAngleToShowLabel: 10
                }
            }
        },
        responsive: [{
            breakpoint: 768,
            options: {
                chart: {
                    height: 300
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    const gender_chart = new ApexCharts(
        document.querySelector('#gender-chart'),
        gender_chart_options
    );
    gender_chart.render();
    </script>
    <!-- jsvectormap -->
    <script
      src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
      integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y="
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
      integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY="
      crossorigin="anonymous"
    ></script>
    <!-- jsvectormap -->
    <script>
      const visitorsData = {
        US: 398, // USA
        SA: 400, // Saudi Arabia
        CA: 1000, // Canada
        DE: 500, // Germany
        FR: 760, // France
        CN: 300, // China
        AU: 700, // Australia
        BR: 600, // Brazil
        IN: 800, // India
        GB: 320, // Great Britain
        RU: 3000, // Russia
      };

      // World map by jsVectorMap
      const map = new jsVectorMap({
        selector: '#world-map',
        map: 'world',
      });

      // Sparkline charts
      const option_sparkline1 = {
        series: [
          {
            data: [1000, 1200, 920, 927, 931, 1027, 819, 930, 1021],
          },
        ],
        chart: {
          type: 'area',
          height: 50,
          sparkline: {
            enabled: true,
          },
        },
        stroke: {
          curve: 'straight',
        },
        fill: {
          opacity: 0.3,
        },
        yaxis: {
          min: 0,
        },
        colors: ['#DCE6EC'],
      };

      const sparkline1 = new ApexCharts(document.querySelector('#sparkline-1'), option_sparkline1);
      sparkline1.render();

      const option_sparkline2 = {
        series: [
          {
            data: [515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921],
          },
        ],
        chart: {
          type: 'area',
          height: 50,
          sparkline: {
            enabled: true,
          },
        },
        stroke: {
          curve: 'straight',
        },
        fill: {
          opacity: 0.3,
        },
        yaxis: {
          min: 0,
        },
        colors: ['#DCE6EC'],
      };

      const sparkline2 = new ApexCharts(document.querySelector('#sparkline-2'), option_sparkline2);
      sparkline2.render();

      const option_sparkline3 = {
        series: [
          {
            data: [15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21],
          },
        ],
        chart: {
          type: 'area',
          height: 50,
          sparkline: {
            enabled: true,
          },
        },
        stroke: {
          curve: 'straight',
        },
        fill: {
          opacity: 0.3,
        },
        yaxis: {
          min: 0,
        },
        colors: ['#DCE6EC'],
      };

      const sparkline3 = new ApexCharts(document.querySelector('#sparkline-3'), option_sparkline3);
      sparkline3.render();
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
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>