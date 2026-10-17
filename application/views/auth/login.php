<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login — IDSS UMKM Fuzzy Mamdani</title>
  <!-- Google Font: Inter -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Bootstrap 4 & AdminLTE 3 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
  <style>
    body {
      background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }
    .login-box {
      width: 100%;
      max-width: 440px;
    }
    .login-card {
      background: #FFFFFF;
      border-radius: 16px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
      overflow: hidden;
    }
    .login-card-header {
      background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%);
      color: #FFFFFF;
      padding: 2.25rem 2rem 1.75rem 2rem;
      text-align: center;
    }
    .login-card-body {
      padding: 2rem;
    }
  </style>
</head>
<body>

<div class="login-box">
  <div class="login-card">
    <div class="login-card-header">
      <div class="mb-2">
        <i class="fas fa-brain fa-3x text-light"></i>
      </div>
      <h3 class="font-weight-bold mb-1" style="letter-spacing: -0.5px;">IDSS UMKM</h3>
      <p class="text-light mb-0 small" style="opacity: 0.9;">
        Model Penalaran Fuzzy Mamdani untuk Evaluasi Kelayakan Usaha UMKM
      </p>
    </div>

    <div class="login-card-body">
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger py-2 px-3 small mb-3">
          <i class="fas fa-exclamation-circle mr-1"></i> <?= $this->session->flashdata('error') ?>
        </div>
      <?php endif; ?>

      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success py-2 px-3 small mb-3">
          <i class="fas fa-check-circle mr-1"></i> <?= $this->session->flashdata('success') ?>
        </div>
      <?php endif; ?>

      <?= form_open('login') ?>
        <div class="form-group mb-3">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Username</label>
          <div class="input-group">
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="<?= set_value('username') ?>" required autofocus>
            <div class="input-group-append">
              <div class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></div>
            </div>
          </div>
          <?= form_error('username', '<small class="text-danger">', '</small>') ?>
        </div>

        <div class="form-group mb-4">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Password</label>
          <div class="input-group">
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            <div class="input-group-append">
              <div class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></div>
            </div>
          </div>
          <?= form_error('password', '<small class="text-danger">', '</small>') ?>
        </div>

        <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm">
          <i class="fas fa-sign-in-alt mr-2"></i> Masuk ke Sistem
        </button>
      <?= form_close() ?>

      <!-- Akun Default untuk Pengujian Penguji/Pengguna -->
      <div class="mt-4 pt-3 border-top text-left">
        <span class="text-xs font-weight-bold text-muted d-block mb-2 text-uppercase">
          <i class="fas fa-key text-warning mr-1"></i> Kredensial Pengujian Sistem:
        </span>
        <div class="bg-light p-2 rounded small border text-muted">
          <div>&bull; <strong>Admin:</strong> <code>admin</code> / <code>Admin#2026!</code></div>
          <div>&bull; <strong>Operator:</strong> <code>operator</code> / <code>Operator#2026!</code></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
