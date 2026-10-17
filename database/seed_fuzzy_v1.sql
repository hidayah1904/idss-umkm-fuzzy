-- =====================================================================
-- Seed Data: Fuzzy Mamdani Model v1.0
-- IDSS UMKM
-- =====================================================================

USE `idss_umkm`;

INSERT INTO `fuzzy_models` (`model_code`, `model_name`, `version`, `method`, `configuration_json`, `rule_base_json`, `is_active`, `notes`, `created_by`, `created_at`)
VALUES
('MAMDANI-V1.0', 'Model Penalaran Fuzzy Mamdani Evaluasi Kelayakan Usaha UMKM v1.0', '1.0.0', 'Mamdani',
'{
  "name": "Model Penalaran Fuzzy Mamdani Evaluasi Kelayakan Usaha UMKM",
  "version": "1.0.0",
  "method": "Mamdani",
  "description": "Model penalaran fuzzy 3 input (Modal, Lama Usaha, Stabilitas Pendapatan) dengan 27 aturan MIN-MAX dan defuzzifikasi Centroid.",
  "domains": {
    "modal": {"min": 500000, "max": 900000000, "unit": "Rupiah", "label": "Nilai Investasi/Modal"},
    "lama_usaha": {"min": 1, "max": 15, "unit": "Tahun", "label": "Lama Usaha"},
    "stabilitas": {"values": ["Fluktuatif", "Cukup stabil", "Stabil"], "unit": "Kategori", "label": "Stabilitas Pendapatan"},
    "output_skor": {"min": 0, "max": 100, "unit": "Skor Kelayakan", "label": "Indeks Kelayakan Usaha"}
  },
  "membership_functions": {
    "modal": {
      "rendah": {"type": "trapezoid", "params": [500000, 500000, 5000000, 14000000], "label": "Rendah", "level": 0},
      "sedang": {"type": "triangle", "params": [5000000, 14000000, 50000000], "label": "Sedang", "level": 1},
      "tinggi": {"type": "trapezoid", "params": [14000000, 50000000, 900000000, 900000000], "label": "Tinggi", "level": 2}
    },
    "lama_usaha": {
      "pendek": {"type": "trapezoid", "params": [1, 1, 4, 8], "label": "Pendek", "level": 0},
      "menengah": {"type": "triangle", "params": [4, 8, 11], "label": "Menengah", "level": 1},
      "panjang": {"type": "trapezoid", "params": [8, 11, 15, 15], "label": "Panjang", "level": 2}
    },
    "stabilitas": {
      "fluktuatif": {"type": "singleton", "value": "Fluktuatif", "label": "Fluktuatif", "level": 0},
      "cukup_stabil": {"type": "singleton", "value": "Cukup stabil", "label": "Cukup stabil", "level": 1},
      "stabil": {"type": "singleton", "value": "Stabil", "label": "Stabil", "level": 2}
    },
    "output": {
      "rendah": {"type": "trapezoid", "params": [0, 0, 30, 50], "label": "Rendah"},
      "sedang": {"type": "triangle", "params": [30, 50, 70], "label": "Sedang"},
      "tinggi": {"type": "trapezoid", "params": [50, 70, 100, 100], "label": "Tinggi"}
    }
  },
  "operators": {
    "and": "MIN",
    "implication": "MIN",
    "aggregation": "MAX",
    "defuzzification": "Centroid",
    "discretization_step": 0.1
  },
  "categories": [
    {"label": "Tidak Layak", "min": 0, "max": 40, "color": "danger", "badge": "badge-danger", "description": "Skor evaluasi di bawah 40"},
    {"label": "Cukup Layak", "min": 40, "max": 70, "color": "warning", "badge": "badge-warning", "description": "Skor evaluasi 40 hingga kurang dari 70"},
    {"label": "Layak", "min": 70, "max": 100, "color": "success", "badge": "badge-success", "description": "Skor evaluasi 70 hingga 100"}
  ]
}',
'[
  {"rule_no": 1, "modal": "Rendah", "modal_level": 0, "lama_usaha": "Pendek", "lama_level": 0, "stabilitas": "Fluktuatif", "stabilitas_level": 0, "total_level": 0, "output": "Rendah"},
  {"rule_no": 2, "modal": "Rendah", "modal_level": 0, "lama_usaha": "Pendek", "lama_level": 0, "stabilitas": "Cukup stabil", "stabilitas_level": 1, "total_level": 1, "output": "Rendah"},
  {"rule_no": 3, "modal": "Rendah", "modal_level": 0, "lama_usaha": "Pendek", "lama_level": 0, "stabilitas": "Stabil", "stabilitas_level": 2, "total_level": 2, "output": "Sedang"},
  {"rule_no": 4, "modal": "Rendah", "modal_level": 0, "lama_usaha": "Menengah", "lama_level": 1, "stabilitas": "Fluktuatif", "stabilitas_level": 0, "total_level": 1, "output": "Rendah"},
  {"rule_no": 5, "modal": "Rendah", "modal_level": 0, "lama_usaha": "Menengah", "lama_level": 1, "stabilitas": "Cukup stabil", "stabilitas_level": 1, "total_level": 2, "output": "Sedang"},
  {"rule_no": 6, "modal": "Rendah", "modal_level": 0, "lama_usaha": "Menengah", "lama_level": 1, "stabilitas": "Stabil", "stabilitas_level": 2, "total_level": 3, "output": "Sedang"},
  {"rule_no": 7, "modal": "Rendah", "modal_level": 0, "lama_usaha": "Panjang", "lama_level": 2, "stabilitas": "Fluktuatif", "stabilitas_level": 0, "total_level": 2, "output": "Sedang"},
  {"rule_no": 8, "modal": "Rendah", "modal_level": 0, "lama_usaha": "Panjang", "lama_level": 2, "stabilitas": "Cukup stabil", "stabilitas_level": 1, "total_level": 3, "output": "Sedang"},
  {"rule_no": 9, "modal": "Rendah", "modal_level": 0, "lama_usaha": "Panjang", "lama_level": 2, "stabilitas": "Stabil", "stabilitas_level": 2, "total_level": 4, "output": "Sedang"},
  {"rule_no": 10, "modal": "Sedang", "modal_level": 1, "lama_usaha": "Pendek", "lama_level": 0, "stabilitas": "Fluktuatif", "stabilitas_level": 0, "total_level": 1, "output": "Rendah"},
  {"rule_no": 11, "modal": "Sedang", "modal_level": 1, "lama_usaha": "Pendek", "lama_level": 0, "stabilitas": "Cukup stabil", "stabilitas_level": 1, "total_level": 2, "output": "Sedang"},
  {"rule_no": 12, "modal": "Sedang", "modal_level": 1, "lama_usaha": "Pendek", "lama_level": 0, "stabilitas": "Stabil", "stabilitas_level": 2, "total_level": 3, "output": "Sedang"},
  {"rule_no": 13, "modal": "Sedang", "modal_level": 1, "lama_usaha": "Menengah", "lama_level": 1, "stabilitas": "Fluktuatif", "stabilitas_level": 0, "total_level": 2, "output": "Sedang"},
  {"rule_no": 14, "modal": "Sedang", "modal_level": 1, "lama_usaha": "Menengah", "lama_level": 1, "stabilitas": "Cukup stabil", "stabilitas_level": 1, "total_level": 3, "output": "Sedang"},
  {"rule_no": 15, "modal": "Sedang", "modal_level": 1, "lama_usaha": "Menengah", "lama_level": 1, "stabilitas": "Stabil", "stabilitas_level": 2, "total_level": 4, "output": "Sedang"},
  {"rule_no": 16, "modal": "Sedang", "modal_level": 1, "lama_usaha": "Panjang", "lama_level": 2, "stabilitas": "Fluktuatif", "stabilitas_level": 0, "total_level": 3, "output": "Sedang"},
  {"rule_no": 17, "modal": "Sedang", "modal_level": 1, "lama_usaha": "Panjang", "lama_level": 2, "stabilitas": "Cukup stabil", "stabilitas_level": 1, "total_level": 4, "output": "Sedang"},
  {"rule_no": 18, "modal": "Sedang", "modal_level": 1, "lama_usaha": "Panjang", "lama_level": 2, "stabilitas": "Stabil", "stabilitas_level": 2, "total_level": 5, "output": "Tinggi"},
  {"rule_no": 19, "modal": "Tinggi", "modal_level": 2, "lama_usaha": "Pendek", "lama_level": 0, "stabilitas": "Fluktuatif", "stabilitas_level": 0, "total_level": 2, "output": "Sedang"},
  {"rule_no": 20, "modal": "Tinggi", "modal_level": 2, "lama_usaha": "Pendek", "lama_level": 0, "stabilitas": "Cukup stabil", "stabilitas_level": 1, "total_level": 3, "output": "Sedang"},
  {"rule_no": 21, "modal": "Tinggi", "modal_level": 2, "lama_usaha": "Pendek", "lama_level": 0, "stabilitas": "Stabil", "stabilitas_level": 2, "total_level": 4, "output": "Sedang"},
  {"rule_no": 22, "modal": "Tinggi", "modal_level": 2, "lama_usaha": "Menengah", "lama_level": 1, "stabilitas": "Fluktuatif", "stabilitas_level": 0, "total_level": 3, "output": "Sedang"},
  {"rule_no": 23, "modal": "Tinggi", "modal_level": 2, "lama_usaha": "Menengah", "lama_level": 1, "stabilitas": "Cukup stabil", "stabilitas_level": 1, "total_level": 4, "output": "Sedang"},
  {"rule_no": 24, "modal": "Tinggi", "modal_level": 2, "lama_usaha": "Menengah", "lama_level": 1, "stabilitas": "Stabil", "stabilitas_level": 2, "total_level": 5, "output": "Tinggi"},
  {"rule_no": 25, "modal": "Tinggi", "modal_level": 2, "lama_usaha": "Panjang", "lama_level": 2, "stabilitas": "Fluktuatif", "stabilitas_level": 0, "total_level": 4, "output": "Sedang"},
  {"rule_no": 26, "modal": "Tinggi", "modal_level": 2, "lama_usaha": "Panjang", "lama_level": 2, "stabilitas": "Cukup stabil", "stabilitas_level": 1, "total_level": 5, "output": "Tinggi"},
  {"rule_no": 27, "modal": "Tinggi", "modal_level": 2, "lama_usaha": "Panjang", "lama_level": 2, "stabilitas": "Stabil", "stabilitas_level": 2, "total_level": 6, "output": "Tinggi"}
]',
1, 'Basis aturan v1.0 prototipe 27 kombinasi level 0-2 (MIN-MAX-Centroid).', 1, NOW())
ON DUPLICATE KEY UPDATE `configuration_json` = VALUES(`configuration_json`), `rule_base_json` = VALUES(`rule_base_json`);
