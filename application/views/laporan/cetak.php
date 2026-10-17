<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan IDSS UMKM — <?= strtoupper(str_replace('_', ' ', $report_type)) ?></title>
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
      padding-bottom: 12px;
      margin-bottom: 20px;
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
    <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> Cetak Laporan</button>
    <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
  </div>

  <div class="header-border d-flex justify-content-between align-items-center">
    <div>
      <h4 class="font-weight-bold mb-1 text-primary">IDSS UMKM — FUZZY MAMDANI</h4>
      <h6 class="text-muted mb-0">Laporan Rekapitulasi: <?= strtoupper(str_replace('_', ' ', $report_type)) ?></h6>
    </div>
    <div class="text-right small text-muted">
      <div>Tanggal Cetak: <?= $print_date ?></div>
      <div>Dicetak Oleh: <?= html_escape($printed_by) ?></div>
    </div>
  </div>

  <?php if ($report_type === 'rekap_kategori'): ?>
    <table class="table table-sm table-bordered small">
      <thead class="bg-light">
        <tr>
          <th>Kategori Kelayakan</th>
          <th class="text-center">Jumlah Evaluasi</th>
          <th class="text-center">Proporsi (%)</th>
          <th class="text-center">Rata-rata Skor</th>
          <th class="text-center">Skor Terendah</th>
          <th class="text-center">Skor Tertinggi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($report_data['items'] as $item): ?>
          <tr>
            <td><strong><?= html_escape($item['category']) ?></strong></td>
            <td class="text-center"><?= number_format($item['total']) ?></td>
            <td class="text-center"><?= $item['percentage'] ?>%</td>
            <td class="text-center"><?= number_format($item['avg_score'], 2) ?></td>
            <td class="text-center"><?= number_format($item['min_score'], 2) ?></td>
            <td class="text-center"><?= number_format($item['max_score'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
        <tr class="table-light font-weight-bold">
          <td>TOTAL</td>
          <td class="text-center"><?= number_format($report_data['total_all']) ?></td>
          <td class="text-center">100%</td>
          <td colspan="3"></td>
        </tr>
      </tbody>
    </table>

  <?php elseif ($report_type === 'bidang_usaha'): ?>
    <table class="table table-sm table-bordered small">
      <thead class="bg-light">
        <tr>
          <th>No</th>
          <th>Bidang Usaha</th>
          <th class="text-center">Total Evaluasi</th>
          <th class="text-center">Layak</th>
          <th class="text-center">Cukup Layak</th>
          <th class="text-center">Tidak Layak</th>
          <th class="text-center">Rata-rata Skor</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; foreach ($report_data as $row): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><strong><?= html_escape($row['bidang_usaha']) ?></strong></td>
            <td class="text-center"><?= number_format($row['total_evaluasi']) ?></td>
            <td class="text-center"><?= number_format($row['total_layak']) ?></td>
            <td class="text-center"><?= number_format($row['total_cukup_layak']) ?></td>
            <td class="text-center"><?= number_format($row['total_tidak_layak']) ?></td>
            <td class="text-center font-weight-bold"><?= number_format($row['avg_score'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  <?php elseif ($report_type === 'kecamatan'): ?>
    <table class="table table-sm table-bordered small">
      <thead class="bg-light">
        <tr>
          <th>No</th>
          <th>Kecamatan</th>
          <th class="text-center">Total Evaluasi</th>
          <th class="text-center">Layak</th>
          <th class="text-center">Cukup Layak</th>
          <th class="text-center">Tidak Layak</th>
          <th class="text-center">Rata-rata Skor</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; foreach ($report_data as $row): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><strong><?= html_escape($row['kecamatan']) ?></strong></td>
            <td class="text-center"><?= number_format($row['total_evaluasi']) ?></td>
            <td class="text-center"><?= number_format($row['total_layak']) ?></td>
            <td class="text-center"><?= number_format($row['total_cukup_layak']) ?></td>
            <td class="text-center"><?= number_format($row['total_tidak_layak']) ?></td>
            <td class="text-center font-weight-bold"><?= number_format($row['avg_score'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  <?php elseif ($report_type === 'evaluasi_periode'): ?>
    <table class="table table-sm table-bordered small">
      <thead class="bg-light">
        <tr>
          <th>ID</th>
          <th>ID UMKM</th>
          <th>Nama Usaha</th>
          <th>Kecamatan</th>
          <th>Modal (Rp)</th>
          <th>Lama</th>
          <th>Stabilitas</th>
          <th>Skor</th>
          <th>Kategori</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($report_data as $row): ?>
          <tr>
            <td>#<?= $row['id'] ?></td>
            <td><?= html_escape($row['id_umkm']) ?></td>
            <td><?= html_escape($row['nama_umkm']) ?></td>
            <td><?= html_escape($row['kecamatan']) ?></td>
            <td>Rp<?= number_format($row['input_modal'], 0, ',', '.') ?></td>
            <td><?= $row['input_lama_usaha'] ?> Thn</td>
            <td><?= html_escape($row['input_stabilitas']) ?></td>
            <td><strong><?= number_format($row['score'], 2) ?></strong></td>
            <td><?= html_escape($row['category']) ?></td>
            <td><?= date('d/m/Y', strtotime($row['evaluated_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <div class="mt-4 pt-2 border-top small text-muted">
    <em>Laporan ini merupakan keluaran analitis sistem pendukung keputusan (Decision Support System) Fuzzy Mamdani dan ditujukan untuk referensi evaluatif internal.</em>
  </div>
</div>

</body>
</html>
