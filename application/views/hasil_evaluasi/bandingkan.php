<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row mb-3 align-items-center">
  <div class="col-md-7">
    <h4 class="font-weight-bold text-dark mb-1">
      <i class="fas fa-columns mr-2 text-primary"></i> Perbandingan Evaluasi: #<?= $eval1['id'] ?> vs #<?= $eval2['id'] ?>
    </h4>
    <p class="text-muted small mb-0">Analisis komparatif perubahan nilai masukan, derajat keanggotaan, kekuatan aturan, dan skor indeks kelayakan.</p>
  </div>
  <div class="col-md-5 text-md-right mt-3 mt-md-0">
    <a href="<?= base_url('hasil_evaluasi') ?>" class="btn btn-light border">
      <i class="fas fa-arrow-left mr-1"></i> Kembali ke Riwayat
    </a>
  </div>
</div>

<?php if ($eval1['fuzzy_model_id'] != $eval2['fuzzy_model_id']): ?>
  <div class="alert alert-warning shadow-sm mb-4">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    <strong>Catatan Metodologis Lintas Model:</strong> Kedua evaluasi ini menggunakan versi model yang berbeda (v<?= html_escape($eval1['model_version']) ?> vs v<?= html_escape($eval2['model_version']) ?>). Perbandingan hasil harus mempertimbangkan kemungkinan perbedaan basis aturan atau parameter fungsi keanggotaan antarversi.
  </div>
<?php endif; ?>

<div class="row">
  <!-- Evaluasi 1 (A) -->
  <div class="col-md-6 mb-4">
    <div class="card shadow-sm h-100 border-primary">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">Evaluasi A: #<?= $eval1['id'] ?> (<?= html_escape($eval1['nama_umkm']) ?>)</h6>
        <span class="badge badge-light text-primary"><?= date('d/m/Y H:i', strtotime($eval1['evaluated_at'])) ?></span>
      </div>
      <div class="card-body">
        <div class="text-center p-3 mb-3 bg-light rounded border">
          <span class="text-xs text-muted text-uppercase font-weight-bold d-block">Skor Akhir:</span>
          <div class="font-weight-bold" style="font-size: 2.25rem; color: #2563EB;">
            <?= number_format($eval1['score'], 2) ?>
          </div>
          <?php if ($eval1['category'] === 'Layak'): ?>
            <span class="badge-soft-success font-weight-bold px-3 py-1">Layak</span>
          <?php elseif ($eval1['category'] === 'Cukup Layak'): ?>
            <span class="badge-soft-warning font-weight-bold px-3 py-1">Cukup Layak</span>
          <?php else: ?>
            <span class="badge-soft-danger font-weight-bold px-3 py-1">Tidak Layak</span>
          <?php endif; ?>
        </div>

        <h6 class="font-weight-bold text-dark small text-uppercase mb-2">Nilai Masukan:</h6>
        <ul class="list-group list-group-flush small mb-3 border rounded">
          <li class="list-group-item d-flex justify-content-between">
            <span class="text-muted">Modal:</span>
            <strong>Rp<?= number_format($eval1['input_modal'], 0, ',', '.') ?></strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span class="text-muted">Lama Usaha:</span>
            <strong><?= $eval1['input_lama_usaha'] ?> Tahun</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span class="text-muted">Stabilitas Pendapatan:</span>
            <strong><?= html_escape($eval1['input_stabilitas']) ?></strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span class="text-muted">Versi Model:</span>
            <span>v<?= html_escape($eval1['model_version'] ? $eval1['model_version'] : '1.0.0') ?></span>
          </li>
        </ul>

        <?php if (!empty($details1['aggregation']['alpha_cuts'])): ?>
          <h6 class="font-weight-bold text-dark small text-uppercase mb-2">Kekuatan Potong Agregasi (&alpha;):</h6>
          <div class="row text-center small">
            <div class="col-4">
              <span class="badge-soft-danger px-1">Rendah</span>
              <div class="font-weight-bold mt-1"><?= number_format($details1['aggregation']['alpha_cuts']['Rendah'], 4) ?></div>
            </div>
            <div class="col-4">
              <span class="badge-soft-warning px-1">Sedang</span>
              <div class="font-weight-bold mt-1"><?= number_format($details1['aggregation']['alpha_cuts']['Sedang'], 4) ?></div>
            </div>
            <div class="col-4">
              <span class="badge-soft-success px-1">Tinggi</span>
              <div class="font-weight-bold mt-1"><?= number_format($details1['aggregation']['alpha_cuts']['Tinggi'], 4) ?></div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Evaluasi 2 (B) -->
  <div class="col-md-6 mb-4">
    <div class="card shadow-sm h-100 border-info">
      <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">Evaluasi B: #<?= $eval2['id'] ?> (<?= html_escape($eval2['nama_umkm']) ?>)</h6>
        <span class="badge badge-light text-info"><?= date('d/m/Y H:i', strtotime($eval2['evaluated_at'])) ?></span>
      </div>
      <div class="card-body">
        <div class="text-center p-3 mb-3 bg-light rounded border">
          <span class="text-xs text-muted text-uppercase font-weight-bold d-block">Skor Akhir:</span>
          <div class="font-weight-bold" style="font-size: 2.25rem; color: #0284C7;">
            <?= number_format($eval2['score'], 2) ?>
          </div>
          <?php if ($eval2['category'] === 'Layak'): ?>
            <span class="badge-soft-success font-weight-bold px-3 py-1">Layak</span>
          <?php elseif ($eval2['category'] === 'Cukup Layak'): ?>
            <span class="badge-soft-warning font-weight-bold px-3 py-1">Cukup Layak</span>
          <?php else: ?>
            <span class="badge-soft-danger font-weight-bold px-3 py-1">Tidak Layak</span>
          <?php endif; ?>
        </div>

        <h6 class="font-weight-bold text-dark small text-uppercase mb-2">Nilai Masukan:</h6>
        <ul class="list-group list-group-flush small mb-3 border rounded">
          <li class="list-group-item d-flex justify-content-between">
            <span class="text-muted">Modal:</span>
            <strong>Rp<?= number_format($eval2['input_modal'], 0, ',', '.') ?></strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span class="text-muted">Lama Usaha:</span>
            <strong><?= $eval2['input_lama_usaha'] ?> Tahun</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span class="text-muted">Stabilitas Pendapatan:</span>
            <strong><?= html_escape($eval2['input_stabilitas']) ?></strong>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span class="text-muted">Versi Model:</span>
            <span>v<?= html_escape($eval2['model_version'] ? $eval2['model_version'] : '1.0.0') ?></span>
          </li>
        </ul>

        <?php if (!empty($details2['aggregation']['alpha_cuts'])): ?>
          <h6 class="font-weight-bold text-dark small text-uppercase mb-2">Kekuatan Potong Agregasi (&alpha;):</h6>
          <div class="row text-center small">
            <div class="col-4">
              <span class="badge-soft-danger px-1">Rendah</span>
              <div class="font-weight-bold mt-1"><?= number_format($details2['aggregation']['alpha_cuts']['Rendah'], 4) ?></div>
            </div>
            <div class="col-4">
              <span class="badge-soft-warning px-1">Sedang</span>
              <div class="font-weight-bold mt-1"><?= number_format($details2['aggregation']['alpha_cuts']['Sedang'], 4) ?></div>
            </div>
            <div class="col-4">
              <span class="badge-soft-success px-1">Tinggi</span>
              <div class="font-weight-bold mt-1"><?= number_format($details2['aggregation']['alpha_cuts']['Tinggi'], 4) ?></div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Selisih & Ringkasan Perubahan -->
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white">
    <h6 class="card-title m-0"><i class="fas fa-balance-scale mr-2 text-primary"></i> Ringkasan Diferensiasi Hasil</h6>
  </div>
  <div class="card-body">
    <?php
      $diff_score = $eval2['score'] - $eval1['score'];
      $diff_modal = $eval2['input_modal'] - $eval1['input_modal'];
      $diff_lama = $eval2['input_lama_usaha'] - $eval1['input_lama_usaha'];
    ?>
    <div class="row text-center">
      <div class="col-md-4 mb-2">
        <div class="border rounded p-3 bg-light">
          <div class="text-xs text-muted text-uppercase">Perubahan Skor Kelayakan:</div>
          <h4 class="font-weight-bold <?= ($diff_score >= 0) ? 'text-success' : 'text-danger' ?> mt-1 mb-0">
            <?= ($diff_score > 0 ? '+' : '') . number_format($diff_score, 2) ?> Poin
          </h4>
        </div>
      </div>
      <div class="col-md-4 mb-2">
        <div class="border rounded p-3 bg-light">
          <div class="text-xs text-muted text-uppercase">Perubahan Modal:</div>
          <h5 class="font-weight-bold text-dark mt-1 mb-0">
            <?= ($diff_modal > 0 ? '+' : '') ?>Rp<?= number_format($diff_modal, 0, ',', '.') ?>
          </h5>
        </div>
      </div>
      <div class="col-md-4 mb-2">
        <div class="border rounded p-3 bg-light">
          <div class="text-xs text-muted text-uppercase">Perubahan Lama Usaha:</div>
          <h5 class="font-weight-bold text-dark mt-1 mb-0">
            <?= ($diff_lama > 0 ? '+' : '') . number_format($diff_lama, 1) ?> Tahun
          </h5>
        </div>
      </div>
    </div>
  </div>
</div>
