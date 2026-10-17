<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row mb-3 align-items-center">
  <div class="col-md-7">
    <h4 class="font-weight-bold text-dark mb-1">
      <i class="fas fa-file-alt mr-2 text-primary"></i> Pusat Laporan & Rekapitulasi IDSS UMKM
    </h4>
    <p class="text-muted small mb-0">Penyajian laporan analitis, rekapitulasi kelayakan usaha, serta ekspor data evaluasi.</p>
  </div>
  <div class="col-md-5 text-md-right mt-3 mt-md-0">
    <a href="<?= base_url('laporan/cetak?' . http_build_query(array_merge($filters, array('type' => $report_type)))) ?>" target="_blank" class="btn btn-outline-secondary mr-2 mb-1">
      <i class="fas fa-print mr-1"></i> Cetak Laporan
    </a>
    <a href="<?= base_url('laporan/export_csv?' . http_build_query(array_merge($filters, array('type' => $report_type)))) ?>" class="btn btn-success mb-1">
      <i class="fas fa-file-excel mr-1"></i> Ekspor CSV
    </a>
  </div>
</div>

<!-- Tabs Pilihan Jenis Laporan -->
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white p-2">
    <ul class="nav nav-pills">
      <li class="nav-item">
        <a class="nav-link <?= ($report_type === 'rekap_kategori') ? 'active' : '' ?>" href="<?= base_url('laporan?type=rekap_kategori') ?>">
          <i class="fas fa-chart-pie mr-1"></i> Rekap Kategori
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($report_type === 'bidang_usaha') ? 'active' : '' ?>" href="<?= base_url('laporan?type=bidang_usaha') ?>">
          <i class="fas fa-briefcase mr-1"></i> Bidang Usaha
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($report_type === 'kecamatan') ? 'active' : '' ?>" href="<?= base_url('laporan?type=kecamatan') ?>">
          <i class="fas fa-map-marker-alt mr-1"></i> Kecamatan
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($report_type === 'evaluasi_periode') ? 'active' : '' ?>" href="<?= base_url('laporan?type=evaluasi_periode') ?>">
          <i class="fas fa-calendar-alt mr-1"></i> Berdasarkan Periode
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($report_type === 'data_umkm') ? 'active' : '' ?>" href="<?= base_url('laporan?type=data_umkm') ?>">
          <i class="fas fa-store mr-1"></i> Data Seluruh UMKM
        </a>
      </li>
    </ul>
  </div>

  <!-- Form Filter Laporan -->
  <div class="card-body bg-light border-bottom py-3">
    <form method="get" action="<?= base_url('laporan') ?>">
      <input type="hidden" name="type" value="<?= html_escape($report_type) ?>">
      <div class="row align-items-end">
        <div class="col-md-3 col-sm-6 form-group mb-2">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Dari Tanggal</label>
          <input type="date" name="start_date" class="form-control form-control-sm" value="<?= html_escape($filters['start_date']) ?>">
        </div>

        <div class="col-md-3 col-sm-6 form-group mb-2">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Sampai Tanggal</label>
          <input type="date" name="end_date" class="form-control form-control-sm" value="<?= html_escape($filters['end_date']) ?>">
        </div>

        <?php if ($report_type === 'evaluasi_periode' || $report_type === 'rekap_kategori'): ?>
        <div class="col-md-3 col-sm-6 form-group mb-2">
          <label class="text-xs font-weight-bold text-muted text-uppercase">Kategori</label>
          <select name="category" class="form-control form-control-sm">
            <option value="">-- Semua Kategori --</option>
            <option value="Layak" <?= ($filters['category'] === 'Layak') ? 'selected' : '' ?>>Layak</option>
            <option value="Cukup Layak" <?= ($filters['category'] === 'Cukup Layak') ? 'selected' : '' ?>>Cukup Layak</option>
            <option value="Tidak Layak" <?= ($filters['category'] === 'Tidak Layak') ? 'selected' : '' ?>>Tidak Layak</option>
          </select>
        </div>
        <?php endif; ?>

        <div class="col-md-2 col-sm-6 form-group mb-2">
          <button type="submit" class="btn btn-sm btn-primary btn-block">
            <i class="fas fa-filter mr-1"></i> Filter Laporan
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- Isi Laporan Sesuai Tab -->
  <div class="card-body p-0">
    
    <!-- 1. Rekap Kategori -->
    <?php if ($report_type === 'rekap_kategori'): ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
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
            <?php if (!empty($report_data['items'])): ?>
              <?php foreach ($report_data['items'] as $item): ?>
                <tr>
                  <td>
                    <?php if ($item['category'] === 'Layak'): ?>
                      <span class="badge-soft-success font-weight-bold px-3 py-1">Layak (&ge; 70)</span>
                    <?php elseif ($item['category'] === 'Cukup Layak'): ?>
                      <span class="badge-soft-warning font-weight-bold px-3 py-1">Cukup Layak (40-69)</span>
                    <?php else: ?>
                      <span class="badge-soft-danger font-weight-bold px-3 py-1">Tidak Layak (&lt; 40)</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center font-weight-bold"><?= number_format($item['total']) ?></td>
                  <td class="text-center font-weight-bold text-primary"><?= $item['percentage'] ?>%</td>
                  <td class="text-center"><?= number_format($item['avg_score'], 2) ?></td>
                  <td class="text-center text-muted"><?= number_format($item['min_score'], 2) ?></td>
                  <td class="text-center text-muted"><?= number_format($item['max_score'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
              <tr class="table-light font-weight-bold">
                <td>TOTAL</td>
                <td class="text-center"><?= number_format($report_data['total_all']) ?></td>
                <td class="text-center">100%</td>
                <td colspan="3"></td>
              </tr>
            <?php else: ?>
              <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data evaluasi.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    <!-- 2. Laporan Bidang Usaha -->
    <?php elseif ($report_type === 'bidang_usaha'): ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>No</th>
              <th>Bidang Usaha</th>
              <th class="text-center">Total Evaluasi</th>
              <th class="text-center text-success">Layak</th>
              <th class="text-center text-warning">Cukup Layak</th>
              <th class="text-center text-danger">Tidak Layak</th>
              <th class="text-center">Rata-rata Skor</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($report_data)): ?>
              <?php $no = 1; foreach ($report_data as $row): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><strong><?= html_escape($row['bidang_usaha']) ?></strong></td>
                  <td class="text-center font-weight-bold"><?= number_format($row['total_evaluasi']) ?></td>
                  <td class="text-center text-success font-weight-bold"><?= number_format($row['total_layak']) ?></td>
                  <td class="text-center text-warning font-weight-bold"><?= number_format($row['total_cukup_layak']) ?></td>
                  <td class="text-center text-danger font-weight-bold"><?= number_format($row['total_tidak_layak']) ?></td>
                  <td class="text-center font-weight-bold text-primary"><?= number_format($row['avg_score'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    <!-- 3. Laporan Kecamatan -->
    <?php elseif ($report_type === 'kecamatan'): ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>No</th>
              <th>Kecamatan</th>
              <th class="text-center">Total Evaluasi</th>
              <th class="text-center text-success">Layak</th>
              <th class="text-center text-warning">Cukup Layak</th>
              <th class="text-center text-danger">Tidak Layak</th>
              <th class="text-center">Rata-rata Skor</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($report_data)): ?>
              <?php $no = 1; foreach ($report_data as $row): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><strong><?= html_escape($row['kecamatan']) ?></strong></td>
                  <td class="text-center font-weight-bold"><?= number_format($row['total_evaluasi']) ?></td>
                  <td class="text-center text-success font-weight-bold"><?= number_format($row['total_layak']) ?></td>
                  <td class="text-center text-warning font-weight-bold"><?= number_format($row['total_cukup_layak']) ?></td>
                  <td class="text-center text-danger font-weight-bold"><?= number_format($row['total_tidak_layak']) ?></td>
                  <td class="text-center font-weight-bold text-primary"><?= number_format($row['avg_score'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    <!-- 4. Laporan Evaluasi Berdasarkan Periode -->
    <?php elseif ($report_type === 'evaluasi_periode'): ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>ID Evaluasi</th>
              <th>ID UMKM</th>
              <th>Nama Usaha</th>
              <th>Kecamatan</th>
              <th>Modal (Rp)</th>
              <th>Lama Usaha</th>
              <th>Stabilitas</th>
              <th>Skor</th>
              <th>Kategori</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($report_data)): ?>
              <?php foreach ($report_data as $row): ?>
                <tr>
                  <td>#<?= $row['id'] ?></td>
                  <td><strong class="text-primary"><?= html_escape($row['id_umkm']) ?></strong></td>
                  <td><?= html_escape($row['nama_umkm']) ?></td>
                  <td><?= html_escape($row['kecamatan']) ?></td>
                  <td>Rp<?= number_format($row['input_modal'], 0, ',', '.') ?></td>
                  <td><?= $row['input_lama_usaha'] ?> Thn</td>
                  <td><?= html_escape($row['input_stabilitas']) ?></td>
                  <td><strong><?= number_format($row['score'], 2) ?></strong></td>
                  <td>
                    <?php if ($row['category'] === 'Layak'): ?>
                      <span class="badge-soft-success">Layak</span>
                    <?php elseif ($row['category'] === 'Cukup Layak'): ?>
                      <span class="badge-soft-warning">Cukup Layak</span>
                    <?php else: ?>
                      <span class="badge-soft-danger">Tidak Layak</span>
                    <?php endif; ?>
                  </td>
                  <td><small class="text-muted"><?= date('d/m/Y', strtotime($row['evaluated_at'])) ?></small></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="10" class="text-center py-4 text-muted">Tidak ada evaluasi pada periode ini.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    <!-- 5. Laporan Data UMKM -->
    <?php elseif ($report_type === 'data_umkm'): ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>ID UMKM</th>
              <th>Nama Usaha</th>
              <th>Bidang Usaha</th>
              <th>Modal (Rp)</th>
              <th>Lama Usaha</th>
              <th>Stabilitas</th>
              <th>Kecamatan</th>
              <th>Status Administratif</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($report_data)): ?>
              <?php foreach ($report_data as $row): ?>
                <tr>
                  <td><strong><?= html_escape($row['id_umkm']) ?></strong></td>
                  <td><?= html_escape($row['nama_umkm']) ?></td>
                  <td><?= html_escape($row['bidang_usaha']) ?></td>
                  <td>Rp<?= number_format($row['nilai_investasi'], 0, ',', '.') ?></td>
                  <td><?= $row['lama_usaha'] ?> Thn</td>
                  <td><?= html_escape($row['stabilitas_pendapatan']) ?></td>
                  <td><?= html_escape($row['kecamatan']) ?></td>
                  <td><span class="badge badge-light border"><?= html_escape($row['status_usaha']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data UMKM.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    <?php endif; ?>

  </div>

  <!-- Footer Disclaimer -->
  <div class="card-footer bg-white text-muted small border-top">
    <i class="fas fa-exclamation-circle text-warning mr-1"></i>
    <strong>Catatan Metodologis:</strong> Rekapitulasi ini merupakan hasil agregasi pemrosesan sistem pendukung keputusan (IDSS) Fuzzy Mamdani dan ditujukan untuk keperluan analisis evaluatif internal, bukan keputusan resmi kredit perbankan.
  </div>
</div>
