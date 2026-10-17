-- =====================================================================
-- IDSS UMKM — Fuzzy Mamdani Decision Support System
-- Model Penalaran Fuzzy untuk Evaluasi Kelayakan Usaha UMKM
-- Database Schema & Initial Data
-- Compatibility: MySQL 5.7+ / MariaDB 10.2+ / phpMyAdmin
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `idss_umkm` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `idss_umkm`;

-- ---------------------------------------------------------------------
-- Table: users
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `evaluations`;
DROP TABLE IF EXISTS `fuzzy_models`;
DROP TABLE IF EXISTS `umkm`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'operator') NOT NULL DEFAULT 'operator',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`),
  KEY `idx_role` (`role`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: umkm
-- ---------------------------------------------------------------------
CREATE TABLE `umkm` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_umkm` VARCHAR(50) NOT NULL,
  `nama_umkm` VARCHAR(150) NOT NULL,
  `bidang_usaha` VARCHAR(150) NULL DEFAULT NULL,
  `nilai_investasi` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `kecamatan` VARCHAR(100) NULL DEFAULT NULL,
  `desa_kelurahan` VARCHAR(100) NULL DEFAULT NULL,
  `nilai_omzet` DECIMAL(18,2) NULL DEFAULT 0.00,
  `nilai_laba` DECIMAL(18,2) NULL DEFAULT 0.00,
  `lama_usaha` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `biaya_operasional` DECIMAL(18,2) NULL DEFAULT 0.00,
  `arus_kas_bersih` DECIMAL(18,2) NULL DEFAULT 0.00,
  `stabilitas_pendapatan` ENUM('Fluktuatif', 'Cukup stabil', 'Stabil') NOT NULL DEFAULT 'Cukup stabil',
  `status_usaha` VARCHAR(50) NOT NULL DEFAULT 'Aktif',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_id_umkm` (`id_umkm`),
  KEY `idx_nama_umkm` (`nama_umkm`),
  KEY `idx_kecamatan` (`kecamatan`),
  KEY `idx_bidang_usaha` (`bidang_usaha`),
  KEY `idx_stabilitas` (`stabilitas_pendapatan`),
  KEY `idx_status_usaha` (`status_usaha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: fuzzy_models
-- ---------------------------------------------------------------------
CREATE TABLE `fuzzy_models` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_code` VARCHAR(50) NOT NULL,
  `model_name` VARCHAR(150) NOT NULL,
  `version` VARCHAR(20) NOT NULL,
  `method` VARCHAR(50) NOT NULL DEFAULT 'Mamdani',
  `configuration_json` LONGTEXT NOT NULL,
  `rule_base_json` LONGTEXT NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `notes` TEXT NULL DEFAULT NULL,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_model_code` (`model_code`),
  KEY `idx_is_active` (`is_active`),
  KEY `fk_fuzzy_models_user` (`created_by`),
  CONSTRAINT `fk_fuzzy_models_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: evaluations
-- ---------------------------------------------------------------------
CREATE TABLE `evaluations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `umkm_id` INT UNSIGNED NOT NULL,
  `fuzzy_model_id` INT UNSIGNED NOT NULL,
  `input_modal` DECIMAL(18,2) NOT NULL,
  `input_lama_usaha` DECIMAL(6,2) NOT NULL,
  `input_stabilitas` VARCHAR(50) NOT NULL,
  `score` DECIMAL(6,2) NOT NULL,
  `category` ENUM('Tidak Layak', 'Cukup Layak', 'Layak') NOT NULL,
  `result_details_json` LONGTEXT NOT NULL,
  `evaluated_by` INT UNSIGNED NULL DEFAULT NULL,
  `evaluated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_umkm_id` (`umkm_id`),
  KEY `idx_fuzzy_model_id` (`fuzzy_model_id`),
  KEY `idx_category` (`category`),
  KEY `idx_evaluated_at` (`evaluated_at`),
  KEY `fk_evaluations_user` (`evaluated_by`),
  CONSTRAINT `fk_evaluations_umkm` FOREIGN KEY (`umkm_id`) REFERENCES `umkm` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_evaluations_model` FOREIGN KEY (`fuzzy_model_id`) REFERENCES `fuzzy_models` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_evaluations_user` FOREIGN KEY (`evaluated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: activity_logs
-- ---------------------------------------------------------------------
CREATE TABLE `activity_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NULL DEFAULT NULL,
  `entity_id` INT UNSIGNED NULL DEFAULT NULL,
  `description` TEXT NOT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Default Seed Data: users
-- Passwords:
-- admin: Admin#2026!
-- operator: Operator#2026!
-- ---------------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `is_active`, `created_at`) VALUES
(1, 'Administrator IDSS', 'admin', '$2y$10$EMcrh73ZXq/RWL1MkYS4vuuvoCDzuzVqjWjaQBFFUVlGlaGBMfdWy', 'admin', 1, NOW()),
(2, 'Operator Evaluasi', 'operator', '$2y$10$1YCv/MciHQni/y8OEiBYUO/wr.uVGfJAJDT1SmccMbQ5eZPIeEN1q', 'operator', 1, NOW());

-- ---------------------------------------------------------------------
-- Default Seed Data: fuzzy_models (Mamdani v1.0)
-- ---------------------------------------------------------------------
INSERT INTO `fuzzy_models` (`id`, `model_code`, `model_name`, `version`, `method`, `configuration_json`, `rule_base_json`, `is_active`, `notes`, `created_by`, `created_at`) VALUES
(1, 'MAMDANI-V1.0', 'Model Penalaran Fuzzy Mamdani Evaluasi Kelayakan Usaha UMKM v1.0', '1.0.0', 'Mamdani',
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
1, 'Basis aturan v1.0 prototipe 27 kombinasi level 0-2 (MIN-MAX-Centroid). Batas kategori: <40 Tidak Layak, 40-69 Cukup Layak, >=70 Layak.', 1, NOW());

-- ---------------------------------------------------------------------
-- Initial Activity Log
-- ---------------------------------------------------------------------
INSERT INTO `activity_logs` (`user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES
(1, 'SYSTEM_INIT', 'system', NULL, 'Inisialisasi sistem IDSS UMKM Fuzzy Mamdani dan model evaluasi v1.0', '127.0.0.1', NOW());

SET FOREIGN_KEY_CHECKS = 1;
