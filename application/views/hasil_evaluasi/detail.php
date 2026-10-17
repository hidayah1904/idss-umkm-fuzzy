<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row mb-3 align-items-center">
  <div class="col-md-7">
    <h4 class="font-weight-bold text-dark mb-1">
      Rincian Hasil Evaluasi #<?= $eval['id'] ?>
      <span class="badge badge-light border text-primary ml-2"><?= html_escape($eval['id_umkm']) ?></span>
    </h4>
    <p class="text-muted small mb-0">
      UMKM: <strong><?= html_escape($eval['nama_umkm']) ?></strong> &bull;
      Waktu: <?= date('d F Y, H:i:s', strtotime($eval['evaluated_at'])) ?> &bull;
      Model: <span class="badge badge-light border">v<?= html_escape($eval['model_version'] ? $eval['model_version'] : '1.0.0') ?></span>
    </p>
  </div>
  <div class="col-md-5 text-md-right mt-3 mt-md-0">
    <a href="<?= base_url('hasil_evaluasi/cetak/' . $eval['id']) ?>" target="_blank" class="btn btn-success shadow-sm mr-1">
      <i class="fas fa-print mr-1"></i> Cetak Lembar Hasil
    </a>
    <a href="<?= base_url('hasil_evaluasi') ?>" class="btn btn-light border">
      <i class="fas fa-arrow-left mr-1"></i> Kembali ke Riwayat
    </a>
  </div>
</div>

<!-- Score Card Banner -->
<div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%); color: #FFFFFF;">
  <div class="card-body p-4">
    <div class="row align-items-center">
      <div class="col-md-8">
        <span class="badge badge-light text-primary font-weight-bold px-2 py-1 mb-2">
          <?= html_escape(isset($details['model']['method']) ? $details['model']['method'] : 'Fuzzy Mamdani') ?>
        </span>
        <h3 class="font-weight-bold mb-1"><?= html_escape($eval['nama_umkm']) ?></h3>
        <p class="text-white-50 mb-0 small">
          Modal: Rp<?= number_format($eval['input_modal'], 0, ',', '.') ?> &bull;
          Lama Usaha: <?= $eval['input_lama_usaha'] ?> Tahun &bull;
          Stabilitas: <?= html_escape($eval['input_stabilitas']) ?>
        </p>
      </div>

      <div class="col-md-4 text-md-right mt-3 mt-md-0">
        <div class="bg-white p-3 rounded text-center shadow" style="display: inline-block; min-width: 200px;">
          <span class="text-xs font-weight-bold text-muted text-uppercase d-block mb-1">Skor Indeks Kelayakan</span>
          <div class="font-weight-bold mb-1" style="font-size: 2.75rem; color: <?= isset($details['result']['category_color']) ? $details['result']['category_color'] : '#2563EB' ?>; line-height: 1;">
            <?= number_format($eval['score'], 2) ?>
          </div>
          <?php if ($eval['category'] === 'Layak'): ?>
            <span class="badge-soft-success font-weight-bold px-3 py-1">Layak</span>
          <?php elseif ($eval['category'] === 'Cukup Layak'): ?>
            <span class="badge-soft-warning font-weight-bold px-3 py-1">Cukup Layak</span>
          <?php else: ?>
            <span class="badge-soft-danger font-weight-bold px-3 py-1">Tidak Layak</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Interpretasi & Rekomendasi -->
<?php if (!empty($details['result']['interpretation'])): ?>
  <div class="card shadow-sm mb-4" style="border-left: 5px solid <?= isset($details['result']['category_color']) ? $details['result']['category_color'] : '#2563EB' ?>;">
    <div class="card-body p-3">
      <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-comment-alt mr-1 text-primary"></i> Interpretasi & Rekomendasi Hasil Evaluasi</h6>
      <p class="mb-0 text-muted" style="line-height: 1.6;"><?= html_escape($details['result']['interpretation']) ?></p>
    </div>
  </div>
<?php endif; ?>

<!-- Step Trace: Fuzzifikasi -->
<?php if (!empty($details['fuzzification'])): ?>
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white">
    <h6 class="card-title m-0"><i class="fas fa-chart-bar mr-2 text-primary"></i> Snapshot Fuzzifikasi (Derajat Keanggotaan &mu;)</h6>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-4 mb-2">
        <div class="border rounded p-3 bg-light">
          <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Modal (Rp<?= number_format($eval['input_modal'], 0, ',', '.') ?>)</div>
          <div class="small">
            <div>&bull; Rendah: <strong>&mu; = <?= number_format($details['fuzzification']['modal']['Rendah'], 4) ?></strong></div>
            <div>&bull; Sedang: <strong>&mu; = <?= number_format($details['fuzzification']['modal']['Sedang'], 4) ?></strong></div>
            <div>&bull; Tinggi: <strong>&mu; = <?= number_format($details['fuzzification']['modal']['Tinggi'], 4) ?></strong></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-2">
        <div class="border rounded p-3 bg-light">
          <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Lama Usaha (<?= $eval['input_lama_usaha'] ?> Thn)</div>
          <div class="small">
            <div>&bull; Pendek: <strong>&mu; = <?= number_format($details['fuzzification']['lama_usaha']['Pendek'], 4) ?></strong></div>
            <div>&bull; Menengah: <strong>&mu; = <?= number_format($details['fuzzification']['lama_usaha']['Menengah'], 4) ?></strong></div>
            <div>&bull; Panjang: <strong>&mu; = <?= number_format($details['fuzzification']['lama_usaha']['Panjang'], 4) ?></strong></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-2">
        <div class="border rounded p-3 bg-light">
          <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Stabilitas (<?= html_escape($eval['input_stabilitas']) ?>)</div>
          <div class="small">
            <div>&bull; Fluktuatif: <strong>&mu; = <?= number_format($details['fuzzification']['stabilitas']['Fluktuatif'], 1) ?></strong></div>
            <div>&bull; Cukup stabil: <strong>&mu; = <?= number_format($details['fuzzification']['stabilitas']['Cukup stabil'], 1) ?></strong></div>
            <div>&bull; Stabil: <strong>&mu; = <?= number_format($details['fuzzification']['stabilitas']['Stabil'], 1) ?></strong></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Chart & Centroid Details -->
<div class="row">
  <div class="col-lg-7 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-wave-square mr-2 text-primary"></i> Poligon Area Agregasi Output (MAX-MIN)</h6>
      </div>
      <div class="card-body">
        <div style="position: relative; height: 250px;">
          <canvas id="detailCurveChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-balance-scale mr-2 text-primary"></i> Parameter Defuzzifikasi Centroid</h6>
      </div>
      <div class="card-body">
        <table class="table table-sm table-borderless small mb-3">
          <tr>
            <td class="text-muted">Aktivasi Potong Rendah (&alpha;):</td>
            <td class="text-right font-weight-bold text-danger"><?= isset($details['aggregation']['alpha_cuts']['Rendah']) ? number_format($details['aggregation']['alpha_cuts']['Rendah'], 4) : '-' ?></td>
          </tr>
          <tr>
            <td class="text-muted">Aktivasi Potong Sedang (&alpha;):</td>
            <td class="text-right font-weight-bold text-warning"><?= isset($details['aggregation']['alpha_cuts']['Sedang']) ? number_format($details['aggregation']['alpha_cuts']['Sedang'], 4) : '-' ?></td>
          </tr>
          <tr>
            <td class="text-muted">Aktivasi Potong Tinggi (&alpha;):</td>
            <td class="text-right font-weight-bold text-success"><?= isset($details['aggregation']['alpha_cuts']['Tinggi']) ? number_format($details['aggregation']['alpha_cuts']['Tinggi'], 4) : '-' ?></td>
          </tr>
          <tr class="border-top">
            <td class="text-muted">Total Momen Numerik (&sum; z &bull; &mu;):</td>
            <td class="text-right font-weight-bold"><?= isset($details['aggregation']['sum_moment']) ? number_format($details['aggregation']['sum_moment'], 2) : '-' ?></td>
          </tr>
          <tr>
            <td class="text-muted">Total Luas Area Numerik (&sum; &mu;):</td>
            <td class="text-right font-weight-bold"><?= isset($details['aggregation']['sum_area']) ? number_format($details['aggregation']['sum_area'], 2) : '-' ?></td>
          </tr>
          <tr class="border-top">
            <td class="font-weight-bold text-dark">Skor Defuzzifikasi (Centroid):</td>
            <td class="text-right font-weight-bold text-primary" style="font-size: 1.15rem;"><?= number_format($eval['score'], 2) ?></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Riwayat Evaluasi Lainnya untuk UMKM ini & Opsi Perbandingan -->
<?php if (!empty($other_evaluations) && count($other_evaluations) > 1): ?>
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
      <h6 class="card-title m-0"><i class="fas fa-history mr-2 text-primary"></i> Evaluasi Lain untuk UMKM Ini (Bandingkan)</h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>ID Evaluasi</th>
              <th>Waktu</th>
              <th>Versi Model</th>
              <th>Modal</th>
              <th>Lama</th>
              <th>Stabilitas</th>
              <th>Skor</th>
              <th>Kategori</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($other_evaluations as $oe): ?>
              <tr class="<?= ($oe['id'] == $eval['id']) ? 'table-primary font-weight-bold' : '' ?>">
                <td>#<?= $oe['id'] ?> <?= ($oe['id'] == $eval['id']) ? '<span class="badge badge-primary">Sedang Dibuka</span>' : '' ?></td>
                <td><?= date('d/m/Y H:i', strtotime($oe['evaluated_at'])) ?></td>
                <td>v<?= html_escape($oe['model_version'] ? $oe['model_version'] : '1.0.0') ?></td>
                <td>Rp<?= number_format($oe['input_modal'], 0, ',', '.') ?></td>
                <td><?= $oe['input_lama_usaha'] ?> Thn</td>
                <td><?= html_escape($oe['input_stabilitas']) ?></td>
                <td><strong><?= number_format($oe['score'], 2) ?></strong></td>
                <td>
                  <?php if ($oe['category'] === 'Layak'): ?>
                    <span class="badge-soft-success">Layak</span>
                  <?php elseif ($oe['category'] === 'Cukup Layak'): ?>
                    <span class="badge-soft-warning">Cukup Layak</span>
                  <?php else: ?>
                    <span class="badge-soft-danger">Tidak Layak</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <?php if ($oe['id'] != $eval['id']): ?>
                    <a href="<?= base_url('hasil_evaluasi/bandingkan?id1=' . $eval['id'] . '&id2=' . $oe['id']) ?>" class="btn btn-xs btn-outline-primary" title="Bandingkan Hasil">
                      <i class="fas fa-columns mr-1"></i> Bandingkan
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  <?php if (!empty($details['aggregation']['chart_samples'])): ?>
  const samples = <?= json_encode($details['aggregation']['chart_samples']) ?>;
  const labels = samples.map(s => s.z);
  const dataAgg = samples.map(s => s.mu_aggregated);
  const dataRendah = samples.map(s => s.cut_rendah);
  const dataSedang = samples.map(s => s.cut_sedang);
  const dataTinggi = samples.map(s => s.cut_tinggi);

  const ctx = document.getElementById('detailCurveChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Poligon Agregasi Output (MAX)',
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
          borderDash: [3, 3],
          borderWidth: 1.5,
          fill: false,
          pointRadius: 0
        },
        {
          label: 'Potong Sedang',
          data: dataSedang,
          borderColor: '#F59E0B',
          borderDash: [3, 3],
          borderWidth: 1.5,
          fill: false,
          pointRadius: 0
        },
        {
          label: 'Potong Tinggi',
          data: dataTinggi,
          borderColor: '#10B981',
          borderDash: [3, 3],
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
        x: { ticks: { stepSize: 10 } },
        y: { min: 0, max: 1.05 }
      },
      plugins: {
        legend: { position: 'top' }
      }
    }
  });
  <?php endif; ?>
});
</script>
