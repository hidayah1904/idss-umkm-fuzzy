<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-lg-8 offset-lg-2">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0">
          <i class="fas fa-file-import text-primary mr-2"></i> Impor Dataset UMKM (Excel / CSV)
        </h5>
        <a href="<?= base_url('umkm') ?>" class="btn btn-sm btn-light border">
          <i class="fas fa-arrow-left mr-1"></i> Kembali ke Data UMKM
        </a>
      </div>
      <div class="card-body p-4">
        
        <?= form_open_multipart('umkm/import') ?>
          
          <div class="form-group mb-4">
            <label class="font-weight-600">Pilih File Dataset (.xlsx atau .csv) <span class="text-danger">*</span></label>
            <div class="custom-file">
              <input type="file" name="file_dataset" class="custom-file-input" id="datasetFileInput" accept=".csv, .xlsx, .xls" required onchange="updateFileName(this)">
              <label class="custom-file-label" for="datasetFileInput" id="fileLabel">Pilih file spreadsheet...</label>
            </div>
            <small class="text-muted d-block mt-1">Mendukung format Microsoft Excel (.xlsx) dan CSV dengan ukuran maksimal 10 MB.</small>
          </div>

          <div class="form-group mb-4 bg-light p-3 rounded border">
            <label class="font-weight-600 text-dark mb-2">
              <i class="fas fa-shield-alt text-primary mr-1"></i> Strategi Penanganan ID UMKM Duplikat
            </label>
            <div class="custom-control custom-radio mb-2">
              <input type="radio" id="stratSkip" name="duplicate_strategy" value="skip" class="custom-control-input" checked>
              <label class="custom-control-label font-weight-500" for="stratSkip">
                <strong>Lewati baris data (Skip)</strong> — Pertahankan data lama di database jika ID UMKM sudah terdaftar.
              </label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="stratUpdate" name="duplicate_strategy" value="update" class="custom-control-input">
              <label class="custom-control-label font-weight-500" for="stratUpdate">
                <strong>Perbarui data yang sudah ada (Update)</strong> — Timpa baris lama dengan data baru dari file yang diunggah.
              </label>
            </div>
          </div>

          <div class="alert alert-info py-2 px-3 small mb-4">
            <h6 class="font-weight-bold mb-1"><i class="fas fa-info-circle mr-1"></i> Fitur Parser Pintar:</h6>
            <ul class="mb-0 pl-3">
              <li>Mendeteksi header kolom secara otomatis meskipun file memiliki baris judul atas.</li>
              <li>Secara otomatis mengabaikan baris kosong, sel kosong, serta baris footer ringkasan (seperti baris "JUMLAH" / "TOTAL").</li>
              <li>Membersihkan format mata uang ("Rp"), titik/koma pemisah ribuan secara otomatis.</li>
              <li>Menggunakan transaksi database: jika terjadi gangguan kritis, database tetap aman dan tidak rusak.</li>
            </ul>
          </div>

          <div class="text-right">
            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm" id="btnUpload">
              <i class="fas fa-cloud-upload-alt mr-2"></i> Mulai Proses Impor
            </button>
          </div>

        <?= form_close() ?>

        <hr class="my-4">

        <h6 class="font-weight-bold text-dark mb-2">
          <i class="fas fa-table mr-1 text-secondary"></i> Spesifikasi Header Kolom Sumber Penelitian
        </h6>
        <p class="text-muted small">Sistem mengenali secara langsung kolom-kolom standar dataset UMKM Kabupaten Soppeng:</p>

        <div class="table-responsive">
          <table class="table table-sm table-bordered small text-muted">
            <thead class="bg-light">
              <tr>
                <th>Kolom Header</th>
                <th>Tipe Data</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <tr><td><code>ID UMKM</code></td><td>String (Teks)</td><td>Pengenal unik usaha (e.g. UMKM-0001)</td></tr>
              <tr><td><code>UMKM</code> / <code>Nama UMKM</code></td><td>String (Teks)</td><td>Nama usaha atau pemilik</td></tr>
              <tr><td><code>Bidang Usaha</code></td><td>String (Teks)</td><td>Sektor bisnis (e.g. Bengkel, Kuliner)</td></tr>
              <tr><td><code>Nilai Investasi/Modal</code></td><td>Decimal / Numerik</td><td>Variabel Masukan 1 Fuzzy Mamdani (Rp)</td></tr>
              <tr><td><code>Lama Usaha (Tahun)</code></td><td>Decimal / Numerik</td><td>Variabel Masukan 2 Fuzzy Mamdani (Tahun)</td></tr>
              <tr><td><code>Stabilitas Pendapatan</code></td><td>Kategori (Teks)</td><td>Variabel Masukan 3 Fuzzy: Stabil, Cukup stabil, Fluktuatif</td></tr>
              <tr><td><code>KEC</code> & <code>DESA/KEL</code></td><td>String (Teks)</td><td>Kecamatan dan Desa/Kelurahan</td></tr>
              <tr><td><code>NILAI OMZET (Rp)</code></td><td>Decimal / Numerik</td><td>Omzet keuangan tahunan/bulanan</td></tr>
              <tr><td><code>NILAI LABA (Rp)</code></td><td>Decimal / Numerik</td><td>Laba bersih usaha</td></tr>
              <tr><td><code>Status Usaha</code></td><td>String (Teks)</td><td>Status administratif (Aktif / Perlu verifikasi)</td></tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
function updateFileName(input) {
  var fileName = input.files[0] ? input.files[0].name : "Pilih file spreadsheet...";
  document.getElementById("fileLabel").innerText = fileName;
}
</script>
