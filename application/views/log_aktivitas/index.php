<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row mb-3 align-items-center">
  <div class="col-12">
    <h4 class="font-weight-bold text-dark mb-1">
      <i class="fas fa-history mr-2 text-primary"></i> Log Aktivitas & Jejak Audit Keamanan
    </h4>
    <p class="text-muted small mb-0">Catatan aktivitas penting dalam sistem untuk keperluan kepatuhan, transparansi, dan audit sistem.</p>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th width="40">ID</th>
            <th>Waktu</th>
            <th>Aksi</th>
            <th>Pengguna</th>
            <th>Deskripsi Aktivitas</th>
            <th>IP Address</th>
            <th>User Agent</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $l): ?>
              <tr>
                <td><strong>#<?= $l['id'] ?></strong></td>
                <td><small class="text-muted"><?= date('d/m/Y H:i:s', strtotime($l['created_at'])) ?></small></td>
                <td><span class="badge badge-light border font-weight-bold"><?= html_escape($l['action']) ?></span></td>
                <td>
                  <?php if (!empty($l['user_name'])): ?>
                    <strong class="text-dark"><?= html_escape($l['user_name']) ?></strong>
                    <div class="text-muted text-xs">@<?= html_escape($l['username']) ?></div>
                  <?php else: ?>
                    <span class="text-muted">Sistem</span>
                  <?php endif; ?>
                </td>
                <td><small class="text-dark"><?= html_escape($l['description']) ?></small></td>
                <td><code><?= html_escape($l['ip_address']) ?></code></td>
                <td><span class="text-muted text-xs text-truncate d-inline-block" style="max-width: 180px;" title="<?= html_escape($l['user_agent']) ?>"><?= html_escape($l['user_agent']) ?></span></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat aktivitas yang tercatat.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($total_pages > 1): ?>
      <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top">
        <div class="text-muted small mb-2 mb-md-0">
          Halaman <strong><?= $current_page ?></strong> dari <strong><?= $total_pages ?></strong> (Total <?= number_format($total_rows) ?> log)
        </div>
        <nav aria-label="Page navigation">
          <ul class="pagination pagination-sm m-0">
            <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= base_url('log_aktivitas?page=' . ($current_page - 1)) ?>">Sebelumnya</a>
            </li>
            <?php
              $start_p = max(1, $current_page - 2);
              $end_p = min($total_pages, $current_page + 2);
              for ($p = $start_p; $p <= $end_p; $p++):
            ?>
              <li class="page-item <?= ($current_page == $p) ? 'active' : '' ?>">
                <a class="page-link" href="<?= base_url('log_aktivitas?page=' . $p) ?>"><?= $p ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= base_url('log_aktivitas?page=' . ($current_page + 1)) ?>">Selanjutnya</a>
            </li>
          </ul>
        </nav>
      </div>
    <?php endif; ?>
  </div>
</div>
