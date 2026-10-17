<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-lg-8 offset-lg-2">
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0">
          <i class="fas fa-calculator text-primary mr-2"></i> Form Evaluasi Kelayakan Usaha UMKM
        </h5>
        <span class="badge badge-light border">
          <i class="fas fa-brain text-primary mr-1"></i> Model: <?= html_escape($active_model['model_code']) ?> (v<?= html_escape($active_model['version']) ?>)
        </span>
      </div>
      <div class="card-body p-4">

        <!-- Banner Pintasan Evaluasi Massal -->
        <div class="alert alert-warning border d-flex flex-wrap align-items-center justify-content-between p-3 mb-4">
          <div class="d-flex align-items-center mb-2 mb-md-0">
            <i class="fas fa-bolt text-warning fa-2x mr-3"></i>
            <div>
              <strong class="d-block text-dark">Ingin Mengevaluasi Seluruh 560 UMKM Sekaligus?</strong>
              <small class="text-muted">Gunakan fitur Evaluasi Massal Otomatis untuk memproses seluruh dataset secara serentak.</small>
            </div>
          </div>
          <a href="<?= base_url('evaluasi/massal') ?>" class="btn btn-warning btn-sm font-weight-bold text-dark shadow-sm">
            <i class="fas fa-bolt mr-1"></i> Buka Evaluasi Massal
          </a>
        </div>

        <?= form_open('evaluasi/proses', array('id' => 'formEvaluasi')) ?>

          <!-- Pemilihan Data UMKM atau Mode Simulasi -->
          <div class="form-group mb-4">
            <label class="font-weight-600 text-dark">
              Pilih UMKM dari Database <span class="text-muted small font-weight-normal">(Atau biarkan kosong untuk Mode Simulasi)</span>
            </label>
            
            <div class="input-group">
              <input type="text" id="umkmSearchInput" class="form-control" placeholder="Ketik ID UMKM atau Nama Usaha untuk mencari..." autocomplete="off">
              <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary" id="btnResetSelected" style="display: <?= $selected_umkm ? 'inline-block' : 'none' ?>;" onclick="resetSelectedUmkm()">
                  <i class="fas fa-times mr-1"></i> Batal Pilih
                </button>
              </div>
            </div>
            <div id="searchResultsDropdown" class="list-group shadow-sm position-absolute w-100" style="z-index: 1050; display: none; max-height: 250px; overflow-y: auto;"></div>
            
            <input type="hidden" name="umkm_id" id="hiddenUmkmId" value="<?= $selected_umkm ? $selected_umkm['id'] : '' ?>">
          </div>

          <!-- Card Info UMKM Terpilih -->
          <div id="selectedUmkmCard" class="card bg-light border p-3 mb-4" style="display: <?= $selected_umkm ? 'block' : 'none' ?>;">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="badge badge-primary px-2 py-1 mr-1" id="dispIdUmkm"><?= $selected_umkm ? html_escape($selected_umkm['id_umkm']) : '' ?></span>
                <strong class="text-dark" id="dispNamaUmkm" style="font-size: 1.1rem;"><?= $selected_umkm ? html_escape($selected_umkm['nama_umkm']) : '' ?></strong>
                <div class="text-muted small mt-1">
                  <span id="dispBidangUsaha"><?= $selected_umkm ? html_escape($selected_umkm['bidang_usaha']) : '' ?></span> &bull; 
                  <span id="dispLokasi"><?= $selected_umkm ? html_escape($selected_umkm['kecamatan'] . ', ' . $selected_umkm['desa_kelurahan']) : '' ?></span>
                </div>
              </div>
              <span class="badge badge-success px-3 py-2"><i class="fas fa-check mr-1"></i> Data Terhubung</span>
            </div>
          </div>

          <hr class="my-4">

          <h6 class="font-weight-bold text-primary mb-3">
            <i class="fas fa-sliders-h mr-1"></i> 3 Variabel Masukan Inferensi Fuzzy Mamdani
          </h6>

          <!-- Input 1: Modal -->
          <div class="form-group mb-3">
            <label class="font-weight-600 text-dark">
              1. Nilai Investasi / Modal Usaha (Rp) <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-light font-weight-bold">Rp</span>
              </div>
              <input type="number" step="any" name="modal" id="inputModal" class="form-control form-control-lg font-weight-bold text-primary" placeholder="Contoh: 15000000" value="<?= $selected_umkm ? $selected_umkm['nilai_investasi'] : set_value('modal', '15000000') ?>" required>
            </div>
            <small class="text-muted d-block mt-1">
              Domain prototipe v1.0: <strong>Rp500.000 s.d. Rp900.000.000</strong>. Himpunan: Rendah (&le;14 jt), Sedang (5 jt - 50 jt), Tinggi (&ge;14 jt).
            </small>
          </div>

          <!-- Input 2: Lama Usaha -->
          <div class="form-group mb-3">
            <label class="font-weight-600 text-dark">
              2. Lama Usaha Operasional (Tahun) <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <input type="number" step="0.1" name="lama_usaha" id="inputLamaUsaha" class="form-control form-control-lg font-weight-bold text-dark" placeholder="Contoh: 5" value="<?= $selected_umkm ? $selected_umkm['lama_usaha'] : set_value('lama_usaha', '5') ?>" required>
              <div class="input-group-append">
                <span class="input-group-text bg-light">Tahun</span>
              </div>
            </div>
            <small class="text-muted d-block mt-1">
              Domain prototipe v1.0: <strong>1 s.d. 15 Tahun</strong>. Himpunan: Pendek (&le;8 th), Menengah (4 - 11 th), Panjang (&ge;8 th).
            </small>
          </div>

          <!-- Input 3: Stabilitas Pendapatan -->
          <div class="form-group mb-4">
            <label class="font-weight-600 text-dark">
              3. Stabilitas Pendapatan Usaha <span class="text-danger">*</span>
            </label>
            <?php 
              $current_stab = $selected_umkm ? $selected_umkm['stabilitas_pendapatan'] : set_value('stabilitas', 'Cukup stabil'); 
            ?>
            <select name="stabilitas" id="selectStabilitas" class="form-control custom-select form-control-lg font-weight-bold" required>
              <option value="Fluktuatif" <?= ($current_stab === 'Fluktuatif') ? 'selected' : '' ?>>Fluktuatif (Level 0)</option>
              <option value="Cukup stabil" <?= ($current_stab === 'Cukup stabil') ? 'selected' : '' ?>>Cukup stabil (Level 1)</option>
              <option value="Stabil" <?= ($current_stab === 'Stabil') ? 'selected' : '' ?>>Stabil (Level 2)</option>
            </select>
            <small class="text-muted d-block mt-1">
              Kategori tegas prototipe v1.0 dengan derajat keanggotaan singleton (1.0 untuk kategori terpilih).
            </small>
          </div>

          <!-- Opsi Penyimpanan -->
          <div class="form-group bg-light p-3 rounded border mb-4" id="boxSimpanHasil">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="checkSimpan" name="simpan_hasil" value="1" <?= $selected_umkm ? 'checked' : '' ?>>
              <label class="custom-control-label font-weight-500" for="checkSimpan">
                Simpan hasil evaluasi ini secara permanen ke riwayat database UMKM
              </label>
            </div>
            <small class="text-muted d-block mt-1 pl-4">
              Jika tidak dicentang (atau dalam Mode Simulasi), evaluasi hanya ditampilkan secara langsung di layar tanpa mengubah riwayat database.
            </small>
          </div>

          <div class="text-right">
            <button type="submit" class="btn btn-primary btn-lg px-5 font-weight-bold shadow">
              <i class="fas fa-play-circle mr-2"></i> Proses Evaluasi Fuzzy
            </button>
          </div>

        <?= form_close() ?>

      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("umkmSearchInput");
  const dropdown = document.getElementById("searchResultsDropdown");
  const hiddenId = document.getElementById("hiddenUmkmId");
  const btnReset = document.getElementById("btnResetSelected");
  const selectedCard = document.getElementById("selectedUmkmCard");
  const checkSimpan = document.getElementById("checkSimpan");

  let timeout = null;

  searchInput.addEventListener("input", function() {
    clearTimeout(timeout);
    const query = this.value.trim();
    if (query.length < 2) {
      dropdown.style.display = "none";
      return;
    }

    timeout = setTimeout(function() {
      fetch("<?= base_url('umkm/ajax_search?term=') ?>" + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
          dropdown.innerHTML = "";
          if (data.results && data.results.length > 0) {
            data.results.forEach(item => {
              const u = item.umkm_data;
              const a = document.createElement("a");
              a.href = "#";
              a.className = "list-group-item list-group-item-action py-2";
              a.innerHTML = `<strong>[${u.id_umkm}]</strong> ${u.nama_umkm} <span class="text-muted small">&bull; ${u.bidang_usaha || '-'} (${u.kecamatan || '-'})</span>`;
              a.onclick = function(e) {
                e.preventDefault();
                selectUmkm(u);
              };
              dropdown.appendChild(a);
            });
            dropdown.style.display = "block";
          } else {
            dropdown.innerHTML = '<div class="list-group-item text-muted small py-2">Tidak ada UMKM ditemukan</div>';
            dropdown.style.display = "block";
          }
        });
    }, 300);
  });

  document.addEventListener("click", function(e) {
    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.style.display = "none";
    }
  });

  function selectUmkm(u) {
    hiddenId.value = u.id;
    document.getElementById("dispIdUmkm").innerText = u.id_umkm;
    document.getElementById("dispNamaUmkm").innerText = u.nama_umkm;
    document.getElementById("dispBidangUsaha").innerText = u.bidang_usaha || "-";
    document.getElementById("dispLokasi").innerText = (u.kecamatan || "-") + ", " + (u.desa_kelurahan || "-");
    
    document.getElementById("inputModal").value = u.nilai_investasi;
    document.getElementById("inputLamaUsaha").value = u.lama_usaha;
    document.getElementById("selectStabilitas").value = u.stabilitas_pendapatan;

    selectedCard.style.display = "block";
    btnReset.style.display = "inline-block";
    dropdown.style.display = "none";
    searchInput.value = "";
    checkSimpan.checked = true;
  }

  window.resetSelectedUmkm = function() {
    hiddenId.value = "";
    selectedCard.style.display = "none";
    btnReset.style.display = "none";
    checkSimpan.checked = false;
  };
});
</script>
