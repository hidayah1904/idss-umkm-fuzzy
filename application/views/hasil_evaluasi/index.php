<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row mb-3 align-items-center">
  <div class="col-md-7">
    <h4 class="font-weight-bold text-dark mb-1">
      <i class="fas fa-poll-h mr-2 text-primary"></i> Riwayat & Hasil Evaluasi Kelayakan Usaha
    </h4>
    <p class="text-muted small mb-0">Arsip riwayat hasil penalaran Fuzzy Mamdani tersimpan dengan snapshot parameter model.</p>
  </div>
  <div class="col-md-5 text-md-right mt-3 mt-md-0">
    <a href="<?= base_url('evaluasi') ?>" class="btn btn-primary shadow-sm mr-2 mb-1">
      <i class="fas fa-plus-circle mr-1"></i> Evaluasi Baru
    </a>
    <a href="<?= base_url('hasil_evaluasi/export?' . http_build_query($filters)) ?>" class="btn btn-success shadow-sm mb-1">
      <i class="fas fa-file-excel mr-1"></i> Ekspor CSV
    </a>
  </div>
</div>

<!-- Filter Box -->
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white">
    <h6 class="card-title m-0"><i class="fas fa-filter mr-2 text-primary"></i> Filter Riwayat Evaluasi</h6>
  </div>
  <div class="card-body">
    <form method="get" action="<?= base_url('hasil_evaluasi') ?>">
      <div class="row">
        <div class="col-md-3 col-sm-6 form-group">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Cari UMKM</label>
          <input type="text" name="search" class="form-control form-control-sm" placeholder="ID atau Nama Usaha" value="<?= html_escape($filters['search']) ?>">
        </div>

        <div class="col-md-2 col-sm-6 form-group">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Kategori</label>
          <select name="category" class="form-control form-control-sm">
            <option value="">-- Semua Kategori --</option>
            <option value="Layak" <?= ($filters['category'] === 'Layak') ? 'selected' : '' ?>>Layak (&ge; 70)</option>
            <option value="Cukup Layak" <?= ($filters['category'] === 'Cukup Layak') ? 'selected' : '' ?>>Cukup Layak (40-69)</option>
            <option value="Tidak Layak" <?= ($filters['category'] === 'Tidak Layak') ? 'selected' : '' ?>>Tidak Layak (&lt; 40)</option>
          </select>
        </div>

        <div class="col-md-2 col-sm-6 form-group">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Kecamatan</label>
          <select name="kecamatan" class="form-control form-control-sm">
            <option value="">-- Semua --</option>
            <?php foreach ($kecamatan_list as $kec): ?>
              <option value="<?= html_escape($kec) ?>" <?= ($filters['kecamatan'] === $kec) ? 'selected' : '' ?>><?= html_escape($kec) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-2 col-sm-6 form-group">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Dari Tanggal</label>
          <input type="date" name="start_date" class="form-control form-control-sm" value="<?= html_escape($filters['start_date']) ?>">
        </div>

        <div class="col-md-2 col-sm-6 form-group">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Sampai Tanggal</label>
          <input type="date" name="end_date" class="form-control form-control-sm" value="<?= html_escape($filters['end_date']) ?>">
        </div>

        <div class="col-md-1 col-sm-12 form-group d-flex align-items-end">
          <button type="submit" class="btn btn-sm btn-primary btn-block">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th width="40">ID</th>
            <th>ID UMKM</th>
            <th>Nama Usaha</th>
            <th>Kecamatan</th>
            <th>Modal (Rp)</th>
            <th>Lama</th>
            <th>Stabilitas</th>
            <th>Skor (0-100)</th>
            <th>Kategori</th>
            <th>Versi Model</th>
            <th>Waktu Evaluasi</th>
            <th class="text-center" width="130">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($evaluations)): ?>
            <?php foreach ($evaluations as $ev): ?>
              <tr>
                <td><strong>#<?= $ev['id'] ?></strong></td>
                <td><strong class="text-primary"><?= html_escape($ev['id_umkm']) ?></strong></td>
                <td>
                  <div class="font-weight-600 text-dark"><?= html_escape($ev['nama_umkm']) ?></div>
                  <small class="text-muted"><?= html_escape($ev['bidang_usaha']) ?></small>
                </td>
                <td><small><?= html_escape($ev['kecamatan']) ?></small></td>
                <td>Rp<?= number_format($ev['input_modal'], 0, ',', '.') ?></td>
                <td><?= $ev['input_lama_usaha'] ?> Thn</td>
                <td><?= html_escape($ev['input_stabilitas']) ?></td>
                <td><strong class="font-weight-bold" style="font-size: 1.05rem;"><?= number_format($ev['score'], 2) ?></strong></td>
                <td>
                  <?php if ($ev['category'] === 'Layak'): ?>
                    <span class="badge-soft-success">Layak</span>
                  <?php elseif ($ev['category'] === 'Cukup Layak'): ?>
                    <span class="badge-soft-warning">Cukup Layak</span>
                  <?php else: ?>
                    <span class="badge-soft-danger">Tidak Layak</span>
                  <?php endif; ?>
                </td>
                <td><span class="badge badge-light border">v<?= html_escape($ev['model_version'] ? $ev['model_version'] : '1.0.0') ?></span></td>
                <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($ev['evaluated_at'])) ?></small></td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= base_url('hasil_evaluasi/detail/' . $ev['id']) ?>" class="btn btn-outline-primary" title="Lihat Rincian">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="<?= base_url('hasil_evaluasi/cetak/' . $ev['id']) ?>" target="_blank" class="btn btn-outline-secondary" title="Cetak Lembar">
                      <i class="fas fa-print"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="12" class="text-center py-5 text-muted">
                <i class="fas fa-poll-h fa-3x mb-3 text-light"></i>
                <p class="mb-1">Belum ada riwayat evaluasi yang sesuai dengan kriteria filter.</p>
                <a href="<?= base_url('evaluasi') ?>" class="btn btn-sm btn-outline-primary mt-2">Mulai Evaluasi UMKM</a>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top">
        <div class="text-muted small mb-2 mb-md-0">
          Halaman <strong><?= $current_page ?></strong> dari <strong><?= $total_pages ?></strong> (Total <?= number_format($total_rows) ?> evaluasi)
        </div>
        <nav aria-label="Page navigation">
          <ul class="pagination pagination-sm m-0">
            <?php
              $query_str = $_GET;
              unset($query_str['page']);
              $base_page_url = base_url('hasil_evaluasi?' . http_build_query($query_str) . '&page=');
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
