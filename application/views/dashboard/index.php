<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Action Banners / Quick Shortcuts -->
<div class="row mb-3">
  <div class="col-12">
    <div class="card bg-white shadow-sm border-0">
      <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between">
        <div class="d-flex align-items-center mb-2 mb-md-0">
          <div class="bg-primary text-white rounded p-3 mr-3 shadow-sm">
            <i class="fas fa-brain fa-2x"></i>
          </div>
          <div>
            <h5 class="font-weight-bold text-dark mb-1">Intelligent Decision Support System — UMKM</h5>
            <p class="text-muted small mb-0">Model Penalaran Fuzzy Mamdani untuk Penilaian Kelayakan Usaha Berbasis Data Empiris.</p>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <a href="<?= base_url('evaluasi/massal') ?>" class="btn btn-warning mr-2 mb-1 shadow-sm font-weight-bold text-dark">
            <i class="fas fa-bolt mr-1"></i> Evaluasi Massal (560 UMKM)
          </a>
          <a href="<?= base_url('evaluasi') ?>" class="btn btn-primary mr-2 mb-1 shadow-sm">
            <i class="fas fa-plus-circle mr-1"></i> Evaluasi Tunggal
          </a>
          <a href="<?= base_url('umkm/import') ?>" class="btn btn-outline-primary mr-2 mb-1">
            <i class="fas fa-file-import mr-1"></i> Impor Dataset
          </a>
          <a href="<?= base_url('laporan') ?>" class="btn btn-light border mb-1">
            <i class="fas fa-file-pdf mr-1 text-danger"></i> Rekap & Laporan
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 5 Real Statistic Cards -->
<div class="row">
  <!-- Total UMKM -->
  <div class="col-xl col-md-6 col-12 mb-3">
    <div class="stat-card">
      <div class="stat-icon bg-light text-primary">
        <i class="fas fa-store"></i>
      </div>
      <div class="stat-val"><?= number_format($stats['total_umkm']) ?></div>
      <div class="stat-label">Total UMKM Terdaftar</div>
      <a href="<?= base_url('umkm') ?>" class="stretched-link"></a>
    </div>
  </div>

  <!-- Total Evaluasi -->
  <div class="col-xl col-md-6 col-12 mb-3">
    <div class="stat-card">
      <div class="stat-icon" style="background-color: #EDE9FE; color: #7C3AED;">
        <i class="fas fa-calculator"></i>
      </div>
      <div class="stat-val"><?= number_format($stats['total_evaluasi']) ?></div>
      <div class="stat-label">Total Evaluasi Dilakukan</div>
      <a href="<?= base_url('hasil_evaluasi') ?>" class="stretched-link"></a>
    </div>
  </div>

  <!-- Layak -->
  <div class="col-xl col-md-4 col-12 mb-3">
    <div class="stat-card" style="border-left: 4px solid #10B981;">
      <div class="stat-icon" style="background-color: var(--status-layak-bg); color: var(--status-layak-text);">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="stat-val text-success"><?= number_format($stats['layak']) ?></div>
      <div class="stat-label">Hasil: Layak (&ge; 70)</div>
      <a href="<?= base_url('hasil_evaluasi?category=Layak') ?>" class="stretched-link"></a>
    </div>
  </div>

  <!-- Cukup Layak -->
  <div class="col-xl col-md-4 col-12 mb-3">
    <div class="stat-card" style="border-left: 4px solid #F59E0B;">
      <div class="stat-icon" style="background-color: var(--status-cukup-bg); color: var(--status-cukup-text);">
        <i class="fas fa-exclamation-circle"></i>
      </div>
      <div class="stat-val text-warning"><?= number_format($stats['cukup_layak']) ?></div>
      <div class="stat-label">Hasil: Cukup Layak (40-69)</div>
      <a href="<?= base_url('hasil_evaluasi?category=Cukup+Layak') ?>" class="stretched-link"></a>
    </div>
  </div>

  <!-- Tidak Layak -->
  <div class="col-xl col-md-4 col-12 mb-3">
    <div class="stat-card" style="border-left: 4px solid #EF4444;">
      <div class="stat-icon" style="background-color: var(--status-tidak-bg); color: var(--status-tidak-text);">
        <i class="fas fa-times-circle"></i>
      </div>
      <div class="stat-val text-danger"><?= number_format($stats['tidak_layak']) ?></div>
      <div class="stat-label">Hasil: Tidak Layak (&lt; 40)</div>
      <a href="<?= base_url('hasil_evaluasi?category=Tidak+Layak') ?>" class="stretched-link"></a>
    </div>
  </div>
</div>

<!-- Charts Row 1: Kategori & Tren Bulanan -->
<div class="row">
  <!-- Distribusi Kategori -->
  <div class="col-lg-5 col-12 mb-4">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h6 class="card-title m-0"><i class="fas fa-chart-pie mr-2 text-primary"></i> Distribusi Kategori Hasil Evaluasi</h6>
      </div>
      <div class="card-body d-flex flex-column justify-content-center">
        <?php if ($stats['total_evaluasi'] > 0): ?>
          <div style="position: relative; height: 260px;">
            <canvas id="categoryChart"></canvas>
          </div>
          <div class="d-flex justify-content-around text-center mt-3 pt-2 border-top">
            <div>
              <span class="badge-soft-success px-2 py-1 small">Layak</span>
              <div class="font-weight-bold mt-1"><?= $stats['layak'] ?> (<?= round(($stats['layak'] / $stats['total_evaluasi']) * 100, 1) ?>%)</div>
            </div>
            <div>
              <span class="badge-soft-warning px-2 py-1 small">Cukup Layak</span>
              <div class="font-weight-bold mt-1"><?= $stats['cukup_layak'] ?> (<?= round(($stats['cukup_layak'] / $stats['total_evaluasi']) * 100, 1) ?>%)</div>
            </div>
            <div>
              <span class="badge-soft-danger px-2 py-1 small">Tidak Layak</span>
              <div class="font-weight-bold mt-1"><?= $stats['tidak_layak'] ?> (<?= round(($stats['tidak_layak'] / $stats['total_evaluasi']) * 100, 1) ?>%)</div>
            </div>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <i class="fas fa-chart-pie fa-3x mb-3 text-light"></i>
            <p class="mb-2">Belum ada data evaluasi yang dilakukan.</p>
            <a href="<?= base_url('evaluasi') ?>" class="btn btn-sm btn-outline-primary">Mulai Evaluasi Pertama</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Tren Bulanan -->
  <div class="col-lg-7 col-12 mb-4">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h6 class="card-title m-0"><i class="fas fa-chart-line mr-2 text-primary"></i> Tren Evaluasi per Bulan (Tahun <?= $selected_year ?>)</h6>
        <div class="card-tools">
          <form method="get" class="form-inline m-0">
            <select name="year" class="form-control form-control-sm" onchange="this.form.submit()">
              <?php for ($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
                <option value="<?= $y ?>" <?= ($selected_year == $y) ? 'selected' : '' ?>><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </form>
        </div>
      </div>
      <div class="card-body">
        <?php if (array_sum($monthly_trend['total']) > 0): ?>
          <div style="position: relative; height: 290px;">
            <canvas id="monthlyTrendChart"></canvas>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <i class="fas fa-chart-bar fa-3x mb-3 text-light"></i>
            <p class="mb-2">Belum ada evaluasi tercatat untuk tahun <?= $selected_year ?>.</p>
            <small class="text-muted">Data akan otomatis digambarkan saat proses evaluasi UMKM disimpan.</small>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row 2: Bidang Usaha & Stabilitas -->
<div class="row">
  <!-- Distribusi Bidang Usaha -->
  <div class="col-lg-7 col-12 mb-4">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-briefcase mr-2 text-primary"></i> Evaluasi Menurut Bidang Usaha Teratas</h6>
      </div>
      <div class="card-body">
        <?php if (!empty($sector_dist)): ?>
          <div style="position: relative; height: 260px;">
            <canvas id="sectorChart"></canvas>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <i class="fas fa-briefcase fa-3x mb-3 text-light"></i>
            <p class="mb-0">Belum ada data bidang usaha yang terhubung dengan hasil evaluasi.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Distribusi Stabilitas Pendapatan -->
  <div class="col-lg-5 col-12 mb-4">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-white">
        <h6 class="card-title m-0"><i class="fas fa-balance-scale mr-2 text-primary"></i> Stabilitas Pendapatan UMKM</h6>
      </div>
      <div class="card-body">
        <?php if (!empty($stability_dist)): ?>
          <div style="position: relative; height: 260px;">
            <canvas id="stabilityChart"></canvas>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <i class="fas fa-balance-scale fa-3x mb-3 text-light"></i>
            <p class="mb-0">Belum ada data stabilitas pendapatan yang dievaluasi.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Tables Row: Evaluasi Terbaru & Log Aktivitas -->
<div class="row">
  <!-- Evaluasi Terbaru -->
  <div class="col-lg-7 col-12 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h6 class="card-title m-0"><i class="fas fa-history mr-2 text-primary"></i> Riwayat Evaluasi Terbaru</h6>
        <a href="<?= base_url('hasil_evaluasi') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body p-0">
        <?php if (!empty($recent_evaluations)): ?>
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>ID UMKM</th>
                  <th>Nama Usaha</th>
                  <th>Waktu</th>
                  <th>Skor</th>
                  <th>Kategori</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent_evaluations as $re): ?>
                  <tr>
                    <td><strong><?= html_escape($re['id_umkm']) ?></strong></td>
                    <td><?= html_escape($re['nama_umkm']) ?></td>
                    <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($re['evaluated_at'])) ?></td>
                    <td><strong><?= number_format($re['score'], 2) ?></strong></td>
                    <td>
                      <?php if ($re['category'] === 'Layak'): ?>
                        <span class="badge-soft-success">Layak</span>
                      <?php elseif ($re['category'] === 'Cukup Layak'): ?>
                        <span class="badge-soft-warning">Cukup Layak</span>
                      <?php else: ?>
                        <span class="badge-soft-danger">Tidak Layak</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <a href="<?= base_url('hasil_evaluasi/detail/' . $re['id']) ?>" class="btn btn-xs btn-outline-primary" title="Lihat Detail">
                        <i class="fas fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="text-center py-4 text-muted">
            <p class="mb-0">Belum ada evaluasi yang tercatat.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Aktivitas Sistem Terbaru -->
  <div class="col-lg-5 col-12 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h6 class="card-title m-0"><i class="fas fa-shield-alt mr-2 text-primary"></i> Aktivitas Sistem</h6>
        <?php if ($logged_user['role'] === 'admin'): ?>
          <a href="<?= base_url('log_aktivitas') ?>" class="btn btn-sm btn-outline-secondary">Audit Trail</a>
        <?php endif; ?>
      </div>
      <div class="card-body p-0">
        <?php if (!empty($recent_activities)): ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($recent_activities as $act): ?>
              <li class="list-group-item px-3 py-2 small">
                <div class="d-flex justify-content-between">
                  <strong class="text-dark"><?= html_escape($act['action']) ?></strong>
                  <span class="text-muted text-xs"><?= date('d M H:i', strtotime($act['created_at'])) ?></span>
                </div>
                <div class="text-muted text-truncate mt-1"><?= html_escape($act['description']) ?></div>
                <div class="text-xs text-primary mt-1">
                  <i class="fas fa-user-circle mr-1"></i> <?= html_escape($act['user_name'] ? $act['user_name'] : 'Sistem') ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <div class="text-center py-4 text-muted">
            <p class="mb-0">Belum ada aktivitas tercatat.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js Scripts -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  <?php if ($stats['total_evaluasi'] > 0): ?>
  // 1. Category Doughnut Chart
  const ctxCat = document.getElementById('categoryChart').getContext('2d');
  new Chart(ctxCat, {
    type: 'doughnut',
    data: {
      labels: ['Layak', 'Cukup Layak', 'Tidak Layak'],
      datasets: [{
        data: [<?= (int)$stats['layak'] ?>, <?= (int)$stats['cukup_layak'] ?>, <?= (int)$stats['tidak_layak'] ?>],
        backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
        borderWidth: 2,
        borderColor: '#FFFFFF'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' }
      },
      cutout: '65%'
    }
  });
  <?php endif; ?>

  <?php if (array_sum($monthly_trend['total']) > 0): ?>
  // 2. Monthly Trend Chart
  const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
  new Chart(ctxTrend, {
    type: 'bar',
    data: {
      labels: <?= json_encode($monthly_trend['labels']) ?>,
      datasets: [
        {
          label: 'Layak',
          data: <?= json_encode($monthly_trend['layak']) ?>,
          backgroundColor: '#10B981'
        },
        {
          label: 'Cukup Layak',
          data: <?= json_encode($monthly_trend['cukup_layak']) ?>,
          backgroundColor: '#F59E0B'
        },
        {
          label: 'Tidak Layak',
          data: <?= json_encode($monthly_trend['tidak_layak']) ?>,
          backgroundColor: '#EF4444'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: { stacked: true },
        y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
      },
      plugins: {
        legend: { position: 'top' }
      }
    }
  });
  <?php endif; ?>

  <?php if (!empty($sector_dist)): ?>
  // 3. Sector Distribution Chart
  const ctxSector = document.getElementById('sectorChart').getContext('2d');
  new Chart(ctxSector, {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_column($sector_dist, 'bidang_usaha')) ?>,
      datasets: [{
        label: 'Jumlah Evaluasi',
        data: <?= json_encode(array_map('intval', array_column($sector_dist, 'count'))) ?>,
        backgroundColor: '#2563EB',
        borderRadius: 4
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: { beginAtZero: true, ticks: { precision: 0 } }
      },
      plugins: { legend: { display: false } }
    }
  });
  <?php endif; ?>

  <?php if (!empty($stability_dist)): ?>
  // 4. Stability Distribution Chart
  const ctxStab = document.getElementById('stabilityChart').getContext('2d');
  new Chart(ctxStab, {
    type: 'pie',
    data: {
      labels: <?= json_encode(array_column($stability_dist, 'input_stabilitas')) ?>,
      datasets: [{
        data: <?= json_encode(array_map('intval', array_column($stability_dist, 'count'))) ?>,
        backgroundColor: ['#3B82F6', '#8B5CF6', '#EC4899']
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom' } }
    }
  });
  <?php endif; ?>
});
</script>
