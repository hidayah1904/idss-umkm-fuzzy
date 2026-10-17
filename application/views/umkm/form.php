<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $is_edit = isset($umkm); ?>

<div class="row">
  <div class="col-lg-10 offset-lg-1">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0">
          <i class="fas <?= $is_edit ? 'fa-edit text-warning' : 'fa-plus-circle text-primary' ?> mr-2"></i>
          <?= $is_edit ? 'Ubah Data UMKM: ' . html_escape($umkm['nama_umkm']) : 'Tambah Data UMKM Baru' ?>
        </h5>
        <a href="<?= $is_edit ? base_url('umkm/detail/' . $umkm['id']) : base_url('umkm') ?>" class="btn btn-sm btn-light border">
          <i class="fas fa-times mr-1"></i> Batal
        </a>
      </div>
      <div class="card-body p-4">
        <?= form_open($is_edit ? 'umkm/ubah/' . $umkm['id'] : 'umkm/tambah') ?>

          <h6 class="font-weight-bold text-primary mb-3">
            <i class="fas fa-id-card mr-1"></i> Identitas & Lokasi Usaha
          </h6>
          <div class="row">
            <div class="col-md-4 form-group">
              <label class="font-weight-600">ID UMKM <span class="text-danger">*</span></label>
              <input type="text" name="id_umkm" class="form-control" placeholder="Contoh: UMKM-0561" value="<?= set_value('id_umkm', $is_edit ? $umkm['id_umkm'] : '') ?>" required>
              <?= form_error('id_umkm', '<small class="text-danger">', '</small>') ?>
            </div>

            <div class="col-md-4 form-group">
              <label class="font-weight-600">Nama Usaha / Pemilik <span class="text-danger">*</span></label>
              <input type="text" name="nama_umkm" class="form-control" placeholder="Nama usaha atau pemilik" value="<?= set_value('nama_umkm', $is_edit ? $umkm['nama_umkm'] : '') ?>" required>
              <?= form_error('nama_umkm', '<small class="text-danger">', '</small>') ?>
            </div>

            <div class="col-md-4 form-group">
              <label class="font-weight-600">Bidang Usaha</label>
              <input type="text" name="bidang_usaha" class="form-control" placeholder="Contoh: KEDAI KOPI, BENGKEL" value="<?= set_value('bidang_usaha', $is_edit ? $umkm['bidang_usaha'] : '') ?>">
              <?= form_error('bidang_usaha', '<small class="text-danger">', '</small>') ?>
            </div>

            <div class="col-md-6 form-group">
              <label class="font-weight-600">Kecamatan</label>
              <input type="text" name="kecamatan" class="form-control" placeholder="Contoh: Lalabata, Lilirilau" value="<?= set_value('kecamatan', $is_edit ? $umkm['kecamatan'] : '') ?>">
              <?= form_error('kecamatan', '<small class="text-danger">', '</small>') ?>
            </div>

            <div class="col-md-6 form-group">
              <label class="font-weight-600">Desa / Kelurahan</label>
              <input type="text" name="desa_kelurahan" class="form-control" placeholder="Contoh: Lapajung, Paroto" value="<?= set_value('desa_kelurahan', $is_edit ? $umkm['desa_kelurahan'] : '') ?>">
              <?= form_error('desa_kelurahan', '<small class="text-danger">', '</small>') ?>
            </div>
          </div>

          <hr class="my-4">
          <h6 class="font-weight-bold text-primary mb-3">
            <i class="fas fa-calculator mr-1"></i> 3 Parameter Masukan Model Fuzzy Mamdani (v1.0)
          </h6>
          <div class="row bg-light p-3 rounded mb-3 border">
            <div class="col-md-4 form-group">
              <label class="font-weight-600">1. Nilai Investasi / Modal (Rp) <span class="text-danger">*</span></label>
              <input type="number" step="any" name="nilai_investasi" class="form-control" placeholder="Contoh: 15000000" value="<?= set_value('nilai_investasi', $is_edit ? $umkm['nilai_investasi'] : '10000000') ?>" required>
              <small class="text-muted">Domain prototipe: 500.000 - 900.000.000</small>
              <?= form_error('nilai_investasi', '<small class="text-danger d-block">', '</small>') ?>
            </div>

            <div class="col-md-4 form-group">
              <label class="font-weight-600">2. Lama Usaha (Tahun) <span class="text-danger">*</span></label>
              <input type="number" step="0.1" name="lama_usaha" class="form-control" placeholder="Contoh: 5" value="<?= set_value('lama_usaha', $is_edit ? $umkm['lama_usaha'] : '3') ?>" required>
              <small class="text-muted">Domain prototipe: 1 - 15 Tahun</small>
              <?= form_error('lama_usaha', '<small class="text-danger d-block">', '</small>') ?>
            </div>

            <div class="col-md-4 form-group">
              <label class="font-weight-600">3. Stabilitas Pendapatan <span class="text-danger">*</span></label>
              <?php $selected_stab = set_value('stabilitas_pendapatan', $is_edit ? $umkm['stabilitas_pendapatan'] : 'Cukup stabil'); ?>
              <select name="stabilitas_pendapatan" class="form-control custom-select" required>
                <option value="Fluktuatif" <?= ($selected_stab === 'Fluktuatif') ? 'selected' : '' ?>>Fluktuatif</option>
                <option value="Cukup stabil" <?= ($selected_stab === 'Cukup stabil') ? 'selected' : '' ?>>Cukup stabil</option>
                <option value="Stabil" <?= ($selected_stab === 'Stabil') ? 'selected' : '' ?>>Stabil</option>
              </select>
              <small class="text-muted">Kategori tegas prototipe</small>
              <?= form_error('stabilitas_pendapatan', '<small class="text-danger d-block">', '</small>') ?>
            </div>
          </div>

          <hr class="my-4">
          <h6 class="font-weight-bold text-info mb-3">
            <i class="fas fa-coins mr-1"></i> Data Keuangan Tambahan & Status Administratif
          </h6>
          <div class="row">
            <div class="col-md-3 col-sm-6 form-group">
              <label class="font-weight-600">Nilai Omzet (Rp)</label>
              <input type="number" step="any" name="nilai_omzet" class="form-control" value="<?= set_value('nilai_omzet', $is_edit ? $umkm['nilai_omzet'] : '0') ?>">
            </div>

            <div class="col-md-3 col-sm-6 form-group">
              <label class="font-weight-600">Nilai Laba (Rp)</label>
              <input type="number" step="any" name="nilai_laba" class="form-control" value="<?= set_value('nilai_laba', $is_edit ? $umkm['nilai_laba'] : '0') ?>">
            </div>

            <div class="col-md-3 col-sm-6 form-group">
              <label class="font-weight-600">Biaya Operasional (Rp)</label>
              <input type="number" step="any" name="biaya_operasional" class="form-control" value="<?= set_value('biaya_operasional', $is_edit ? $umkm['biaya_operasional'] : '0') ?>">
            </div>

            <div class="col-md-3 col-sm-6 form-group">
              <label class="font-weight-600">Arus Kas Bersih (Rp)</label>
              <input type="number" step="any" name="arus_kas_bersih" class="form-control" value="<?= set_value('arus_kas_bersih', $is_edit ? $umkm['arus_kas_bersih'] : '0') ?>">
            </div>

            <div class="col-md-4 form-group">
              <label class="font-weight-600">Status Usaha Administratif</label>
              <?php $selected_status = set_value('status_usaha', $is_edit ? $umkm['status_usaha'] : 'Aktif'); ?>
              <select name="status_usaha" class="form-control custom-select">
                <option value="Aktif" <?= ($selected_status === 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                <option value="Perlu verifikasi" <?= ($selected_status === 'Perlu verifikasi') ? 'selected' : '' ?>>Perlu verifikasi</option>
              </select>
              <small class="text-muted">Data administratif (bukan hasil fuzzy)</small>
            </div>
          </div>

          <div class="mt-4 pt-3 border-top d-flex justify-content-between">
            <a href="<?= base_url('umkm') ?>" class="btn btn-light border px-4">
              <i class="fas fa-arrow-left mr-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary px-4 shadow-sm">
              <i class="fas fa-save mr-1"></i> <?= $is_edit ? 'Simpan Perubahan' : 'Tambahkan UMKM' ?>
            </button>
          </div>

        <?= form_close() ?>
      </div>
    </div>
  </div>
</div>
