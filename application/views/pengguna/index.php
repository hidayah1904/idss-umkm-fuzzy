<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row mb-3 align-items-center">
  <div class="col-md-7">
    <h4 class="font-weight-bold text-dark mb-1">
      <i class="fas fa-users-cog mr-2 text-primary"></i> Manajemen Pengguna Sistem
    </h4>
    <p class="text-muted small mb-0">Pengelolaan hak akses pengguna, otentikasi role Administrator dan Operator.</p>
  </div>
  <div class="col-md-5 text-md-right mt-3 mt-md-0">
    <a href="<?= base_url('pengguna/tambah') ?>" class="btn btn-primary shadow-sm">
      <i class="fas fa-user-plus mr-1"></i> Tambah Pengguna Baru
    </a>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-body p-3">
    <form method="get" action="<?= base_url('pengguna') ?>" class="form-inline">
      <div class="input-group input-group-sm w-100" style="max-width: 380px;">
        <input type="text" name="search" class="form-control" placeholder="Cari nama, username, atau role..." value="<?= html_escape($search) ?>">
        <div class="input-group-append">
          <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Cari</button>
          <?php if (!empty($search)): ?>
            <a href="<?= base_url('pengguna') ?>" class="btn btn-light border"><i class="fas fa-times"></i></a>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th width="50">ID</th>
            <th>Nama Lengkap</th>
            <th>Username</th>
            <th>Role Akses</th>
            <th>Status</th>
            <th>Login Terakhir</th>
            <th>Dibuat Pada</th>
            <th class="text-center" width="130">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($users)): ?>
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td>
                  <div class="font-weight-600 text-dark"><?= html_escape($u['name']) ?></div>
                </td>
                <td><code><?= html_escape($u['username']) ?></code></td>
                <td>
                  <span class="badge <?= ($u['role'] === 'admin') ? 'badge-primary' : 'badge-secondary' ?> text-capitalize px-2 py-1">
                    <i class="fas <?= ($u['role'] === 'admin') ? 'fa-shield-alt' : 'fa-user' ?> mr-1"></i> <?= $u['role'] ?>
                  </span>
                </td>
                <td>
                  <?php if ($u['is_active'] == 1): ?>
                    <span class="badge-soft-success">Aktif</span>
                  <?php else: ?>
                    <span class="badge-soft-danger">Nonaktif</span>
                  <?php endif; ?>
                </td>
                <td><small class="text-muted"><?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : '-' ?></small></td>
                <td><small class="text-muted"><?= date('d/m/Y', strtotime($u['created_at'])) ?></small></td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= base_url('pengguna/ubah/' . $u['id']) ?>" class="btn btn-warning" title="Ubah Pengguna">
                      <i class="fas fa-edit"></i>
                    </a>
                    <?php if ($u['id'] != $this->session->userdata('user_id')): ?>
                      <a href="#" class="btn btn-danger" onclick="return confirmDelete('<?= base_url('pengguna/hapus/' . $u['id']) ?>', 'Yakin ingin menghapus pengguna <?= addslashes(html_escape($u['name'])) ?>?');" title="Hapus Pengguna">
                        <i class="fas fa-trash"></i>
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">Tidak ada pengguna ditemukan.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
