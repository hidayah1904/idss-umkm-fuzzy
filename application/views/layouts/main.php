<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title><?= isset($page_title) ? html_escape($page_title) : 'IDSS UMKM' ?> — Intelligent Decision Support System</title>

  <!-- Google Font: Inter / Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Bootstrap 4 & AdminLTE 3 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <!-- DataTables BS4 -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
  <!-- Custom Theme CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold text-primary">
          <i class="fas fa-project-diagram mr-1"></i> IDSS UMKM Fuzzy Mamdani
        </span>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center">
      <!-- User Account Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown" href="#">
          <div class="bg-primary text-white rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600;">
            <?= strtoupper(substr($logged_user['name'], 0, 1)) ?>
          </div>
          <span class="d-none d-md-inline font-weight-500 text-dark"><?= html_escape($logged_user['name']) ?></span>
          <span class="badge <?= $logged_user['role'] === 'admin' ? 'badge-primary' : 'badge-secondary' ?> ml-2 text-capitalize"><?= $logged_user['role'] ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-sm border-0">
          <div class="dropdown-header bg-light">
            <strong><?= html_escape($logged_user['name']) ?></strong>
            <div class="text-muted small">@<?= html_escape($logged_user['username']) ?> &bull; <?= ucfirst($logged_user['role']) ?></div>
          </div>
          <div class="dropdown-divider"></div>
          <a href="<?= base_url('profile') ?>" class="dropdown-item">
            <i class="fas fa-user-cog mr-2 text-primary"></i> Profil & Password
          </a>
          <?php if ($logged_user['role'] === 'admin'): ?>
          <a href="<?= base_url('pengguna') ?>" class="dropdown-item">
            <i class="fas fa-users-cog mr-2 text-info"></i> Manajemen Pengguna
          </a>
          <a href="<?= base_url('log_aktivitas') ?>" class="dropdown-item">
            <i class="fas fa-history mr-2 text-secondary"></i> Log Aktivitas
          </a>
          <?php endif; ?>
          <div class="dropdown-divider"></div>
          <a href="<?= base_url('logout') ?>" class="dropdown-item text-danger">
            <i class="fas fa-sign-out-alt mr-2"></i> Keluar Sistem
          </a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-2">
    <!-- Brand Logo -->
    <a href="<?= base_url('dashboard') ?>" class="brand-link">
      <i class="fas fa-brain brand-image text-primary ml-2 mr-2" style="font-size: 1.5rem; opacity: .9"></i>
      <span class="brand-text font-weight-bold">IDSS UMKM</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-3">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
          
          <li class="nav-header text-uppercase text-xs text-muted font-weight-bold">Menu Utama</li>
          
          <li class="nav-item">
            <a href="<?= base_url('dashboard') ?>" class="nav-link <?= ($active_menu === 'dashboard') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= base_url('umkm') ?>" class="nav-link <?= ($active_menu === 'umkm') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-store"></i>
              <p>Data UMKM</p>
            </a>
          </li>

          <li class="nav-header text-uppercase text-xs text-muted font-weight-bold">Penalaran Fuzzy</li>

          <li class="nav-item">
            <a href="<?= base_url('evaluasi') ?>" class="nav-link <?= ($active_menu === 'evaluasi') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-calculator"></i>
              <p>Evaluasi Tunggal</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= base_url('evaluasi/massal') ?>" class="nav-link <?= ($active_menu === 'evaluasi_massal') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-bolt text-warning"></i>
              <p>Evaluasi Massal</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= base_url('hasil_evaluasi') ?>" class="nav-link <?= ($active_menu === 'hasil_evaluasi') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-poll-h"></i>
              <p>Hasil & Riwayat</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= base_url('laporan') ?>" class="nav-link <?= ($active_menu === 'laporan') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>Laporan IDSS</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= base_url('konfigurasi_fuzzy') ?>" class="nav-link <?= ($active_menu === 'konfigurasi_fuzzy') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-sliders-h"></i>
              <p>Konfigurasi Model</p>
            </a>
          </li>

          <?php if ($logged_user['role'] === 'admin'): ?>
          <li class="nav-header text-uppercase text-xs text-muted font-weight-bold">Administrasi</li>

          <li class="nav-item">
            <a href="<?= base_url('pengguna') ?>" class="nav-link <?= ($active_menu === 'pengguna') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-users"></i>
              <p>Manajemen Pengguna</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= base_url('log_aktivitas') ?>" class="nav-link <?= ($active_menu === 'log_aktivitas') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-history"></i>
              <p>Log Aktivitas</p>
            </a>
          </li>
          <?php endif; ?>

          <li class="nav-header text-uppercase text-xs text-muted font-weight-bold">Akun</li>

          <li class="nav-item">
            <a href="<?= base_url('profile') ?>" class="nav-link <?= ($active_menu === 'profile') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-user-circle"></i>
              <p>Profil Saya</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= base_url('logout') ?>" class="nav-link text-danger">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Keluar (Logout)</p>
            </a>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2 align-items-center">
          <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark" style="font-size: 1.5rem;"><?= isset($page_title) ? html_escape($page_title) : 'Dashboard' ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
              <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-home mr-1"></i> Home</a></li>
              <?php if (!empty($breadcrumbs)): ?>
                <?php foreach ($breadcrumbs as $b): ?>
                  <?php if (!empty($b['url'])): ?>
                    <li class="breadcrumb-item"><a href="<?= $b['url'] ?>"><?= html_escape($b['label']) ?></a></li>
                  <?php else: ?>
                    <li class="breadcrumb-item active"><?= html_escape($b['label']) ?></li>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('warning')): ?>
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> <?= $this->session->flashdata('warning') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <!-- Content view injected here -->
        <?php if (isset($content_view)) { $this->load->view($content_view); } ?>

      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      <span class="badge badge-light border">
        <i class="fas fa-code-branch text-primary mr-1"></i> Model: <strong><?= isset($active_fuzzy_model['model_code']) ? html_escape($active_fuzzy_model['model_code']) : 'MAMDANI-V1.0' ?></strong> (v<?= isset($active_fuzzy_model['version']) ? html_escape($active_fuzzy_model['version']) : '1.0.0' ?>)
      </span>
    </div>
    <strong>&copy; <?= date('Y') ?> IDSS UMKM — Fuzzy Mamdani Decision Support System.</strong>
    <span class="text-muted d-block d-sm-inline ml-sm-2 text-xs">Model Penalaran Fuzzy untuk Evaluasi Kelayakan Usaha UMKM</span>
  </footer>
</div>
<!-- ./wrapper -->

<!-- Scripts: jQuery, Bootstrap 4, AdminLTE 3, Chart.js, DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<!-- Global CSRF Header Setup for AJAX -->
<script>
  $.ajaxSetup({
    data: {
      '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
    }
  });

  // Reusable confirmation modal handler
  function confirmDelete(url, message) {
    if (confirm(message || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) {
      window.location.href = url;
    }
    return false;
  }
</script>

<?php if (isset($extra_js)) { echo $extra_js; } ?>

</body>
</html>
