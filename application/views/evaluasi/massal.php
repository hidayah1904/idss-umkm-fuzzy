<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row mb-3">
  <div class="col-12 d-flex flex-wrap justify-content-between align-items-center">
    <div>
      <h4 class="font-weight-bold text-dark mb-1">
        <i class="fas fa-bolt text-warning mr-2"></i> Evaluasi Massal Otomatis — Fuzzy Mamdani
      </h4>
      <p class="text-muted small mb-0">Pemrosesan batch inferensi fuzzy untuk seluruh dataset UMKM terdaftar secara objektif & ilmiah.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= base_url('evaluasi') ?>" class="btn btn-outline-secondary mr-2">
        <i class="fas fa-calculator mr-1"></i> Evaluasi Tunggal
      </a>
      <a href="<?= base_url('hasil_evaluasi') ?>" class="btn btn-outline-primary mr-2">
        <i class="fas fa-list-alt mr-1"></i> Hasil & Riwayat
      </a>
      <a href="<?= base_url('dashboard') ?>" class="btn btn-light border">
        <i class="fas fa-arrow-left mr-1"></i> Dashboard
      </a>
    </div>
  </div>
</div>

<?php if (!empty($batch_summary)): ?>
<!-- BANNER HASIL EVALUASI MASSAL -->
<div class="card shadow-sm border-success mb-4" style="border-width: 2px;">
  <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-3">
    <h5 class="card-title m-0 font-weight-bold">
      <i class="fas fa-check-circle mr-2"></i> Hasil Pemrosesan Evaluasi Massal Selesai!
    </h5>
    <span class="badge badge-light px-3 py-2 font-weight-bold" style="font-size: 0.9rem;">
      <i class="fas fa-stopwatch mr-1"></i> Selesai dalam <?= $batch_summary['elapsed_seconds'] ?> detik
    </span>
  </div>
  <div class="card-body p-4">
    <div class="alert alert-info py-2 px-3 small mb-4">
      <i class="fas fa-info-circle mr-1"></i> <strong>Integritas Penelitian:</strong> Hasil evaluasi ini murni mencerminkan pemetaan matematis Fuzzy Mamdani berdasarkan parameter modal, lama usaha, dan stabilitas pendapatan dari dataset empiris. Distribusi hasil tidak dipaksakan merata dan tersimpan secara permanen di database.
    </div>

    <!-- 4 Kartu Ringkasan Hasil Aktual -->
    <div class="row mb-4">
      <div class="col-md-3 col-sm-6 mb-3">
        <div class="p-3 rounded border text-center bg-light h-100">
          <div class="text-muted small text-uppercase font-weight-bold mb-1">Total UMKM Diproses</div>
          <div class="h2 font-weight-bold text-dark mb-0"><?= number_format($batch_summary['total_processed']) ?></div>
          <small class="text-muted">100% data terhitung</small>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 mb-3">
        <div class="p-3 rounded border text-center h-100" style="background-color: #ECFDF5; border-color: #10B981 !important;">
          <div class="text-success small text-uppercase font-weight-bold mb-1">
            <i class="fas fa-check mr-1"></i> Kategori Layak
          </div>
          <div class="h2 font-weight-bold text-success mb-0"><?= number_format($batch_summary['cat_counts']['Layak']) ?></div>
          <small class="text-muted font-weight-bold">
            <?= $batch_summary['total_processed'] > 0 ? round(($batch_summary['cat_counts']['Layak'] / $batch_summary['total_processed']) * 100, 2) : 0 ?>% dari total (Skor &ge; 70)
          </small>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 mb-3">
        <div class="p-3 rounded border text-center h-100" style="background-color: #FFFBEB; border-color: #F59E0B !important;">
          <div class="text-warning small text-uppercase font-weight-bold mb-1">
            <i class="fas fa-exclamation mr-1"></i> Kategori Cukup Layak
          </div>
          <div class="h2 font-weight-bold text-warning mb-0"><?= number_format($batch_summary['cat_counts']['Cukup Layak']) ?></div>
          <small class="text-muted font-weight-bold">
            <?= $batch_summary['total_processed'] > 0 ? round(($batch_summary['cat_counts']['Cukup Layak'] / $batch_summary['total_processed']) * 100, 2) : 0 ?>% dari total (Skor 40-69)
          </small>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 mb-3">
        <div class="p-3 rounded border text-center h-100" style="background-color: #FEF2F2; border-color: #EF4444 !important;">
          <div class="text-danger small text-uppercase font-weight-bold mb-1">
            <i class="fas fa-times mr-1"></i> Kategori Tidak Layak
          </div>
          <div class="h2 font-weight-bold text-danger mb-0"><?= number_format($batch_summary['cat_counts']['Tidak Layak']) ?></div>
          <small class="text-muted font-weight-bold">
            <?= $batch_summary['total_processed'] > 0 ? round(($batch_summary['cat_counts']['Tidak Layak'] / $batch_summary['total_processed']) * 100, 2) : 0 ?>% dari total (Skor &lt; 40)
          </small>
        </div>
      </div>
    </div>

    <!-- Statistik Skor Evaluasi -->
    <div class="bg-light p-3 rounded border d-flex flex-wrap justify-content-around text-center mb-4">
      <div>
        <span class="text-muted small">Skor Minimum:</span>
        <div class="font-weight-bold text-dark h5 mb-0"><?= $batch_summary['min_score'] ?></div>
      </div>
      <div class="border-left pl-4">
        <span class="text-muted small">Skor Rata-rata:</span>
        <div class="font-weight-bold text-primary h5 mb-0"><?= $batch_summary['avg_score'] ?></div>
      </div>
      <div class="border-left pl-4">
        <span class="text-muted small">Skor Maksimum:</span>
        <div class="font-weight-bold text-success h5 mb-0"><?= $batch_summary['max_score'] ?></div>
      </div>
      <div class="border-left pl-4">
        <span class="text-muted small">Metode Defuzzifikasi:</span>
        <div class="font-weight-bold text-dark h5 mb-0">Centroid (Center of Gravity)</div>
      </div>
    </div>

    <!-- Tombol Navigasi Lanjutan -->
    <div class="text-center">
      <a href="<?= base_url('hasil_evaluasi') ?>" class="btn btn-primary px-4 py-2 mr-2 font-weight-bold shadow-sm">
        <i class="fas fa-table mr-2"></i> Buka Hasil & Riwayat Lengkap (560 Data)
      </a>
      <a href="<?= base_url('dashboard') ?>" class="btn btn-success px-4 py-2 mr-2 font-weight-bold shadow-sm">
        <i class="fas fa-tachometer-alt mr-2"></i> Lihat Grafik & Metrik Dashboard
      </a>
      <a href="<?= base_url('laporan?type=rekap_kategori') ?>" class="btn btn-outline-secondary px-4 py-2 font-weight-bold">
        <i class="fas fa-file-pdf mr-2 text-danger"></i> Unduh / Cetak Laporan
      </a>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- PANEL KONTROL EKSEKUSI EVALUASI MASSAL -->
<div class="row">
  <div class="col-lg-8">
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 font-weight-bold">
          <i class="fas fa-cogs text-primary mr-2"></i> Parameter & Eksekusi Evaluasi Massal
        </h5>
        <span class="badge badge-primary px-2 py-1">Engine Siap</span>
      </div>
      <div class="card-body p-4">
        
        <?= form_open('evaluasi/proses_massal', array('id' => 'formMassal', 'onsubmit' => 'return confirmMassal(this);')) ?>
          
          <h6 class="font-weight-bold text-dark mb-3">1. Pilih Cakupan Pemrosesan Data:</h6>

          <div class="custom-control custom-radio mb-3 p-3 rounded border bg-light">
            <input type="radio" id="scopeReset" name="scope" value="reset_all" class="custom-control-input" checked>
            <label class="custom-control-label font-weight-bold text-dark" for="scopeReset">
              <span class="text-primary font-weight-bold">Evaluasi Ulang Penuh & Bersih (Reset & All 560 Data)</span> — <span class="badge badge-success">Rekomendasi Riset</span>
            </label>
            <p class="text-muted small mb-0 mt-1 pl-4">
              Menghapus data simulasi/pengujian lama di tabel evaluasi, lalu menjalankan penalaran Fuzzy Mamdani secara serentak untuk seluruh <?= number_format($total_umkm) ?> UMKM terdaftar. Menjamin konsistensi data statistik penelitian 100%.
            </p>
          </div>

          <div class="custom-control custom-radio mb-3 p-3 rounded border">
            <input type="radio" id="scopeAll" name="scope" value="all" class="custom-control-input">
            <label class="custom-control-label font-weight-bold text-dark" for="scopeAll">
              Tambahkan Evaluasi ke Riwayat Tanpa Menghapus Data Lama
            </label>
            <p class="text-muted small mb-0 mt-1 pl-4">
              Mengevaluasi seluruh <?= number_format($total_umkm) ?> UMKM dan menyimpannya sebagai catatan riwayat baru tanpa menghapus riwayat sebelumnya.
            </p>
          </div>

          <?php if ($total_pending > 0): ?>
          <div class="custom-control custom-radio mb-3 p-3 rounded border">
            <input type="radio" id="scopePending" name="scope" value="pending_only" class="custom-control-input">
            <label class="custom-control-label font-weight-bold text-dark" for="scopePending">
              Evaluasi Hanya UMKM yang Belum Pernah Dievaluasi (<?= number_format($total_pending) ?> Usaha)
            </label>
            <p class="text-muted small mb-0 mt-1 pl-4">
              Hanya memproses UMKM yang belum memiliki riwayat evaluasi di sistem.
            </p>
          </div>
          <?php endif; ?>

          <hr class="my-4">

          <h6 class="font-weight-bold text-dark mb-3">2. Variabel Masukan yang Dihubungkan Secara Otomatis:</h6>
          <div class="table-responsive mb-4">
            <table class="table table-sm table-bordered small">
              <thead class="bg-light">
                <tr>
                  <th>Variabel Input</th>
                  <th>Kolom Database</th>
                  <th>Fungsi Keanggotaan</th>
                  <th>Domain Prototipe</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Input 1: Nilai Investasi / Modal</strong></td>
                  <td><code>umkm.nilai_investasi</code></td>
                  <td>Rendah (Trapesium), Sedang (Segitiga), Tinggi (Trapesium)</td>
                  <td>Rp500.000 — Rp900.000.000</td>
                </tr>
                <tr>
                  <td><strong>Input 2: Lama Usaha</strong></td>
                  <td><code>umkm.lama_usaha</code></td>
                  <td>Pendek (Trapesium), Menengah (Segitiga), Panjang (Trapesium)</td>
                  <td>1 — 15 Tahun</td>
                </tr>
                <tr>
                  <td><strong>Input 3: Stabilitas Pendapatan</strong></td>
                  <td><code>umkm.stabilitas_pendapatan</code></td>
                  <td>Kategori Tegas Singleton (Fluktuatif, Cukup stabil, Stabil)</td>
                  <td>3 Kategori Nominal</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="alert alert-warning small mb-4">
            <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Catatan Integritas Ilmiah:</strong>
            Sistem menjalankan 27 aturan logika fuzzy Mamdani dengan operator MIN, implikasi MIN, agregasi MAX, dan defuzzifikasi Centroid secara presisi pada setiap UMKM. Distribusi hasil akan sepenuhnya mengikuti kondisi riil data UMKM.
          </div>

          <div class="text-right">
            <button type="submit" class="btn btn-warning btn-lg px-4 font-weight-bold shadow-sm" id="btnProsesMassal">
              <i class="fas fa-bolt mr-2"></i> Jalankan Evaluasi Massal Sekarang
            </button>
          </div>

        <?= form_close() ?>

      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <!-- Status Data Terkini -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white">
        <h6 class="card-title m-0 font-weight-bold">
          <i class="fas fa-database text-info mr-2"></i> Status Database Saat Ini
        </h6>
      </div>
      <div class="card-body p-3">
        <ul class="list-group list-group-flush small">
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            <span><i class="fas fa-store text-primary mr-2"></i> Total UMKM Terdaftar:</span>
            <span class="badge badge-primary badge-pill font-weight-bold" style="font-size: 0.95rem;"><?= number_format($total_umkm) ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            <span><i class="fas fa-check-double text-success mr-2"></i> UMKM Sudah Dievaluasi:</span>
            <span class="badge badge-success badge-pill font-weight-bold" style="font-size: 0.95rem;"><?= number_format($total_evaluated_unique) ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            <span><i class="fas fa-clock text-warning mr-2"></i> UMKM Belum Dievaluasi:</span>
            <span class="badge badge-warning badge-pill font-weight-bold" style="font-size: 0.95rem;"><?= number_format($total_pending) ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-0">
            <span><i class="fas fa-layer-group text-secondary mr-2"></i> Total Catatan Evaluasi:</span>
            <span class="badge badge-secondary badge-pill font-weight-bold" style="font-size: 0.95rem;"><?= number_format($total_all_evaluations) ?></span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Informasi Model Fuzzy Aktif -->
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h6 class="card-title m-0 font-weight-bold">
          <i class="fas fa-brain text-primary mr-2"></i> Spesifikasi Model Aktif
        </h6>
      </div>
      <div class="card-body p-3 small text-muted">
        <p class="mb-1"><strong>Kode Model:</strong> <span class="badge badge-dark"><?= html_escape($active_model['model_code']) ?></span></p>
        <p class="mb-1"><strong>Nama:</strong> <?= html_escape($active_model['model_name']) ?></p>
        <p class="mb-1"><strong>Versi:</strong> v<?= html_escape($active_model['version']) ?></p>
        <p class="mb-1"><strong>Basis Aturan:</strong> 27 Aturan Kombinasi Penuh</p>
        <p class="mb-1"><strong>Operator Implikasi:</strong> MIN</p>
        <p class="mb-1"><strong>Operator Agregasi:</strong> MAX</p>
        <p class="mb-0"><strong>Defuzzifikasi:</strong> Centroid (Titik Pusat Gravitasi)</p>
      </div>
    </div>
  </div>
</div>

<script>
function confirmMassal(form) {
  var scope = $('input[name="scope"]:checked').val();
  var msg = 'Apakah Anda yakin ingin menjalankan proses evaluasi massal untuk ' + '<?= number_format($total_umkm) ?>' + ' UMKM?';
  if (scope === 'reset_all') {
    msg = 'Perhatian: Riwayat evaluasi simulasi/lama akan dibersihkan dan seluruh ' + '<?= number_format($total_umkm) ?>' + ' UMKM akan dievaluasi ulang secara bersih.\n\nLanjutkan proses evaluasi massal?';
  }
  
  if (confirm(msg)) {
    var btn = $('#btnProsesMassal');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses Evaluasi 560 UMKM...');
    return true;
  }
  return false;
}
</script>
