<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row mb-3 align-items-center">
  <div class="col-md-7">
    <h4 class="font-weight-bold text-dark mb-1">
      <?= html_escape($umkm['nama_umkm']) ?>
      <span class="badge badge-light border text-primary ml-2"><?= html_escape($umkm['id_umkm']) ?></span>
    </h4>
    <p class="text-muted small mb-0">
      <i class="fas fa-map-marker-alt text-danger mr-1"></i> Kecamatan <?= html_escape($umkm['kecamatan']) ?>, Desa/Kel. <?= html_escape($umkm['desa_kelurahan']) ?>
      &bull; Status Usaha: <span class="badge badge-light border"><?= html_escape($umkm['status_usaha']) ?></span>
    </p>
  </div>
  <div class="col-md-5 text-md-right mt-3 mt-md-0">
    <a href="<?= base_url('evaluasi?umkm_id=' . $umkm['id']) ?>" class="btn btn-primary shadow-sm mr-1">
      <i class="fas fa-calculator mr-1"></i> Evaluasi Sekarang
    </a>
    <a href="<?= base_url('umkm/ubah/' . $umkm['id']) ?>" class="btn btn-warning mr-1">
      <i class="fas fa-edit mr-1"></i> Ubah
    </a>
    <a href="<?= base_url('umkm') ?>" class="btn btn-light border">
      <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
  </div>
</div>

<div class="row">
  <!-- Profil & Variabel Input Fuzzy -->
  <div class="col-lg-6 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-cogs mr-2 text-primary"></i> 3 Parameter Masukan Model Fuzzy Mamdani (v1.0)</h6>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-sm-5 text-muted small font-weight-bold text-uppercase">1. Modal / Investasi:</div>
          <div class="col-sm-7">
            <h5 class="font-weight-bold text-primary mb-0">Rp<?= number_format($umkm['nilai_investasi'], 0, ',', '.') ?></h5>
            <small class="text-muted">Domain prototipe: Rp500.000 s.d. Rp900.000.000</small>
          </div>
        </div>
        <hr class="my-2">
        <div class="row mb-3">
          <div class="col-sm-5 text-muted small font-weight-bold text-uppercase">2. Lama Usaha:</div>
          <div class="col-sm-7">
            <h5 class="font-weight-bold text-dark mb-0"><?= $umkm['lama_usaha'] ?> Tahun</h5>
            <small class="text-muted">Domain prototipe: 1 s.d. 15 Tahun</small>
          </div>
        </div>
        <hr class="my-2">
        <div class="row">
          <div class="col-sm-5 text-muted small font-weight-bold text-uppercase">3. Stabilitas Pendapatan:</div>
          <div class="col-sm-7">
            <?php if ($umkm['stabilitas_pendapatan'] === 'Stabil'): ?>
              <span class="badge-soft-success px-3 py-1 font-weight-bold">Stabil</span>
            <?php elseif ($umkm['stabilitas_pendapatan'] === 'Cukup stabil'): ?>
              <span class="badge-soft-warning px-3 py-1 font-weight-bold">Cukup stabil</span>
            <?php else: ?>
              <span class="badge-soft-danger px-3 py-1 font-weight-bold">Fluktuatif</span>
            <?php endif; ?>
            <div class="text-muted small mt-1">Kategori tegas prototipe (Singleton)</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Data Keuangan Lainnya (Penelitian Sumber) -->
  <div class="col-lg-6 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-coins mr-2 text-info"></i> Variabel Keuangan Lainnya (Dataset Sumber)</h6>
      </div>
      <div class="card-body">
        <div class="row mb-2">
          <div class="col-6 text-muted small">Nilai Omzet (Rp):</div>
          <div class="col-6 font-weight-bold text-dark">Rp<?= number_format($umkm['nilai_omzet'], 0, ',', '.') ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-6 text-muted small">Nilai Laba (Rp):</div>
          <div class="col-6 font-weight-bold text-dark">Rp<?= number_format($umkm['nilai_laba'], 0, ',', '.') ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-6 text-muted small">Biaya Operasional:</div>
          <div class="col-6 font-weight-bold text-dark">Rp<?= number_format($umkm['biaya_operasional'], 0, ',', '.') ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-6 text-muted small">Arus Kas Bersih:</div>
          <div class="col-6 font-weight-bold text-dark">Rp<?= number_format($umkm['arus_kas_bersih'], 0, ',', '.') ?></div>
        </div>
        <hr class="my-2">
        <div class="alert alert-info py-2 px-3 small mb-0">
          <i class="fas fa-info-circle mr-1"></i> <strong>Catatan Metodologis:</strong> Variabel keuangan tambahan dipertahankan dari data sumber penelitian dan tidak otomatis dimasukkan ke dalam inferensi fuzzy v1.0 guna mencegah redundansi dan multikolinearitas.
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Riwayat Evaluasi Kelayakan UMKM ini -->
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white d-flex align-items-center justify-content-between">
    <h6 class="card-title m-0"><i class="fas fa-poll-h mr-2 text-primary"></i> Riwayat Evaluasi Kelayakan Usaha</h6>
    <a href="<?= base_url('evaluasi?umkm_id=' . $umkm['id']) ?>" class="btn btn-sm btn-primary">
      <i class="fas fa-plus mr-1"></i> Lakukan Evaluasi Baru
    </a>
  </div>
  <div class="card-body p-0">
    <?php if (!empty($evaluations)): ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>ID Evaluasi</th>
              <th>Waktu Evaluasi</th>
              <th>Versi Model</th>
              <th>Modal Masukan</th>
              <th>Lama Usaha</th>
              <th>Stabilitas</th>
              <th>Skor Akhir (0-100)</th>
              <th>Kategori Kelayakan</th>
              <th>Evaluator</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($evaluations as $ev): ?>
              <tr>
                <td><strong>#<?= $ev['id'] ?></strong></td>
                <td><?= date('d/m/Y H:i', strtotime($ev['evaluated_at'])) ?></td>
                <td><span class="badge badge-light border">v<?= html_escape($ev['model_version'] ? $ev['model_version'] : '1.0.0') ?></span></td>
                <td>Rp<?= number_format($ev['input_modal'], 0, ',', '.') ?></td>
                <td><?= $ev['input_lama_usaha'] ?> Thn</td>
                <td><?= html_escape($ev['input_stabilitas']) ?></td>
                <td><strong class="font-weight-bold"><?= number_format($ev['score'], 2) ?></strong></td>
                <td>
                  <?php if ($ev['category'] === 'Layak'): ?>
                    <span class="badge-soft-success">Layak</span>
                  <?php elseif ($ev['category'] === 'Cukup Layak'): ?>
                    <span class="badge-soft-warning">Cukup Layak</span>
                  <?php else: ?>
                    <span class="badge-soft-danger">Tidak Layak</span>
                  <?php endif; ?>
                </td>
                <td><small class="text-muted"><?= html_escape($ev['evaluator_name'] ? $ev['evaluator_name'] : 'Sistem') ?></small></td>
                <td class="text-center">
                  <a href="<?= base_url('hasil_evaluasi/detail/' . $ev['id']) ?>" class="btn btn-sm btn-outline-primary" title="Lihat Rincian Inferensi">
                    <i class="fas fa-eye mr-1"></i> Rincian
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="text-center py-5 text-muted">
        <i class="fas fa-calculator fa-3x mb-3 text-light"></i>
        <p class="mb-2">UMKM ini belum pernah dievaluasi menggunakan metode Fuzzy Mamdani.</p>
        <a href="<?= base_url('evaluasi?umkm_id=' . $umkm['id']) ?>" class="btn btn-primary btn-sm">
          <i class="fas fa-play mr-1"></i> Mulai Evaluasi Kelayakan
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>
