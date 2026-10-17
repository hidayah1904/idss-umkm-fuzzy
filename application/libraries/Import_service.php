<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Service Import Dataset UMKM (CSV & XLSX)
 * Menangani header eksplisit, baris judul, baris kosong, format rupiah, dan penanganan duplikat
 */
class Import_service {

    protected $CI;

    public function __construct()
    {
        if (function_exists('get_instance')) {
            $this->CI =& get_instance();
        }
    }

    /**
     * Parsing file Excel (.xlsx) atau CSV
     * 
     * @param string $file_path Lokasi file temporer
     * @param string $original_name Nama asli file
     * @return array Hasil parsing dan baris data
     */
    public function parse_file($file_path, $original_name = null)
    {
        // 1. Ekstrak ekstensi dari nama file asli jika tersedia
        $ext = '';
        if (!empty($original_name)) {
            $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        }

        // 2. Fallback: Ekstrak ekstensi dari path file di server jika nama asli tidak ada ekstensi
        if (empty($ext) && !empty($file_path)) {
            $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        }

        // 3. Inspeksi Konten Berkas (Magic Bytes) jika ekstensi belum terdeteksi secara pasti
        if (empty($ext) || !in_array($ext, array('csv', 'xlsx', 'xls'))) {
            if (file_exists($file_path)) {
                $handle = @fopen($file_path, 'rb');
                if ($handle) {
                    $header = fread($handle, 4);
                    fclose($handle);
                    if ($header === "PK\x03\x04") {
                        $ext = 'xlsx'; // File Excel OpenXML (berbasis ZIP)
                    } else {
                        $ext = 'csv';  // File teks / CSV
                    }
                }
            }
        }

        if ($ext === 'csv') {
            return $this->parse_csv($file_path);
        } elseif ($ext === 'xlsx') {
            return $this->parse_xlsx($file_path);
        } elseif ($ext === 'xls') {
            return array(
                'success' => false,
                'message' => 'Format file .xls (Excel 97-2003) tidak didukung secara langsung. Silakan buka file tersebut di Excel dan pilih "Save As" sebagai format Excel Workbook (.xlsx) atau CSV UTF-8 (.csv).',
                'records' => array(),
                'errors' => array()
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Format file tidak didukung. Harap unggah file berformat .csv atau .xlsx.',
                'records' => array(),
                'errors' => array()
            );
        }
    }

    /**
     * Parser CSV yang tangguh (Auto Delimiter, UTF-8 BOM Handler)
     */
    protected function parse_csv($file_path)
    {
        $handle = fopen($file_path, 'r');
        if (!$handle) {
            return array('success' => false, 'message' => 'Gagal membuka file CSV.', 'records' => array());
        }

        // Hapus BOM UTF-8 jika ada
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Deteksi delimiter (koma atau titik koma)
        $first_line = fgets($handle);
        rewind($handle);
        $delimiter = (substr_count($first_line, ';') > substr_count($first_line, ',')) ? ';' : ',';

        $raw_rows = array();
        while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
            $raw_rows[] = $row;
        }
        fclose($handle);

        return $this->process_raw_rows($raw_rows);
    }

    /**
     * Parser Native XLSX menggunakan ZipArchive dan SimpleXML
     */
    protected function parse_xlsx($file_path)
    {
        if (!class_exists('ZipArchive')) {
            return array('success' => false, 'message' => 'Ekstensi PHP ZipArchive tidak aktif pada server.', 'records' => array());
        }

        $zip = new ZipArchive();
        if ($zip->open($file_path) !== TRUE) {
            return array('success' => false, 'message' => 'Gagal membuka arsip file Excel (.xlsx).', 'records' => array());
        }

        // 1. Baca shared strings jika ada
        $shared_strings = array();
        $ss_xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ss_xml) {
            $xml_ss = simplexml_load_string($ss_xml);
            if ($xml_ss && isset($xml_ss->si)) {
                foreach ($xml_ss->si as $si) {
                    if (isset($si->t)) {
                        $shared_strings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string)$r->t;
                        }
                        $shared_strings[] = $text;
                    } else {
                        $shared_strings[] = '';
                    }
                }
            }
        }

        // 2. Baca sheet data (sheet1.xml)
        $sheet_xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheet_xml) {
            // Coba cari sheet pertama dari relasi jika nama bukan sheet1.xml
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (strpos($entry, 'xl/worksheets/sheet') === 0 && substr($entry, -4) === '.xml') {
                    $sheet_xml = $zip->getFromIndex($i);
                    break;
                }
            }
        }

        $zip->close();

        if (!$sheet_xml) {
            return array('success' => false, 'message' => 'Lembar kerja (sheet) tidak ditemukan dalam file Excel.', 'records' => array());
        }

        $xml_sheet = simplexml_load_string($sheet_xml);
        if (!$xml_sheet || !isset($xml_sheet->sheetData->row)) {
            return array('success' => false, 'message' => 'Data tidak ditemukan dalam lembar kerja Excel.', 'records' => array());
        }

        $raw_rows = array();
        foreach ($xml_sheet->sheetData->row as $r) {
            $row_data = array();
            $max_col_idx = 0;

            foreach ($r->c as $c) {
                $ref = (string)$c['r']; // e.g. "A4", "B4"
                preg_match('/^([A-Z]+)(\d+)$/', $ref, $matches);
                $col_letters = isset($matches[1]) ? $matches[1] : 'A';
                $col_idx = $this->column_letter_to_index($col_letters);

                $val = '';
                $type = (string)$c['t'];

                if ($type === 's') { // Shared string index
                    $idx = (int)$c->v;
                    $val = isset($shared_strings[$idx]) ? $shared_strings[$idx] : '';
                } elseif ($type === 'inlineStr' && isset($c->is->t)) { // Inline string
                    $val = (string)$c->is->t;
                } elseif (isset($c->v)) {
                    $val = (string)$c->v;
                }

                $row_data[$col_idx] = $val;
                if ($col_idx > $max_col_idx) {
                    $max_col_idx = $col_idx;
                }
            }

            // Normalisasi array berurutan tanpa gap kolom
            $normalized_row = array();
            for ($k = 0; $k <= $max_col_idx; $k++) {
                $normalized_row[$k] = isset($row_data[$k]) ? $row_data[$k] : '';
            }

            $raw_rows[] = $normalized_row;
        }

        return $this->process_raw_rows($raw_rows);
    }

    /**
     * Konversi huruf kolom Excel (A, B, ..., Z, AA) ke indeks 0-based
     */
    protected function column_letter_to_index($letter)
    {
        $letter = strtoupper($letter);
        $len = strlen($letter);
        $num = 0;
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord($letter[$i]) - 64);
        }
        return $num - 1;
    }

    /**
     * Memproses baris mentah: mencari baris header eksplisit, validasi dan pemetaan kolom
     */
    protected function process_raw_rows($raw_rows)
    {
        if (empty($raw_rows)) {
            return array('success' => false, 'message' => 'File tidak memiliki baris data.', 'records' => array());
        }

        $header_index = -1;
        $column_map = array();

        // Cari baris header eksplisit
        foreach ($raw_rows as $idx => $row) {
            $matched = $this->match_header_columns($row);
            if (!empty($matched['id_umkm']) || (!empty($matched['nama_umkm']) && !empty($matched['nilai_investasi']))) {
                $header_index = $idx;
                $column_map = $matched;
                break;
            }
        }

        if ($header_index === -1) {
            return array(
                'success' => false,
                'message' => 'Header kolom dataset tidak ditemukan. Pastikan file memiliki kolom: ID UMKM, UMKM / Nama Usaha, Nilai Investasi/Modal, Lama Usaha.',
                'records' => array()
            );
        }

        $records = array();
        $errors = array();
        $skipped_empty = 0;

        for ($i = $header_index + 1; $i < count($raw_rows); $i++) {
            $row = $raw_rows[$i];
            $row_num = $i + 1;

            $id_umkm = isset($column_map['id_umkm']) && isset($row[$column_map['id_umkm']]) ? trim((string)$row[$column_map['id_umkm']]) : '';
            $nama_umkm = isset($column_map['nama_umkm']) && isset($row[$column_map['nama_umkm']]) ? trim((string)$row[$column_map['nama_umkm']]) : '';

            // Jika baris kosong atau tidak memiliki ID & Nama, anggap baris kosong/footer Excel yang dilewati
            if ($id_umkm === '' && $nama_umkm === '') {
                $skipped_empty++;
                continue;
            }

            // Tangani baris footer/ringkasan yang bukan data (e.g. "JUMLAH", "TOTAL", "SUB TOTAL", "RATA-RATA")
            $nama_upper = strtoupper($nama_umkm);
            $id_upper = strtoupper($id_umkm);
            $footer_keywords = array('JUMLAH', 'TOTAL', 'SUBTOTAL', 'SUB TOTAL', 'GRAND TOTAL', 'RATA-RATA', 'AVERAGE');
            if (in_array($nama_upper, $footer_keywords) || in_array($id_upper, $footer_keywords)) {
                $skipped_empty++;
                continue;
            }

            // Jika ID UMKM kosong tetapi nama ada, buat fallback ID berbasis nomor urut
            if ($id_umkm === '') {
                $id_umkm = sprintf('UMKM-%04d', count($records) + 1);
            }

            $bidang_usaha = isset($column_map['bidang_usaha']) && isset($row[$column_map['bidang_usaha']]) ? trim((string)$row[$column_map['bidang_usaha']]) : null;
            $investasi_raw = isset($column_map['nilai_investasi']) && isset($row[$column_map['nilai_investasi']]) ? $row[$column_map['nilai_investasi']] : 0;
            $nilai_investasi = $this->parse_numeric($investasi_raw);

            $kecamatan = isset($column_map['kecamatan']) && isset($row[$column_map['kecamatan']]) ? trim((string)$row[$column_map['kecamatan']]) : null;
            $desa_kelurahan = isset($column_map['desa_kelurahan']) && isset($row[$column_map['desa_kelurahan']]) ? trim((string)$row[$column_map['desa_kelurahan']]) : null;

            $omzet_raw = isset($column_map['nilai_omzet']) && isset($row[$column_map['nilai_omzet']]) ? $row[$column_map['nilai_omzet']] : 0;
            $laba_raw  = isset($column_map['nilai_laba']) && isset($row[$column_map['nilai_laba']]) ? $row[$column_map['nilai_laba']] : 0;
            $nilai_omzet = $this->parse_numeric($omzet_raw);
            $nilai_laba  = $this->parse_numeric($laba_raw);

            $lama_raw = isset($column_map['lama_usaha']) && isset($row[$column_map['lama_usaha']]) ? $row[$column_map['lama_usaha']] : 0;
            $lama_usaha = $this->parse_numeric($lama_raw);

            $biaya_raw = isset($column_map['biaya_operasional']) && isset($row[$column_map['biaya_operasional']]) ? $row[$column_map['biaya_operasional']] : 0;
            $arus_raw  = isset($column_map['arus_kas_bersih']) && isset($row[$column_map['arus_kas_bersih']]) ? $row[$column_map['arus_kas_bersih']] : 0;
            $biaya_operasional = $this->parse_numeric($biaya_raw);
            $arus_kas_bersih   = $this->parse_numeric($arus_raw);

            $stabilitas_raw = isset($column_map['stabilitas_pendapatan']) && isset($row[$column_map['stabilitas_pendapatan']]) ? trim((string)$row[$column_map['stabilitas_pendapatan']]) : '';
            $stabilitas = $this->normalize_stabilitas($stabilitas_raw);

            $status_raw = isset($column_map['status_usaha']) && isset($row[$column_map['status_usaha']]) ? trim((string)$row[$column_map['status_usaha']]) : '';
            $status_usaha = !empty($status_raw) ? $status_raw : 'Aktif';

            $records[] = array(
                'row_num' => $row_num,
                'id_umkm' => $id_umkm,
                'nama_umkm' => $nama_umkm,
                'bidang_usaha' => $bidang_usaha,
                'nilai_investasi' => $nilai_investasi,
                'kecamatan' => $kecamatan,
                'desa_kelurahan' => $desa_kelurahan,
                'nilai_omzet' => $nilai_omzet,
                'nilai_laba' => $nilai_laba,
                'lama_usaha' => $lama_usaha,
                'biaya_operasional' => $biaya_operasional,
                'arus_kas_bersih' => $arus_kas_bersih,
                'stabilitas_pendapatan' => $stabilitas,
                'status_usaha' => $status_usaha
            );
        }

        return array(
            'success' => true,
            'header_row' => $header_index + 1,
            'column_map' => $column_map,
            'total_parsed' => count($records),
            'skipped_empty' => $skipped_empty,
            'records' => $records,
            'errors' => $errors
        );
    }

    /**
     * Pencocokan Nama Header Kolom
     */
    protected function match_header_columns($row)
    {
        $map = array();

        foreach ($row as $col_idx => $val) {
            $norm = strtolower(trim((string)$val));
            $norm = preg_replace('/\s+/', ' ', $norm);

            if ($norm === 'id umkm' || $norm === 'id_umkm' || $norm === 'id') {
                $map['id_umkm'] = $col_idx;
            } elseif ($norm === 'umkm' || $norm === 'nama umkm' || $norm === 'nama_umkm' || $norm === 'nama pemilik' || $norm === 'nama usaha') {
                $map['nama_umkm'] = $col_idx;
            } elseif ($norm === 'bidang usaha' || $norm === 'bidang_usaha' || $norm === 'jenis usaha') {
                $map['bidang_usaha'] = $col_idx;
            } elseif (strpos($norm, 'investasi') !== false || strpos($norm, 'modal') !== false) {
                $map['nilai_investasi'] = $col_idx;
            } elseif ($norm === 'kec' || $norm === 'kecamatan') {
                $map['kecamatan'] = $col_idx;
            } elseif ($norm === 'desa/kel' || $norm === 'desa' || $norm === 'kelurahan' || $norm === 'desa_kelurahan') {
                $map['desa_kelurahan'] = $col_idx;
            } elseif (strpos($norm, 'omzet') !== false) {
                $map['nilai_omzet'] = $col_idx;
            } elseif (strpos($norm, 'laba') !== false) {
                $map['nilai_laba'] = $col_idx;
            } elseif (strpos($norm, 'lama usaha') !== false) {
                $map['lama_usaha'] = $col_idx;
            } elseif (strpos($norm, 'biaya') !== false || strpos($norm, 'operasional') !== false) {
                $map['biaya_operasional'] = $col_idx;
            } elseif (strpos($norm, 'arus kas') !== false) {
                $map['arus_kas_bersih'] = $col_idx;
            } elseif (strpos($norm, 'stabilitas') !== false) {
                $map['stabilitas_pendapatan'] = $col_idx;
            } elseif (strpos($norm, 'status') !== false) {
                $map['status_usaha'] = $col_idx;
            }
        }

        return $map;
    }

    /**
     * Konversi format rupiah atau angka string ke float/decimal bersih
     */
    protected function parse_numeric($val)
    {
        if (is_numeric($val)) {
            return (float)$val;
        }

        $str = trim((string)$val);
        if ($str === '') {
            return 0.0;
        }

        // Hapus simbol mata uang dan spasi
        $str = preg_replace('/[Rp\s]/i', '', $str);

        // Jika terdapat koma dan titik (e.g. 15.000.000,00 atau 15,000,000.00)
        if (strpos($str, '.') !== false && strpos($str, ',') !== false) {
            if (strrpos($str, ',') > strrpos($str, '.')) {
                // Format Indonesia: 15.000.000,00
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                // Format US: 15,000,000.00
                $str = str_replace(',', '', $str);
            }
        } else {
            // Jika hanya titik banyak: 15.000.000
            if (substr_count($str, '.') > 1) {
                $str = str_replace('.', '', $str);
            } elseif (substr_count($str, ',') > 1) {
                $str = str_replace(',', '', $str);
            }
        }

        $clean = preg_replace('/[^0-9\.\-]/', '', $str);
        return is_numeric($clean) ? (float)$clean : 0.0;
    }

    /**
     * Normalisasi nilai stabilitas pendapatan
     */
    protected function normalize_stabilitas($val)
    {
        $v = strtolower(trim((string)$val));
        if (strpos($v, 'fluk') !== false) {
            return 'Fluktuatif';
        } elseif (strpos($v, 'cukup') !== false) {
            return 'Cukup stabil';
        } elseif (strpos($v, 'stabil') !== false) {
            return 'Stabil';
        }
        return 'Cukup stabil'; // Default fallback
    }
}
