<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $is_edit = isset($edit_user); ?>

<div class="row">
  <div class="col-lg-6 offset-lg-3">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0">
          <i class="fas <?= $is_edit ? 'fa-user-edit text-warning' : 'fa-user-plus text-primary' ?> mr-2"></i>
          <?= $is_edit ? 'Ubah Data Pengguna: ' . html_escape($edit_user['name']) : 'Tambah Pengguna Baru' ?>
        </h5>
        <a href="<?= base_url('pengguna') ?>" class="btn btn-sm btn-light border">
          <i class="fas fa-arrow-left mr-1"></i> Batal
        </a>
      </div>
      <div class="card-body p-4">
        <?= form_open($is_edit ? 'pengguna/ubah/' . $edit_user['id'] : 'pengguna/tambah') ?>

          <div class="form-group mb-3">
            <label class="font-weight-600">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="Nama lengkap petugas" value="<?= set_value('name', $is_edit ? $edit_user['name'] : '') ?>" required>
            <?= form_error('name', '<small class="text-danger">', '</small>') ?>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-600">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" class="form-control" placeholder="Minimal 4 karakter" value="<?= set_value('username', $is_edit ? $edit_user['username'] : '') ?>" required>
            <?= form_error('username', '<small class="text-danger">', '</small>') ?>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-600">
              Password <?= $is_edit ? '<span class="text-muted small font-weight-normal">(Kosongkan jika tidak ingin mengubah)</span>' : '<span class="text-danger">*</span>' ?>
            </label>
            <input type="password" name="password" class="form-control" placeholder="<?= $is_edit ? 'Biarkan kosong untuk mempertahankan password lama' : 'Minimal 6 karakter' ?>" <?= $is_edit ? '' : 'required' ?>>
            <?= form_error('password', '<small class="text-danger">', '</small>') ?>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-600">Role Akses <span class="text-danger">*</span></label>
            <?php $sel_role = set_value('role', $is_edit ? $edit_user['role'] : 'operator'); ?>
            <select name="role" class="form-control custom-select" required>
              <option value="operator" <?= ($sel_role === 'operator') ? 'selected' : '' ?>>Operator (Akses UMKM, Evaluasi, dan Laporan)</option>
              <option value="admin" <?= ($sel_role === 'admin') ? 'selected' : '' ?>>Administrator (Akses Penuh Termasuk Pengguna & Log)</option>
            </select>
            <?= form_error('role', '<small class="text-danger">', '</small>') ?>
          </div>

          <div class="form-group mb-4">
            <div class="custom-control custom-checkbox">
              <?php 
                $is_act = $is_edit ? ($edit_user['is_active'] == 1) : true;
              ?>
              <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="checkActive" <?= $is_act ? 'checked' : '' ?>>
              <label class="custom-control-label font-weight-500" for="checkActive">Akun Aktif (Dapat Login ke Sistem)</label>
            </div>
          </div>

          <div class="d-flex justify-content-between pt-3 border-top">
            <a href="<?= base_url('pengguna') ?>" class="btn btn-light border px-3">Batal</a>
            <button type="submit" class="btn btn-primary px-4 shadow-sm">
              <i class="fas fa-save mr-1"></i> <?= $is_edit ? 'Simpan Perubahan' : 'Tambahkan Pengguna' ?>
            </button>
          </div>

        <?= form_close() ?>
      </div>
    </div>
  </div>
</div>
