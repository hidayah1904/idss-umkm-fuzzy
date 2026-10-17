<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Lembar Hasil Evaluasi Kelayakan UMKM — #<?= $eval['id'] ?></title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      color: #1E293B;
      background: #FFFFFF;
      padding: 20px;
    }
    .header-border {
      border-bottom: 2px solid #1E3A8A;
      padding-bottom: 15px;
      margin-bottom: 20px;
    }
    .score-badge {
      display: inline-block;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 1.5rem;
      font-weight: 700;
      color: #FFFFFF;
      background-color: #2563EB;
    }
    .disclaimer-box {
      border: 1px dashed #CBD5E1;
      padding: 12px;
      border-radius: 6px;
      font-size: 0.8rem;
      color: #64748B;
      margin-top: 30px;
    }
    @media print {
      body { padding: 0; }
      .no-print { display: none !important; }
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="no-print text-right mb-3">
    <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> Cetak Dokumen</button>
    <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
  </div>

  <!-- Header Dokumen -->
  <div class="header-border d-flex align-items-center justify-content-between">
    <div>
      <h4 class="font-weight-bold mb-1 text-primary">IDSS UMKM — FUZZY MAMDANI</h4>
      <h6 class="text-muted mb-0">Model Penalaran Fuzzy untuk Evaluasi Kelayakan Usaha UMKM</h6>
    </div>
    <div class="text-right small text-muted">
      <div><strong>No. Evaluasi:</strong> #<?= sprintf('%05d', $eval['id']) ?></div>
      <div><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($eval['evaluated_at'])) ?></div>
      <div><strong>Model:</strong> <?= html_escape($eval['model_code'] ? $eval['model_code'] : 'MAMDANI-V1.0') ?> (v<?= html_escape($eval['model_version'] ? $eval['model_version'] : '1.0.0') ?>)</div>
    </div>
  </div>

  <!-- Identitas Usaha -->
  <h6 class="font-weight-bold text-uppercase border-bottom pb-1 mb-2">I. Identitas Usaha</h6>
  <table class="table table-sm table-bordered mb-4 small">
    <tr>
      <th width="25%" class="bg-light">ID UMKM</th>
      <td width="25%"><strong><?= html_escape($eval['id_umkm']) ?></strong></td>
      <th width="25%" class="bg-light">Status Administratif</th>
      <td width="25%"><?= html_escape($eval['status_usaha']) ?></td>
    </tr>
    <tr>
      <th class="bg-light">Nama Usaha / Pemilik</th>
      <td><strong><?= html_escape($eval['nama_umkm']) ?></strong></td>
      <th class="bg-light">Bidang Usaha</th>
      <td><?= html_escape($eval['bidang_usaha']) ?></td>
    </tr>
    <tr>
      <th class="bg-light">Kecamatan</th>
      <td><?= html_escape($eval['kecamatan']) ?></td>
      <th class="bg-light">Desa / Kelurahan</th>
      <td><?= html_escape($eval['desa_kelurahan']) ?></td>
    </tr>
  </table>

  <!-- Nilai Masukan & Fuzzifikasi -->
  <h6 class="font-weight-bold text-uppercase border-bottom pb-1 mb-2">II. Variabel Masukan & Derajat Keanggotaan (&mu;)</h6>
  <table class="table table-sm table-bordered mb-4 small">
    <thead class="bg-light">
      <tr>
        <th>Variabel Masukan</th>
        <th>Nilai Aktual</th>
        <th>Fungsi Keanggotaan</th>
        <th>Derajat (&mu;)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td rowspan="3" class="align-middle"><strong>1. Modal / Investasi</strong></td>
        <td rowspan="3" class="align-middle">Rp<?= number_format($eval['input_modal'], 0, ',', '.') ?></td>
        <td>Rendah (&le; Rp14.000.000)</td>
        <td><?= isset($details['fuzzification']['modal']['Rendah']) ? number_format($details['fuzzification']['modal']['Rendah'], 4) : '-' ?></td>
      </tr>
      <tr>
        <td>Sedang (Rp5.000.000 - Rp50.000.000)</td>
        <td><?= isset($details['fuzzification']['modal']['Sedang']) ? number_format($details['fuzzification']['modal']['Sedang'], 4) : '-' ?></td>
      </tr>
      <tr>
        <td>Tinggi (&ge; Rp14.000.000)</td>
        <td><?= isset($details['fuzzification']['modal']['Tinggi']) ? number_format($details['fuzzification']['modal']['Tinggi'], 4) : '-' ?></td>
      </tr>
      <tr>
        <td rowspan="3" class="align-middle"><strong>2. Lama Usaha</strong></td>
        <td rowspan="3" class="align-middle"><?= $eval['input_lama_usaha'] ?> Tahun</td>
        <td>Pendek (&le; 8 Tahun)</td>
        <td><?= isset($details['fuzzification']['lama_usaha']['Pendek']) ? number_format($details['fuzzification']['lama_usaha']['Pendek'], 4) : '-' ?></td>
      </tr>
      <tr>
        <td>Menengah (4 - 11 Tahun)</td>
        <td><?= isset($details['fuzzification']['lama_usaha']['Menengah']) ? number_format($details['fuzzification']['lama_usaha']['Menengah'], 4) : '-' ?></td>
      </tr>
      <tr>
        <td>Panjang (&ge; 8 Tahun)</td>
        <td><?= isset($details['fuzzification']['lama_usaha']['Panjang']) ? number_format($details['fuzzification']['lama_usaha']['Panjang'], 4) : '-' ?></td>
      </tr>
      <tr>
        <td><strong>3. Stabilitas Pendapatan</strong></td>
        <td><strong><?= html_escape($eval['input_stabilitas']) ?></strong></td>
        <td>Singleton Kategori Terpilih</td>
        <td>1.0000</td>
      </tr>
    </tbody>
  </table>

  <!-- Hasil Inferensi & Skor Centroid -->
  <h6 class="font-weight-bold text-uppercase border-bottom pb-1 mb-2">III. Hasil Inferensi & Defuzzifikasi Centroid</h6>
  <div class="row align-items-center mb-4">
    <div class="col-8">
      <table class="table table-sm table-borderless small mb-0">
        <tr>
          <td width="40%" class="text-muted">Kekuatan Potong Rendah (&alpha;):</td>
          <td><strong><?= isset($details['aggregation']['alpha_cuts']['Rendah']) ? number_format($details['aggregation']['alpha_cuts']['Rendah'], 4) : '-' ?></strong></td>
        </tr>
        <tr>
          <td class="text-muted">Kekuatan Potong Sedang (&alpha;):</td>
          <td><strong><?= isset($details['aggregation']['alpha_cuts']['Sedang']) ? number_format($details['aggregation']['alpha_cuts']['Sedang'], 4) : '-' ?></strong></td>
        </tr>
        <tr>
          <td class="text-muted">Kekuatan Potong Tinggi (&alpha;):</td>
          <td><strong><?= isset($details['aggregation']['alpha_cuts']['Tinggi']) ? number_format($details['aggregation']['alpha_cuts']['Tinggi'], 4) : '-' ?></strong></td>
        </tr>
        <tr>
          <td class="text-muted">Metode Defuzzifikasi:</td>
          <td><strong>Centroid (Center of Gravity)</strong></td>
        </tr>
      </table>
    </div>
    <div class="col-4 text-center">
      <div class="border p-3 rounded bg-light">
        <span class="text-xs text-muted font-weight-bold text-uppercase d-block mb-1">Skor Akhir:</span>
        <h2 class="font-weight-bold mb-1 text-primary"><?= number_format($eval['score'], 2) ?></h2>
        <span class="badge badge-<?= ($eval['category'] === 'Layak') ? 'success' : (($eval['category'] === 'Cukup Layak') ? 'warning' : 'danger') ?> font-weight-bold px-3 py-1" style="font-size: 0.95rem;">
          <?= html_escape($eval['category']) ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Catatan & Rekomendasi -->
  <h6 class="font-weight-bold text-uppercase border-bottom pb-1 mb-2">IV. Catatan Interpretasi & Rekomendasi</h6>
  <p class="small text-muted" style="line-height: 1.6;">
    <?= html_escape(isset($details['result']['interpretation']) ? $details['result']['interpretation'] : '-') ?>
  </p>

  <!-- Tanda Tangan / Verifikasi -->
  <div class="row mt-5 pt-3">
    <div class="col-6 text-center">
      <div class="small text-muted">Pelaksana Evaluasi:</div>
      <div style="height: 60px;"></div>
      <div class="font-weight-bold small"><?= html_escape($eval['evaluator_name'] ? $eval['evaluator_name'] : 'Operator Sistem') ?></div>
      <div class="text-muted text-xs">IDSS UMKM Operator</div>
    </div>
    <div class="col-6 text-center">
      <div class="small text-muted">Tanggal Cetak: <?= date('d F Y') ?></div>
      <div style="height: 60px;"></div>
      <div class="font-weight-bold small">Sistem IDSS UMKM Fuzzy Mamdani</div>
      <div class="text-muted text-xs">Intelligent Decision Support System</div>
    </div>
  </div>

  <!-- Catatan Metodologis Disclaimer -->
  <div class="disclaimer-box">
    <strong>DISCLAIMER SISTEM PENDUKUNG KEPUTUSAN:</strong><br>
    Hasil evaluasi ini dihasilkan melalui model komputasi Fuzzy Mamdani (v1.0) sebagai sistem pendukung keputusan (Decision Support System). Nilai indeks dan kategori yang ditampilkan bukan merupakan keputusan resmi lembaga pembiayaan, bukan jaminan kelangsungan usaha, dan tidak menggantikan proses uji tuntas (due diligence) formal oleh otoritas yang berwenang.
  </div>
</div>

</body>
</html>
