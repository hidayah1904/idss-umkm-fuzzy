<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Header Ringkasan Keputusan -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%); color: #FFFFFF;">
      <div class="card-body p-4">
        <div class="row align-items-center">
          <div class="col-lg-8 mb-3 mb-lg-0">
            <span class="badge badge-light px-2 py-1 mb-2 font-weight-bold text-primary">
              <i class="fas fa-brain mr-1"></i> <?= html_escape($eval_result['model']['method']) ?> &bull; <?= html_escape($eval_result['model']['version']) ?>
            </span>
            <h3 class="font-weight-bold mb-1">
              <?php if (!empty($umkm)): ?>
                <?= html_escape($umkm['nama_umkm']) ?> <small class="text-white-50">(<?= html_escape($umkm['id_umkm']) ?>)</small>
              <?php else: ?>
                Mode Simulasi Mandiri <small class="text-white-50">(Tanpa Terhubung Database)</small>
              <?php endif; ?>
            </h3>
            <p class="text-white-50 mb-0 small">
              <i class="fas fa-calendar-alt mr-1"></i> Waktu Evaluasi: <?= date('d F Y, H:i:s', strtotime($eval_result['timestamp'])) ?>
              <?php if (!empty($umkm)): ?>
                &bull; Bidang: <?= html_escape($umkm['bidang_usaha']) ?> (<?= html_escape($umkm['kecamatan']) ?>)
              <?php endif; ?>
            </p>
          </div>

          <div class="col-lg-4 text-lg-right">
            <div class="bg-white p-3 rounded text-center shadow" style="display: inline-block; min-width: 220px;">
              <span class="text-xs font-weight-bold text-muted text-uppercase d-block mb-1">Skor Indeks Kelayakan</span>
              <div class="font-weight-bold mb-1" style="font-size: 2.75rem; color: <?= $eval_result['result']['category_color'] ?>; line-height: 1;">
                <?= number_format($eval_result['result']['score'], 2) ?>
              </div>
              <span class="badge" style="background-color: <?= $eval_result['result']['category_color'] ?>; color: #FFFFFF; font-size: 1rem; padding: 0.4em 0.8em; border-radius: 8px;">
                <?= html_escape($eval_result['result']['category']) ?>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Catatan Interpretasi & Rekomendasi Sistem -->
<div class="card shadow-sm mb-4" style="border-left: 5px solid <?= $eval_result['result']['category_color'] ?>;">
  <div class="card-body p-3">
    <div class="d-flex align-items-start">
      <div class="mr-3 mt-1" style="color: <?= $eval_result['result']['category_color'] ?>; font-size: 1.5rem;">
        <i class="fas fa-comment-alt"></i>
      </div>
      <div>
        <h6 class="font-weight-bold mb-1 text-dark">Interpretasi Penalaran Fuzzy & Rekomendasi</h6>
        <p class="mb-0 text-muted" style="line-height: 1.6;"><?= html_escape($eval_result['result']['interpretation']) ?></p>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($eval_result['warnings'])): ?>
  <div class="alert alert-warning mb-4 shadow-sm">
    <h6 class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Peringatan Batas Domain:</h6>
    <ul class="mb-0 pl-3">
      <?php foreach ($eval_result['warnings'] as $w): ?>
        <li><?= html_escape($w) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<!-- Step 1 & 2: Fuzzifikasi (Derajat Keanggotaan Setiap Input) -->
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white">
    <h6 class="card-title m-0">
      <i class="fas fa-chart-bar mr-2 text-primary"></i> Tahap 1: Fuzzifikasi Variabel Masukan (Derajat Keanggotaan &mu;)
    </h6>
  </div>
  <div class="card-body">
    <div class="row">
      <!-- Input 1: Modal -->
      <div class="col-md-4 mb-3">
        <div class="border rounded p-3 h-100 bg-light">
          <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Masukan 1: Modal / Investasi</div>
          <h5 class="font-weight-bold text-primary mb-2">Rp<?= number_format($eval_result['inputs']['modal'], 0, ',', '.') ?></h5>
          <div class="small">
            <div class="d-flex justify-content-between mb-1">
              <span>&bull; Rendah (&le;14 jt):</span>
              <strong>&mu; = <?= number_format($eval_result['fuzzification']['modal']['Rendah'], 4) ?></strong>
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span>&bull; Sedang (5 - 50 jt):</span>
              <strong>&mu; = <?= number_format($eval_result['fuzzification']['modal']['Sedang'], 4) ?></strong>
            </div>
            <div class="d-flex justify-content-between">
              <span>&bull; Tinggi (&ge;14 jt):</span>
              <strong>&mu; = <?= number_format($eval_result['fuzzification']['modal']['Tinggi'], 4) ?></strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Input 2: Lama Usaha -->
      <div class="col-md-4 mb-3">
        <div class="border rounded p-3 h-100 bg-light">
          <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Masukan 2: Lama Usaha</div>
          <h5 class="font-weight-bold text-dark mb-2"><?= $eval_result['inputs']['lama_usaha'] ?> Tahun</h5>
          <div class="small">
            <div class="d-flex justify-content-between mb-1">
              <span>&bull; Pendek (&le;8 th):</span>
              <strong>&mu; = <?= number_format($eval_result['fuzzification']['lama_usaha']['Pendek'], 4) ?></strong>
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span>&bull; Menengah (4 - 11 th):</span>
              <strong>&mu; = <?= number_format($eval_result['fuzzification']['lama_usaha']['Menengah'], 4) ?></strong>
            </div>
            <div class="d-flex justify-content-between">
              <span>&bull; Panjang (&ge;8 th):</span>
              <strong>&mu; = <?= number_format($eval_result['fuzzification']['lama_usaha']['Panjang'], 4) ?></strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Input 3: Stabilitas -->
      <div class="col-md-4 mb-3">
        <div class="border rounded p-3 h-100 bg-light">
          <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Masukan 3: Stabilitas Pendapatan</div>
          <h5 class="font-weight-bold text-dark mb-2"><?= html_escape($eval_result['inputs']['stabilitas']) ?></h5>
          <div class="small">
            <div class="d-flex justify-content-between mb-1">
              <span>&bull; Fluktuatif (Level 0):</span>
              <strong>&mu; = <?= number_format($eval_result['fuzzification']['stabilitas']['Fluktuatif'], 1) ?></strong>
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span>&bull; Cukup stabil (Level 1):</span>
              <strong>&mu; = <?= number_format($eval_result['fuzzification']['stabilitas']['Cukup stabil'], 1) ?></strong>
            </div>
            <div class="d-flex justify-content-between">
              <span>&bull; Stabil (Level 2):</span>
              <strong>&mu; = <?= number_format($eval_result['fuzzification']['stabilitas']['Stabil'], 1) ?></strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Step 3 & 4: Visualisasi Poligon Agregasi Output (Chart.js) -->
<div class="row">
  <div class="col-lg-7 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="card-title m-0">
          <i class="fas fa-wave-square mr-2 text-primary"></i> Tahap 3: Himpunan Output Teragregasi (MAX-MIN)
        </h6>
        <span class="badge badge-light border">Centroid: <?= number_format($eval_result['result']['score'], 2) ?></span>
      </div>
      <div class="card-body">
        <div style="position: relative; height: 260px;">
          <canvas id="outputCurveChart"></canvas>
        </div>
        <div class="d-flex justify-content-around text-center small mt-3 pt-2 border-top">
          <div>
            <span class="badge-soft-danger px-2 py-1">Potong Rendah (&alpha;)</span>
            <div class="font-weight-bold mt-1"><?= number_format($eval_result['aggregation']['alpha_cuts']['Rendah'], 4) ?></div>
          </div>
          <div>
            <span class="badge-soft-warning px-2 py-1">Potong Sedang (&alpha;)</span>
            <div class="font-weight-bold mt-1"><?= number_format($eval_result['aggregation']['alpha_cuts']['Sedang'], 4) ?></div>
          </div>
          <div>
            <span class="badge-soft-success px-2 py-1">Potong Tinggi (&alpha;)</span>
            <div class="font-weight-bold mt-1"><?= number_format($eval_result['aggregation']['alpha_cuts']['Tinggi'], 4) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Step 5: Detail Perhitungan Defuzzifikasi Centroid -->
  <div class="col-lg-5 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white">
        <h6 class="card-title m-0">
          <i class="fas fa-balance-scale mr-2 text-primary"></i> Tahap 4: Defuzzifikasi Centroid (COG)
        </h6>
      </div>
      <div class="card-body">
        <div class="bg-light p-3 rounded border text-center mb-3">
          <div class="text-xs text-muted text-uppercase font-weight-bold mb-1">Rumus Titik Berat (Centroid):</div>
          <div class="font-italic text-dark font-weight-bold" style="font-size: 1.1rem;">
            z* = &int; (z &bull; &mu;(z)) dz / &int; &mu;(z) dz
          </div>
        </div>

        <table class="table table-sm table-borderless small mb-3">
          <tr>
            <td class="text-muted">Partisi Diskret (&Delta;z):</td>
            <td class="text-right font-weight-bold">0.1 (1001 Titik)</td>
          </tr>
          <tr>
            <td class="text-muted">Total Momen Numerik (&sum; z &bull; &mu;):</td>
            <td class="text-right font-weight-bold"><?= number_format($eval_result['aggregation']['sum_moment'], 2) ?></td>
          </tr>
          <tr>
            <td class="text-muted">Total Luas Area Numerik (&sum; &mu;):</td>
            <td class="text-right font-weight-bold"><?= number_format($eval_result['aggregation']['sum_area'], 2) ?></td>
          </tr>
          <tr class="border-top">
            <td class="font-weight-bold text-dark">Skor Centroid (z*):</td>
            <td class="text-right font-weight-bold text-primary" style="font-size: 1.15rem;"><?= number_format($eval_result['result']['score'], 2) ?></td>
          </tr>
        </table>

        <div class="p-2 rounded bg-light border small text-muted">
          <strong>Klasifikasi Kategori:</strong>
          <ul class="mb-0 pl-3">
            <li>Skor &lt; 40 : <span class="badge-soft-danger px-1">Tidak Layak</span></li>
            <li>Skor 40 s.d. &lt; 70 : <span class="badge-soft-warning px-1">Cukup Layak</span></li>
            <li>Skor 70 s.d. 100 : <span class="badge-soft-success px-1">Layak</span></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Step 2: Rincian 27 Aturan Inferensi Mamdani & Kekuatan Aktivasi -->
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white d-flex align-items-center justify-content-between">
    <h6 class="card-title m-0">
      <i class="fas fa-list-ol mr-2 text-primary"></i> Tahap 2: Evaluasi 27 Aturan Inferensi Mamdani (MIN-MAX)
    </h6>
    <span class="badge badge-primary px-3 py-1">
      <?= $eval_result['rules']['active_count'] ?> dari 27 Aturan Aktif (&alpha; &gt; 0)
    </span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive" style="max-height: 380px;">
      <table class="table table-sm table-hover mb-0">
        <thead class="bg-light sticky-top">
          <tr>
            <th width="50">No</th>
            <th>Modal</th>
            <th>Lama Usaha</th>
            <th>Stabilitas</th>
            <th>Formula MIN(&mu;<sub>m</sub>, &mu;<sub>l</sub>, &mu;<sub>s</sub>)</th>
            <th>Aktivasi (&alpha;)</th>
            <th>Konsekuen (Output)</th>
            <th class="text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($eval_result['rules']['all'] as $r): ?>
            <tr class="<?= $r['is_active'] ? 'bg-light font-weight-bold' : 'text-muted' ?>">
              <td>R<?= sprintf('%02d', $r['rule_no']) ?></td>
              <td><?= $r['modal'] ?> (<?= number_format($r['mu_modal'], 2) ?>)</td>
              <td><?= $r['lama_usaha'] ?> (<?= number_format($r['mu_lama_usaha'], 2) ?>)</td>
              <td><?= $r['stabilitas'] ?> (<?= number_format($r['mu_stabilitas'], 2) ?>)</td>
              <td><code>MIN(<?= number_format($r['mu_modal'], 2) ?>, <?= number_format($r['mu_lama_usaha'], 2) ?>, <?= number_format($r['mu_stabilitas'], 2) ?>)</code></td>
              <td><strong class="<?= $r['is_active'] ? 'text-primary' : '' ?>"><?= number_format($r['alpha'], 4) ?></strong></td>
              <td>
                <?php if ($r['output'] === 'Layak' || $r['output'] === 'Tinggi'): ?>
                  <span class="badge-soft-success">Tinggi / Layak</span>
                <?php elseif ($r['output'] === 'Cukup Layak' || $r['output'] === 'Sedang'): ?>
                  <span class="badge-soft-warning">Sedang / Cukup</span>
                <?php else: ?>
                  <span class="badge-soft-danger">Rendah / Tidak</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <?php if ($r['is_active']): ?>
                  <span class="badge badge-success"><i class="fas fa-check"></i> Aktif</span>
                <?php else: ?>
                  <span class="badge badge-light border">0.00</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Aksi Lanjutan -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <a href="<?= base_url('evaluasi') ?>" class="btn btn-outline-primary mr-2 mb-1">
      <i class="fas fa-redo mr-1"></i> Evaluasi Ulang / Usaha Lain
    </a>
    <?php if (!empty($umkm)): ?>
      <a href="<?= base_url('umkm/detail/' . $umkm['id']) ?>" class="btn btn-light border mb-1">
        <i class="fas fa-store mr-1"></i> Buka Profil UMKM
      </a>
    <?php endif; ?>
  </div>

  <div>
    <?php if (!empty($saved_id)): ?>
      <a href="<?= base_url('hasil_evaluasi/cetak/' . $saved_id) ?>" target="_blank" class="btn btn-success shadow-sm mb-1">
        <i class="fas fa-print mr-1"></i> Cetak Lembar Hasil Evaluasi
      </a>
    <?php endif; ?>
  </div>
</div>

<!-- Script Chart.js Kurva Agregasi Output -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const samples = <?= json_encode($eval_result['aggregation']['chart_samples']) ?>;
  const labels = samples.map(s => s.z);
  const dataAgg = samples.map(s => s.mu_aggregated);
  const dataRendah = samples.map(s => s.cut_rendah);
  const dataSedang = samples.map(s => s.cut_sedang);
  const dataTinggi = samples.map(s => s.cut_tinggi);

  const ctx = document.getElementById('outputCurveChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Area Teragregasi (MAX)',
          data: dataAgg,
          borderColor: '#2563EB',
          backgroundColor: 'rgba(37, 99, 235, 0.25)',
          fill: true,
          tension: 0.1,
          borderWidth: 2,
          pointRadius: 0
        },
        {
          label: 'Potong Rendah',
          data: dataRendah,
          borderColor: '#EF4444',
          borderDash: [4, 4],
          borderWidth: 1.5,
          fill: false,
          pointRadius: 0
        },
        {
          label: 'Potong Sedang',
          data: dataSedang,
          borderColor: '#F59E0B',
          borderDash: [4, 4],
          borderWidth: 1.5,
          fill: false,
          pointRadius: 0
        },
        {
          label: 'Potong Tinggi',
          data: dataTinggi,
          borderColor: '#10B981',
          borderDash: [4, 4],
          borderWidth: 1.5,
          fill: false,
          pointRadius: 0
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          title: { display: true, text: 'Domain Skor Output (0 - 100)' },
          ticks: { stepSize: 10 }
        },
        y: {
          title: { display: true, text: 'Derajat Keanggotaan (\u03BC)' },
          min: 0,
          max: 1.05
        }
      },
      plugins: {
        legend: { position: 'top' }
      }
    }
  });
});
</script>
