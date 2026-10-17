<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row mb-3">
  <div class="col-12 d-flex flex-wrap justify-content-between align-items-center">
    <h4 class="font-weight-bold text-dark mb-2 mb-md-0">
      <i class="fas fa-store mr-2 text-primary"></i> Data Usaha Mikro, Kecil, dan Menengah (UMKM)
    </h4>
    <div class="d-flex flex-wrap gap-2">
      <a href="<?= base_url('evaluasi/massal') ?>" class="btn btn-warning mr-2 mb-1 shadow-sm font-weight-bold text-dark">
        <i class="fas fa-bolt mr-1"></i> Evaluasi Massal (Fuzzy)
      </a>
      <a href="<?= base_url('umkm/tambah') ?>" class="btn btn-primary mr-2 mb-1 shadow-sm">
        <i class="fas fa-plus mr-1"></i> Tambah UMKM
      </a>
      <a href="<?= base_url('umkm/import') ?>" class="btn btn-outline-primary mr-2 mb-1">
        <i class="fas fa-file-import mr-1"></i> Impor Dataset
      </a>
      <a href="<?= base_url('umkm/export?' . http_build_query($filters)) ?>" class="btn btn-success mb-1">
        <i class="fas fa-file-excel mr-1"></i> Ekspor CSV
      </a>
    </div>
  </div>
</div>

<!-- Filter Box -->
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white">
    <h6 class="card-title m-0"><i class="fas fa-filter mr-2 text-primary"></i> Filter & Pencarian Dataset</h6>
  </div>
  <div class="card-body">
    <form method="get" action="<?= base_url('umkm') ?>">
      <div class="row">
        <div class="col-md-3 col-sm-6 form-group">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Cari Kata Kunci</label>
          <input type="text" name="search" class="form-control form-control-sm" placeholder="ID UMKM, Nama, atau Usaha" value="<?= html_escape($filters['search']) ?>">
        </div>

        <div class="col-md-2 col-sm-6 form-group">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Kecamatan</label>
          <select name="kecamatan" class="form-control form-control-sm">
            <option value="">-- Semua Kecamatan --</option>
            <?php foreach ($kecamatan_list as $kec): ?>
              <option value="<?= html_escape($kec) ?>" <?= ($filters['kecamatan'] === $kec) ? 'selected' : '' ?>><?= html_escape($kec) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-3 col-sm-6 form-group">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Bidang Usaha</label>
          <select name="bidang_usaha" class="form-control form-control-sm">
            <option value="">-- Semua Bidang Usaha --</option>
            <?php foreach ($bidang_usaha_list as $bu): ?>
              <option value="<?= html_escape($bu) ?>" <?= ($filters['bidang_usaha'] === $bu) ? 'selected' : '' ?>><?= html_escape($bu) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-2 col-sm-6 form-group">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Stabilitas</label>
          <select name="stabilitas_pendapatan" class="form-control form-control-sm">
            <option value="">-- Semua --</option>
            <option value="Fluktuatif" <?= ($filters['stabilitas_pendapatan'] === 'Fluktuatif') ? 'selected' : '' ?>>Fluktuatif</option>
            <option value="Cukup stabil" <?= ($filters['stabilitas_pendapatan'] === 'Cukup stabil') ? 'selected' : '' ?>>Cukup stabil</option>
            <option value="Stabil" <?= ($filters['stabilitas_pendapatan'] === 'Stabil') ? 'selected' : '' ?>>Stabil</option>
          </select>
        </div>

        <div class="col-md-2 col-sm-6 form-group">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Status Usaha</label>
          <select name="status_usaha" class="form-control form-control-sm">
            <option value="">-- Semua Status --</option>
            <option value="Aktif" <?= ($filters['status_usaha'] === 'Aktif') ? 'selected' : '' ?>>Aktif</option>
            <option value="Perlu verifikasi" <?= ($filters['status_usaha'] === 'Perlu verifikasi') ? 'selected' : '' ?>>Perlu verifikasi</option>
          </select>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center pt-2 border-top">
        <div class="text-muted small">
          Menemukan <strong><?= number_format($total_rows) ?></strong> data UMKM
        </div>
        <div>
          <button type="submit" class="btn btn-sm btn-primary px-3 mr-2">
            <i class="fas fa-search mr-1"></i> Terapkan Filter
          </button>
          <a href="<?= base_url('umkm') ?>" class="btn btn-sm btn-light border">
            <i class="fas fa-redo mr-1"></i> Reset
          </a>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Data Table Card -->
<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th width="40">NO</th>
            <th>ID UMKM</th>
            <th>Nama Usaha / Pemilik</th>
            <th>Bidang Usaha</th>
            <th>Nilai Modal (Rp)</th>
            <th>Lama Usaha</th>
            <th>Stabilitas</th>
            <th>Kecamatan / Desa</th>
            <th>Status</th>
            <th class="text-center" width="160">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($umkm_list)): ?>
            <?php $no = $offset + 1; foreach ($umkm_list as $u): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><strong class="text-primary"><?= html_escape($u['id_umkm']) ?></strong></td>
                <td>
                  <div class="font-weight-600 text-dark"><?= html_escape($u['nama_umkm']) ?></div>
                </td>
                <td><?= html_escape($u['bidang_usaha'] ? $u['bidang_usaha'] : '-') ?></td>
                <td>Rp<?= number_format($u['nilai_investasi'], 0, ',', '.') ?></td>
                <td><?= $u['lama_usaha'] ?> Thn</td>
                <td>
                  <?php if ($u['stabilitas_pendapatan'] === 'Stabil'): ?>
                    <span class="badge-soft-success">Stabil</span>
                  <?php elseif ($u['stabilitas_pendapatan'] === 'Cukup stabil'): ?>
                    <span class="badge-soft-warning">Cukup stabil</span>
                  <?php else: ?>
                    <span class="badge-soft-danger">Fluktuatif</span>
                  <?php endif; ?>
                </td>
                <td>
                  <small class="text-muted"><?= html_escape($u['kecamatan']) ?> &bull; <?= html_escape($u['desa_kelurahan']) ?></small>
                </td>
                <td>
                  <span class="badge badge-light border text-capitalize"><?= html_escape($u['status_usaha']) ?></span>
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= base_url('evaluasi?umkm_id=' . $u['id']) ?>" class="btn btn-primary" title="Evaluasi Kelayakan Fuzzy">
                      <i class="fas fa-calculator"></i>
                    </a>
                    <a href="<?= base_url('umkm/detail/' . $u['id']) ?>" class="btn btn-info" title="Lihat Detail">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="<?= base_url('umkm/ubah/' . $u['id']) ?>" class="btn btn-warning" title="Ubah Data">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="#" class="btn btn-danger" onclick="return confirmDelete('<?= base_url('umkm/hapus/' . $u['id']) ?>', 'Yakin ingin menghapus data UMKM <?= addslashes(html_escape($u['nama_umkm'])) ?> (<?= $u['id_umkm'] ?>)?');" title="Hapus Data">
                      <i class="fas fa-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="10" class="text-center py-5 text-muted">
                <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                <p class="mb-1">Tidak ada data UMKM yang cocok dengan filter atau pencarian Anda.</p>
                <a href="<?= base_url('umkm') ?>" class="btn btn-sm btn-outline-primary mt-2">Reset Filter</a>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination & Summary -->
    <?php if ($total_pages > 1): ?>
      <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top">
        <div class="text-muted small mb-2 mb-md-0">
          Halaman <strong><?= $current_page ?></strong> dari <strong><?= $total_pages ?></strong> (Total <?= number_format($total_rows) ?> data)
        </div>
        <nav aria-label="Page navigation">
          <ul class="pagination pagination-sm m-0">
            <?php
              $query_str = $_GET;
              unset($query_str['page']);
              $base_page_url = base_url('umkm?' . http_build_query($query_str) . '&page=');
            ?>
            <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= $base_page_url . ($current_page - 1) ?>">Sebelumnya</a>
            </li>
            
            <?php
              $start_p = max(1, $current_page - 2);
              $end_p = min($total_pages, $current_page + 2);
              for ($p = $start_p; $p <= $end_p; $p++):
            ?>
              <li class="page-item <?= ($current_page == $p) ? 'active' : '' ?>">
                <a class="page-link" href="<?= $base_page_url . $p ?>"><?= $p ?></a>
              </li>
            <?php endfor; ?>

            <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= $base_page_url . ($current_page + 1) ?>">Selanjutnya</a>
            </li>
          </ul>
        </nav>
      </div>
    <?php endif; ?>
  </div>
</div>
