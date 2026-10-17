<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-md-4">
    <div class="card shadow-sm text-center p-4">
      <div class="mx-auto bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px; font-size: 2rem; font-weight: 700;">
        <?= strtoupper(substr($user['name'], 0, 1)) ?>
      </div>
      <h5 class="font-weight-bold text-dark mb-1"><?= html_escape($user['name']) ?></h5>
      <p class="text-muted small mb-2">@<?= html_escape($user['username']) ?></p>
      <div>
        <span class="badge <?= $user['role'] === 'admin' ? 'badge-primary' : 'badge-secondary' ?> px-3 py-1 text-capitalize">
          <i class="fas fa-shield-alt mr-1"></i> <?= $user['role'] ?>
        </span>
      </div>
      <hr>
      <div class="text-left text-muted small">
        <div class="mb-1"><strong>Status:</strong> <span class="badge badge-success">Aktif</span></div>
        <div class="mb-1"><strong>Terdaftar:</strong> <?= date('d M Y', strtotime($user['created_at'])) ?></div>
        <div><strong>Login Terakhir:</strong> <?= $user['last_login'] ? date('d M Y H:i', strtotime($user['last_login'])) : '-' ?></div>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h5 class="card-title m-0"><i class="fas fa-user-edit mr-2 text-primary"></i> Perbarui Data Profil & Password</h5>
      </div>
      <div class="card-body">
        <?= form_open('profile') ?>
          <div class="form-group">
            <label class="font-weight-600">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" value="<?= set_value('name', $user['name']) ?>" required>
            <?= form_error('name', '<small class="text-danger">', '</small>') ?>
          </div>

          <div class="form-group">
            <label class="font-weight-600">Username</label>
            <input type="text" class="form-control" value="<?= html_escape($user['username']) ?>" disabled>
            <small class="text-muted">Username tidak dapat diubah secara mandiri.</small>
          </div>

          <hr class="my-4">
          <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-key mr-2"></i> Ganti Password (Opsional)</h6>
          <p class="text-muted small">Biarkan bagian password kosong jika Anda tidak ingin mengubah password akun.</p>

          <div class="form-group">
            <label class="font-weight-600">Password Saat Ini</label>
            <input type="password" name="current_password" class="form-control" placeholder="Masukkan password saat ini">
            <?= form_error('current_password', '<small class="text-danger">', '</small>') ?>
          </div>

          <div class="row">
            <div class="col-md-6 form-group">
              <label class="font-weight-600">Password Baru</label>
              <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
              <?= form_error('new_password', '<small class="text-danger">', '</small>') ?>
            </div>
            <div class="col-md-6 form-group">
              <label class="font-weight-600">Konfirmasi Password Baru</label>
              <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru">
              <?= form_error('confirm_password', '<small class="text-danger">', '</small>') ?>
            </div>
          </div>

          <div class="mt-4 text-right">
            <button type="submit" class="btn btn-primary px-4 shadow-sm">
              <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
          </div>
        <?= form_close() ?>
      </div>
    </div>
  </div>
</div>
