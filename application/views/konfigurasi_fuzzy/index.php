<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row mb-3 align-items-center">
  <div class="col-md-7">
    <h4 class="font-weight-bold text-dark mb-1">
      <i class="fas fa-sliders-h mr-2 text-primary"></i> Konfigurasi Parameter Model Fuzzy Mamdani
    </h4>
    <p class="text-muted small mb-0">Spesifikasi formal fungsi keanggotaan, operator inferensi, dan 27 aturan prototipe v1.0.</p>
  </div>
  <div class="col-md-5 text-md-right mt-3 mt-md-0">
    <div class="dropdown d-inline-block">
      <button class="btn btn-outline-primary dropdown-toggle shadow-sm" type="button" id="modelDropdown" data-toggle="dropdown">
        <i class="fas fa-code-branch mr-1"></i> Versi: <strong><?= html_escape($model['model_code']) ?> (v<?= html_escape($model['version']) ?>)</strong>
      </button>
      <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
        <h6 class="dropdown-header">Pilih Versi Model:</h6>
        <?php foreach ($all_models as $m): ?>
          <a class="dropdown-item <?= ($m['id'] == $model['id']) ? 'active' : '' ?>" href="<?= base_url('konfigurasi_fuzzy?model_id=' . $m['id']) ?>">
            <?= html_escape($m['model_code']) ?> (v<?= html_escape($m['version']) ?>)
            <?= ($m['is_active'] == 1) ? '<span class="badge badge-success ml-1">Aktif</span>' : '' ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- Banner Model Info -->
<div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%); color: #FFFFFF;">
  <div class="card-body p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <span class="badge badge-light text-primary font-weight-bold px-2 py-1 mb-2">
          <?= html_escape($model['method']) ?> &bull; Status: <?= ($model['is_active'] == 1) ? 'Default Aktif' : 'Arsip Versi' ?>
        </span>
        <h4 class="font-weight-bold mb-1"><?= html_escape($model['model_name']) ?></h4>
        <p class="text-white-50 mb-0 small">
          Kode Model: <code><?= html_escape($model['model_code']) ?></code> &bull;
          Dibuat: <?= date('d F Y, H:i', strtotime($model['created_at'])) ?>
        </p>
      </div>
      <div class="text-md-right mt-3 mt-md-0">
        <?php if ($model['is_active'] != 1 && $is_admin): ?>
          <a href="<?= base_url('konfigurasi_fuzzy/activate/' . $model['id']) ?>" class="btn btn-success shadow" onclick="return confirm('Aktifkan model ini sebagai model evaluasi default?');">
            <i class="fas fa-check-circle mr-1"></i> Aktifkan Model Ini
          </a>
        <?php else: ?>
          <span class="badge badge-success px-3 py-2 font-weight-bold"><i class="fas fa-check mr-1"></i> Model Sedang Aktif</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Grid Spesifikasi Fungsi Keanggotaan -->
<div class="row">
  <!-- Input 1: Modal -->
  <div class="col-lg-4 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-wallet mr-2 text-primary"></i> Input 1: Modal / Investasi</h6>
      </div>
      <div class="card-body small">
        <div class="mb-2 text-muted">Domain Prototipe: <strong>Rp500.000 s.d. Rp900.000.000</strong></div>
        <hr class="my-2">
        <div class="mb-2">
          <strong>1. Rendah</strong> (Trapesium Bahu Kiri):
          <div><code>(500.000, 500.000, 5.000.000, 14.000.000)</code></div>
        </div>
        <div class="mb-2">
          <strong>2. Sedang</strong> (Segitiga):
          <div><code>(5.000.000, 14.000.000, 50.000.000)</code></div>
        </div>
        <div>
          <strong>3. Tinggi</strong> (Trapesium Bahu Kanan):
          <div><code>(14.000.000, 50.000.000, 900.000.000, 900.000.000)</code></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Input 2: Lama Usaha -->
  <div class="col-lg-4 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-clock mr-2 text-primary"></i> Input 2: Lama Usaha</h6>
      </div>
      <div class="card-body small">
        <div class="mb-2 text-muted">Domain Prototipe: <strong>1 s.d. 15 Tahun</strong></div>
        <hr class="my-2">
        <div class="mb-2">
          <strong>1. Pendek</strong> (Trapesium Bahu Kiri):
          <div><code>(1, 1, 4, 8)</code></div>
        </div>
        <div class="mb-2">
          <strong>2. Menengah</strong> (Segitiga):
          <div><code>(4, 8, 11)</code></div>
        </div>
        <div>
          <strong>3. Panjang</strong> (Trapesium Bahu Kanan):
          <div><code>(8, 11, 15, 15)</code></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Input 3: Stabilitas -->
  <div class="col-lg-4 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-balance-scale mr-2 text-primary"></i> Input 3: Stabilitas Pendapatan</h6>
      </div>
      <div class="card-body small">
        <div class="mb-2 text-muted">Domain Prototipe: <strong>3 Kategori Tegas (Singleton)</strong></div>
        <hr class="my-2">
        <div class="mb-2">
          <strong>1. Fluktuatif</strong>: Level 0
          <div><code>&mu; = 1.0 jika Fluktuatif, 0.0 lainnya</code></div>
        </div>
        <div class="mb-2">
          <strong>2. Cukup stabil</strong>: Level 1
          <div><code>&mu; = 1.0 jika Cukup stabil, 0.0 lainnya</code></div>
        </div>
        <div>
          <strong>3. Stabil</strong>: Level 2
          <div><code>&mu; = 1.0 jika Stabil, 0.0 lainnya</code></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Output & Batas Kategori -->
<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-poll mr-2 text-primary"></i> Variabel Output: Indeks Kelayakan Usaha</h6>
      </div>
      <div class="card-body small">
        <div class="mb-2 text-muted">Domain Output: <strong>Skala 0 s.d. 100</strong></div>
        <hr class="my-2">
        <div class="mb-2">
          <strong>1. Rendah</strong>: Trapesium Bahu Kiri <code>(0, 0, 30, 50)</code>
        </div>
        <div class="mb-2">
          <strong>2. Sedang</strong>: Segitiga Simetris <code>(30, 50, 70)</code>
        </div>
        <div class="mb-3">
          <strong>3. Tinggi</strong>: Trapesium Bahu Kanan <code>(50, 70, 100, 100)</code>
        </div>
        <div class="alert alert-light border small mb-0">
          <strong>Operator Inferensi:</strong> AND = <code>MIN</code>, Implikasi = <code>MIN</code>, Agregasi = <code>MAX</code>, Defuzzifikasi = <code>Centroid (COG)</code> dengan resolusi partisi numerik diskret &Delta;z = 0.1.
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-tags mr-2 text-primary"></i> Batas Keputusan Kategori Kelayakan</h6>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center mb-3 p-2 rounded bg-light border">
          <span class="badge-soft-danger px-3 py-2 font-weight-bold mr-3" style="min-width: 120px;">Tidak Layak</span>
          <div>
            <strong class="text-dark">Skor &lt; 40.0</strong>
            <div class="text-muted small">Usaha dinilai belum layak pembiayaan eksternal dan memerlukan pendampingan model bisnis.</div>
          </div>
        </div>

        <div class="d-flex align-items-center mb-3 p-2 rounded bg-light border">
          <span class="badge-soft-warning px-3 py-2 font-weight-bold mr-3" style="min-width: 120px;">Cukup Layak</span>
          <div>
            <strong class="text-dark">Skor 40.0 s.d. &lt; 70.0</strong>
            <div class="text-muted small">Usaha memiliki potensi keberlanjutan namun memerlukan perkuatan stabilitas omzet atau manajemen kas.</div>
          </div>
        </div>

        <div class="d-flex align-items-center p-2 rounded bg-light border">
          <span class="badge-soft-success px-3 py-2 font-weight-bold mr-3" style="min-width: 120px;">Layak</span>
          <div>
            <strong class="text-dark">Skor 70.0 s.d. 100.0</strong>
            <div class="text-muted small">Usaha memenuhi profil kelayakan permodalan dan operasional untuk diprioritaskan dalam program pembiayaan.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Basis Aturan 27 Kombinasi Lengkap -->
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white d-flex align-items-center justify-content-between">
    <h6 class="card-title m-0">
      <i class="fas fa-list-ol mr-2 text-primary"></i> Basis Aturan Inferensi (27 Kombinasi Eksplisit)
    </h6>
    <span class="badge badge-light border">Level: Rendah=0, Sedang=1, Tinggi=2 &bull; Total: 0-1 (Rendah), 2-4 (Sedang), 5-6 (Tinggi)</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead class="bg-light">
          <tr>
            <th width="50">No</th>
            <th>Modal Usaha</th>
            <th>Lama Usaha</th>
            <th>Stabilitas Pendapatan</th>
            <th>Total Level</th>
            <th>Keputusan Konsekuen (Output)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rule_base as $r): ?>
            <tr>
              <td><strong>R<?= sprintf('%02d', $r['rule_no']) ?></strong></td>
              <td><?= html_escape($r['modal']) ?> <small class="text-muted">(Lvl <?= $r['modal_level'] ?>)</small></td>
              <td><?= html_escape($r['lama_usaha']) ?> <small class="text-muted">(Lvl <?= $r['lama_level'] ?>)</small></td>
              <td><?= html_escape($r['stabilitas']) ?> <small class="text-muted">(Lvl <?= $r['stabilitas_level'] ?>)</small></td>
              <td><strong><?= $r['total_level'] ?></strong></td>
              <td>
                <?php if ($r['output'] === 'Tinggi' || $r['output'] === 'Layak'): ?>
                  <span class="badge-soft-success">Tinggi (Layak)</span>
                <?php elseif ($r['output'] === 'Sedang' || $r['output'] === 'Cukup Layak'): ?>
                  <span class="badge-soft-warning">Sedang (Cukup Layak)</span>
                <?php else: ?>
                  <span class="badge-soft-danger">Rendah (Tidak Layak)</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
